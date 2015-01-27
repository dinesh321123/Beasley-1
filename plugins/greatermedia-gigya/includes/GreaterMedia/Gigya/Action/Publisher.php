<?php

namespace GreaterMedia\Gigya\Action;

use GreaterMedia\Gigya\Sync\Task;
use GreaterMedia\Gigya\GigyaRequest;
use GreaterMedia\Gigya\MemberQuery;

class Publisher extends Task {

	public $store_name = 'actions';
	public $guest_uid  = '37F5E08F-74D3-40FC-8F4B-296AD29DACBB';
	public $member_query;
	public $message_types = array(
		'execute',
		'retry',
		'abort',
		'error',
	);

	public $counter_actions = array(
		'comment',
		'contest',
		'survey',
		'social_share',

		'email_message_open',
		'email_message_click',
	);

	public $list_actions = array(
		'contest',
		'survey',
		'social_share',

		'email_message_open',
		'email_message_click',
	);

	function get_task_name() {
		return 'action_publisher';
	}

	function run() {
		$actions = $this->params['actions'];
		$this->publish_actions( $actions );
	}

	function publish_actions( $actions ) {
		foreach ( $actions as $action ) {
			$this->publish( $action );
		}
	}

	function publish( $action ) {
		$request = new GigyaRequest( null, null, 'ds.store' );
		$data    = $this->prepare_action_for_storage( $action );
		$uid     = $this->get_action_uid();

		$request->setParam( 'type', $this->store_name );
		$request->setParam( 'data', $data );
		$request->setParam( 'UID', $uid );
		$request->setParam( 'oid', 'auto' );

		$response      = $request->send();
		$response_text = $response->getResponseText();

		if ( $response->getErrorCode() === 0 ) {
			$json = json_decode( $response_text, true );

			if ( is_array( $json ) ) {
				$this->update_profile_data( $uid, $action );
				return $json;
			} else {
				throw new \Exception(
					"ActionPublisher: Failed to decode response json - {$response_text}"
				);
			}
		} else {
			$error_message = $this->error_message_for( $response );

			throw new \Exception(
				"ActionPublisher: Store Failed - {$data} - " . $error_message
			);
		}

	}

	function update_profile_data( $uid, $action ) {
		$data            = get_gigya_user_profile_data( $uid );
		$action_sub_type = $this->action_subtype_for( $action['actionType'] );
		$counter_name    = $action_sub_type . '_count';
		$list_name       = $action_sub_type . '_list';
		$action_id       = strval( $action['actionID'] );

		if ( $this->is_counter_action( $action_sub_type ) ) {
			if ( array_key_exists( $counter_name, $data ) ) {
				$counter = intval( $data[ $counter_name ] );
				$counter++;
			} else {
				$counter = 1;
			}

			$data[ $counter_name ] = $counter;
		}

		if ( $this->is_list_action( $action_sub_type ) ) {
			if ( array_key_exists( $list_name, $data ) ) {
				$list = $data[ $list_name ];

				if ( ! in_array( $action_id, $list ) ) {
					$list[] = $action_id;
				}
			} else {
				$list = array( $action_id );
			}

			$data[ $list_name ] = $list;
		}

		set_gigya_user_profile_data( $uid, $data );
	}

	function is_counter_action( $counter_name ) {
		return in_array( $counter_name, $this->counter_actions );
	}

	function is_list_action( $counter_name ) {
		return in_array( $counter_name, $this->list_actions );
	}

	function action_subtype_for( $action_type ) {
		$parts   = explode( ':', $action_type );
		$subtype = $parts[1];

		return $subtype;
	}

	function prepare_action_for_storage( $action ) {
		$total = count( $action['actionData'] );
		for ( $i = 0; $i < $total; $i++ ) {
			$item       = $action['actionData'][ $i ];
			$field_name = $this->field_name_for( 'value', $item['value'] );

			$action['actionData'][ $i ][ $field_name ] = $item['value'];
			unset( $action['actionData'][ $i ]['value'] );
		}

		// each action gets a timestamp
		$action['actionData'][] = array(
			'name'    => 'timestamp',
			'value_i' => time()
		);

		$data = array( 'actions' => array( $action ) );
		$json = json_encode( $data );

		return $json;
	}

	function error_message_for( $response ) {
		$response_text = $response->getResponseText();
		$json          = json_decode( $response_text, true );

		if ( json_last_error() === JSON_ERROR_NONE ) {
			if ( array_key_exists( 'errorDetails', $json ) ) {
				return $json['errorDetails'];
			} else {
				return $response->getErrorMessage();
			}
		} else {
			return 'Gigya API returned invalid JSON - ' . $response_text;
		}
	}

	function get_user_id() {
		return $this->params['user_id'];
	}

	function get_action_uid() {
		$user_id = $this->get_user_id();

		if ( $user_id === 'guest' ) {
			$user_id = $this->get_guest_uid();
		}

		return $user_id;
	}

	// This corresponds to oid on a DS action
	function get_guest_uid() {
		return $this->guest_uid;
	}

	// KLUDGE: this doesn't belong here..
	function get_member_query() {
		if ( is_null( $this->member_query ) ) {
			$this->member_query = new MemberQuery( 1, '{}' );
		}

		return $this->member_query;
	}

	function field_name_for( $field, $value ) {
		$value_type = $this->value_type_for( $value );
		return $field . $this->suffix_for( $value_type );
	}

	function suffix_for( $value_type ) {
		return $this->get_member_query()->suffix_for( $value_type );
	}

	function value_type_for( $value ) {
		$type = gettype( $value );

		if ( $type === 'double' ) {
			$type = 'float';
		}

		return $type;
	}



}
