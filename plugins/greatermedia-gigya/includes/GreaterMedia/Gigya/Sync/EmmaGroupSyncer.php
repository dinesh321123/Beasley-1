<?php

namespace GreaterMedia\Gigya\Sync;

use GreaterMedia\Gigya\GigyaRequest;
use GreaterMedia\MyEmma\EmmaAPI;

class EmmaGroupSyncer {

	public $gigya_user_id;
	public $emma_user_id;
	public $emma_api;

	public $gigya_account;
	public $new_gigya_account_data;
	public $emma_account;

	public $available_groups;
	public $available_group_ids;
	public $user_email;
	public $user_group_ids;

	function __construct( $gigya_user_id ) {
		$this->gigya_user_id = $gigya_user_id;
	}

	function sync() {
		$emma_user_id        = $this->get_emma_user_id();
		$available_group_ids = $this->get_available_group_ids();
		$published_group_ids = $this->get_published_user_group_ids();
		$user_group_ids      = $this->get_user_group_ids();

		$this->remove_user_from_groups( $emma_user_id, $published_group_ids );
		$this->add_user_to_groups( $emma_user_id, $user_group_ids );

		error_log( sprintf(
			'[Emma Debug] Syncing Emma user: %s. Published Groups: %s, User Groups: %s',
			$emma_user_id,
			print_r( $published_group_ids, true ),
			print_r( $user_group_ids, true )
		), 1, 'elliottstocks@get10up.com' );


		$this->update_gigya_account_data();
	}

	function get_emma_api() {
		$this->emma_api = new EmmaAPI();
		return $this->emma_api;
	}

	function get_gigya_user_id() {
		return $this->gigya_user_id;
	}

	function get_available_groups() {
		if ( is_null( $this->available_groups ) ) {
			$this->available_groups = get_option( 'emma_groups' );
			$this->available_groups = json_decode( $this->available_groups, true );
		}

		return $this->available_groups;
	}

	function get_available_group_ids() {
		if ( is_null( $this->available_group_ids ) ) {
			$available_groups          = $this->get_available_groups();
			$this->available_group_ids = array_column(
				$available_groups, 'group_id'
			);
		}

		return $this->available_group_ids;
	}

	function get_gigya_account() {
		if ( is_null( $this->gigya_account ) ) {
			$request = new GigyaRequest( null, null, 'accounts.getAccountInfo' );
			$request->setParam( 'UID', $this->get_gigya_user_id() );
			$response = $request->send();

			if ( $response->getErrorCode() === 0 ) {
				$response_text       = $response->getResponseText();
				$this->gigya_account = json_decode( $response_text, true );
			} else {
				throw new \Exception(
					'Failed to get Gigya Account for - ' . $this->get_gigya_user_id()
				);
			}
		}

		return $this->gigya_account;
	}

	function get_user_email() {
		if ( is_null( $this->user_email ) ) {
			$gigya_account    = $this->get_gigya_account();
			$this->user_email = $gigya_account['profile']['email'];
		}

		return $this->user_email;
	}

	function get_emma_account() {
		if ( is_null( $this->emma_account ) ) {
			$api                = $this->get_emma_api();
			$email              = $this->get_user_email();

			try {
				$response = $api->membersListByEmail( $email );
				$this->emma_account = json_decode( $response, true );
			} catch ( \Emma_Invalid_Response_Exception $e ) {
				$emma_account       = $this->add_emma_account( $email );
				$response           = $api->membersListByEmail( $email );
				$this->emma_account = json_decode( $response, true );
			}
		}

		return $this->emma_account;
	}

	function add_emma_account( $email ) {
		$api           = $this->get_emma_api();
		$gigya_account = $this->get_gigya_account();
		$member = array(
			'email'             => $email,
			'fields'            => array(
				'first_name'    => $gigya_account['profile']['firstName'],
				'last_name'     => $gigya_account['profile']['lastName'],
				'birthday'      => $this->get_birth_day( $gigya_account['profile'] ),
				'gigya_user_id' => $gigya_account['UID'],
			)
		);

		$response = $api->membersAddSingle( $member );
		$json     = json_decode( $response, true );

		return $json['member_id'];
	}

	function get_birth_day( $profile ) {
		return $profile['birthMonth'] . '/' . $profile['birthDay'] . '/' . $profile['birthYear'];
	}

	function get_emma_user_id() {
		if ( is_null( $this->emma_user_id ) ) {
			$emma_account       = $this->get_emma_account();
			$this->emma_user_id = $emma_account['member_id'];
		}

		return $this->emma_user_id;
	}

	function get_user_group_ids() {
		if ( is_null( $this->user_group_ids ) ) {
			$available_groups   = $this->get_available_groups();
			$gigya_account      = $this->get_gigya_account();
			$gigya_account_data = $gigya_account['data'];
			$this->user_group_ids  = array();

			foreach ( $available_groups as $group ) {
				$field_key = $group['field_key'];
				$group_id  = $group['group_id'];
				$has_group = $this->data_has_group(
					$gigya_account_data, $field_key
				);

				if ( $has_group ) {
					$this->user_group_ids[] = $group_id;
				}
			}
		}

		return $this->user_group_ids;
	}

	function data_has_group( $data, $key ) {
		return array_key_exists( $key, $data ) && $data[ $key ];
	}

	function get_published_user_group_ids() {
		$gigya_account     = $this->get_gigya_account();
		$has_subscriptions = $this->data_has_group( $gigya_account['data'], 'subscribedToList' );

		if ( $has_subscriptions ) {
			return $gigya_account['data']['subscribedToList'];
		} else {
			return [];
		}
	}

	function get_new_gigya_account_data() {
		if ( is_null( $this->new_gigya_account_data ) ) {
			$gigya_account                          = $this->get_gigya_account();
			$gigya_account_data                     = $gigya_account['data'];
			$gigya_account_data['subscribedToList'] = $this->get_user_group_ids();

			$this->new_gigya_account_data = $gigya_account_data;
		}

		return $this->new_gigya_account_data;
	}

	function update_gigya_account_data() {
		$account_data = $this->get_new_gigya_account_data();
		$account_data = json_encode( $account_data );

		$request = new GigyaRequest( null, null, 'accounts.setAccountInfo' );
		$request->setParam( 'UID', $this->get_gigya_user_id() );
		$request->setParam( 'data', $account_data );

		$response = $request->send();

		if ( $response->getErrorCode() === 0 ) {
			return true;
		} else {
			throw new \Exception(
				'Failed to update gigya account data - ' . $response->getErrorMessage()
			);
		}
	}

	function remove_user_from_groups( $emma_user_id, $group_ids ) {
		if ( count( $group_ids ) === 0 ) {
			return true;
		}

		$emma_user_id = intval( $emma_user_id );
		$params       = array(
			'member_ids' => array( $emma_user_id ),
			'group_ids'  => $this->to_int_ids( $group_ids )
		);

		$api      = $this->get_emma_api();
		$response = $api->membersRemoveSingleFromGroups( $emma_user_id, $params );

		return true;
	}

	function add_user_to_groups( $emma_user_id, $group_ids ) {
		if ( count( $group_ids ) === 0 ) {
			return true;
		}

		$emma_user_id = intval( $emma_user_id );
		$gigya_account = $this->get_gigya_account();
		$member = array(
			'email'             => $gigya_account['profile']['email'],
			'fields'            => array(
				'first_name'    => $gigya_account['profile']['firstName'],
				'last_name'     => $gigya_account['profile']['lastName'],
				'birthday'      => $this->get_birth_day( $gigya_account['profile'] ),
				'gigya_user_id' => $gigya_account['UID'],
			)
		);

		$group_ids = $this->to_int_ids( $group_ids );

		$signup_method = false;

		if ( array_key_exists( 'optout', $gigya_account['data'] ) && $gigya_account['data']['optout'] === true ) {
			// if optout, signup has to happen via Emma Signup email
			$this->signup( $gigya_account, $group_ids );
		} else {
			// user only changed their subscriptions
			$params = array(
				'members'   => array( $member ),
				'group_ids' => $group_ids,
			);

			$api      = $this->get_emma_api();
			$response = $api->membersBatchAdd( $params );

			$signup_method = true;
		}

		error_log( sprintf(
			'[Emma Debug] Emma User ID: %s, Member Array %s, Group IDs %s, Used signup method? %s',
			$emma_user_id,
			print_r( $member, true ),
			print_r( $group_ids, true ),
			$signup_method
		), 1, 'elliottstocks@get10up.com' );

		return true;
	}

	function signup( $gigya_account, $group_ids ) {
		$member = array(
			'email'             => $gigya_account['profile']['email'],
			'fields'            => array(
				'first_name'    => $gigya_account['profile']['firstName'],
				'last_name'     => $gigya_account['profile']['lastName'],
				'birthday'      => $this->get_birth_day( $gigya_account['profile'] ),
				'gigya_user_id' => $gigya_account['UID'],
			),
			'group_ids' => $group_ids,
		);

		$api      = $this->get_emma_api();
		$response = $api->membersSignup( $member );

		return true;
	}

	function to_int_ids( $list ) {
		$int_list = array();
		foreach ( $list as $item ) {
			$int_list[] = intval( $item );
		}

		return $int_list;
	}

}
