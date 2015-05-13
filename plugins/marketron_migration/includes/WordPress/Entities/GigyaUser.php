<?php

namespace WordPress\Entities;

class GigyaUser extends BaseEntity {

	public $gigya_users            = array();
	public $member_ids             = array();
	public $can_import_all_members = true;

	function add( &$gigya_user ) {
		if ( empty( $gigya_user['id'] ) ) {
			var_dump( $gigya_user );
			\WP_CLI::error( 'invalid id error' );
		}
		$id = $gigya_user['id'];

		if ( ! $this->user_exists( $id ) ) {
			$this->gigya_users[ $id ] = $gigya_user;
		} else {
			\WP_CLI::log( 'Skipped Duplicate User: ' . $id );
		}

		return $gigya_user;
	}

	function user_exists( $member_id ) {
		return array_key_exists( $member_id, $this->gigya_users );
	}

	function get_by_id( $member_id ) {
		return $this->gigya_users[ $member_id ];
	}

	function get_full_name( $member_id ) {
		$gigya_user = $this->get_by_id( $member_id );
		return $gigya_user['first_name'] . ' ' . $gigya_user['last_name'];
	}

	function get_email( $member_id ) {
		$gigya_user = $this->get_by_id( $member_id );
		return $gigya_user['email'];
	}

	function export() {
		if ( count( $this->gigya_users ) === 0 ) {
			\WP_CLI::warning( 'No Gigya users to export' );
			return;
		}

		$this->load_member_ids();

		$export_file  = $this->container->config->get_gigya_profile_export_file();
		$gigya_export = $this->export_to_gigya();
		$total        = $gigya_export['settings']['totalRecords'];

		file_put_contents( $export_file, json_encode( $gigya_export, JSON_PRETTY_PRINT ) );
		\WP_CLI::success( "Saved $total Gigya Profiles" );

		$export_file          = $this->container->config->get_gigya_account_export_file();
		$filtered_gigya_users = $this->export_gigya_users();
		file_put_contents( $export_file, json_encode( $filtered_gigya_users, JSON_PRETTY_PRINT ) );

		\WP_CLI::success( "Saved $total Gigya Accounts" );
	}

	function export_gigya_users() {
		$gigya_users         = array();
		$total               = count( $this->gigya_users );
		$msg                 = 'Saving Gigya Accounts ...';
		$progress_bar        = new \WordPress\Utils\ProgressBar( $msg, $total );

		foreach ( $this->gigya_users as $user_id => $gigya_user ) {
			if ( $this->can_import_gigya_user( $gigya_user ) ) {
				$gigya_users[] = $gigya_user;
			}

			$progress_bar->tick();
		}

		$progress_bar->finish();

		return $gigya_users;
	}

	function export_actions() {
		\WP_CLI::log( 'Loading Gigya Accounts File ...' );
		$export_file = $this->container->config->get_gigya_account_export_file();
		$json        = file_get_contents( $export_file );
		$accounts    = json_decode( $json, true );

		\WP_CLI::success( 'Loaded Gigya Accounts File' );

		$export_file = $this->container->config->get_gigya_action_export_file();
		$actions     = $this->export_actions_to_gigya( $accounts );
		file_put_contents( $export_file, json_encode( $actions, JSON_PRETTY_PRINT ) );

		$total = count( $actions );
		\WP_CLI::success( "Saved $total Gigya Actions" );
	}

	function export_actions_to_gigya( &$accounts ) {
		$actions = array();
		$total   = count( $accounts );
		$msg     = 'Saving Gigya Actions ...';
		$progress_bar = new \WordPress\Utils\ProgressBar( $msg, $total );
		$max = 100000000;
		$index = 0;

		foreach ( $accounts as $account ) {
			$account_actions = $this->export_user_actions( $account );
			$actions         = array_merge( $actions, $account_actions );
			$progress_bar->tick();
			if ( $index++ > $max ) {
				break;
			}
		}

		$progress_bar->finish();

		return $actions;
	}

	function export_user_actions( &$account ) {
		$actions = $this->export_survey_actions( $account );
		$actions = array_merge( $actions, $this->export_contest_actions( $account ) );

		return $actions;
	}

	function export_contest_actions( &$account ) {
		return $this->export_entry_actions(
			$account, 'action:contest', 'contest_entries', 'contest_id'
		);
	}

	function export_survey_actions( &$account ) {
		return $this->export_entry_actions(
			$account, 'action:survey', 'survey_entries', 'survey_id'
		);
	}

	function export_entry_actions( &$gigya_user, $action_type, $store_key, $store_id ) {
		$member_id  = $gigya_user['id'];

		if ( array_key_exists( $store_key, $gigya_user ) ) {
			$actions = array();
			$entries = $gigya_user[ $store_key ];
			//$total   = count( $entries );
			//$msg     = "Exporting $total $action_type entries";
			//$progress_bar = new \WordPress\Utils\ProgressBar( $msg, $total );

			foreach ( $entries as $entry ) {
				$action_item = array(
					'actionType' => $action_type,
					'actionID' => strval( $entry[ $store_id ] ),
					'actionData' => $this->export_answers_to_action_data( $entry['answers'] ),
				);

				$actions[] = array(
					'UID' => $member_id,
					'data' => array(
						'actions' => array( $action_item )
					)
				);

				//$progress_bar->tick();
			}

			//$progress_bar->finish();

			return $actions;
		} else {
			return array();
		}
	}

	function export_answers_to_action_data( &$answers ) {
		$action_data = array();

		if ( empty( $answers ) ) {
			return $action_data;
		}

		foreach ( $answers as $key => $value ) {
			$action_data_item = array( 'name' => $key );

			if ( is_array( $value ) ) {
				$action_data_item['value_list'] = $value;
			} else {
				$action_data_item['value_t'] = $value;
			}

			$action_data[] = $action_data_item;
		}

		return $action_data;
	}

	function export_to_gigya() {
		$gigya_export = array();
		$settings = $this->export_settings();
		$accounts = $this->export_accounts();

		$settings['totalRecords'] = count( $accounts );
		$gigya_export['settings'] = $settings;
		$gigya_export['accounts'] = $accounts;

		return $gigya_export;
	}

	function export_settings() {
		$config = $this->container->config;

		$settings = array();
		$settings['importFormat'] = 'gigya-users-import';
		$settings['apiKey'] = $config->get_config_option( 'gigya', 'api_key' );
		$settings['finalizeRegistration'] = true;
		$settings['skipVerification'] = true;

		return $settings;
	}

	function export_accounts() {
		$accounts            = array();
		$total               = count( $this->gigya_users );
		$msg                 = 'Saving Gigya Profiles';
		$progress_bar        = new \WordPress\Utils\ProgressBar( $msg, $total );
		$max_contest_entries = 0;
		$max_survey_entries  = 0;

		foreach ( $this->gigya_users as $user_id => $gigya_user ) {
			if ( $this->can_import_gigya_user( $gigya_user ) ) {
				$account    = $this->export_gigya_user( $gigya_user );
				if ( count( $account['data']['contest_list'] ) > $max_contest_entries ) {
					$max_contest_entries = count( $account['data']['contest_list'] );
				}
				if ( count( $account['data']['survey_list'] ) > $max_survey_entries ) {
					$max_survey_entries = count( $account['data']['survey_list'] );
				}
				$accounts[] = $account;
			}

			$progress_bar->tick();
		}

		\WP_CLI::log( "Max Contest Entries: $max_contest_entries, Max Survey Entries: $max_survey_entries" );
		$progress_bar->finish();

		return $accounts;
	}

	function export_gigya_user( &$gigya_user ) {
		$id = $gigya_user['id'];

		$account                           = array();
		$account['UID']                    = $id;
		$account['compoundHashedPassword'] = md5( $id );
		$account['email']                  = $gigya_user['email'];
		$account['profile']                = $this->export_gigya_user_profile( $gigya_user );
		$account['data']                   = $this->export_gigya_user_data( $gigya_user );

		if ( ! empty( $gigya_user['facebook_id'] ) ) {
			$account['identities'] = $this->export_identities( $gigya_user );
		}

		return $account;
	}

	function export_gigya_user_profile( &$gigya_user ) {
		$profile = array();
		$profile['email'] = $gigya_user['email'];
		$profile['firstName'] = $gigya_user['first_name'];
		$profile['lastName'] = $gigya_user['last_name'];
		$profile['nickname'] = $gigya_user['nick_name'];

		if ( ! empty( $gigya_user['birthday'] ) ) {
			$birthday              = $gigya_user['birthday'];
			$profile['birthYear']  = $birthday['year'];
			$profile['birthMonth'] = $birthday['month'];
			$profile['birthDay']   = $birthday['day'];
		}

		$profile['gender']  = $this->export_gender( $gigya_user['gender'] );
		$profile['city']    = $gigya_user['city'];
		$profile['state']   = $gigya_user['state'];
		$profile['country'] = $this->export_country( $gigya_user['country'] );
		$profile['address'] = $gigya_user['address'];
		$profile['zip']     = $gigya_user['zip'];

		return $profile;
	}

	function export_gender( $gender ) {
		$gender = strtolower( $gender );

		if ( $gender === 'male' ) {
			return 'm';
		} else {
			return 'f';
		}
	}

	function export_country( $country ) {
		return empty( $country ) ? 'United States' : $country;
	}

	function export_gigya_user_data( &$gigya_user ) {
		$id = $gigya_user['id'];

		$data                      = array();
		$data['marketronStatus']   = $gigya_user['marketron_status'];
		$data['marketronSource']   = $gigya_user['marketron_source'];
		$data['marketronMemberID'] = $gigya_user['marketron_member_id'];

		$data['registered']          = $gigya_user['registered'];
		$data['registeredTimestamp'] = strtotime( $gigya_user['registered'] );

		$data['lastUpdated']          = $gigya_user['last_updated'];
		$data['lastUpdatedTimestamp'] = strtotime( $gigya_user['last_updated'] );

		$survey_entity         = $this->get_entity( 'survey' );
		$data['contest_list']  = $this->get_user_contest_entries_list( $id );
		$data['survey_list']   = $this->get_user_survey_entries_list( $id );
		$data['contest_count'] = count( $data['contest_list'] );
		$data['survey_count']  = count( $data['survey_list'] );

		$this->export_newsletters( $gigya_user, $data );

		return $data;
	}

	function export_newsletters( &$gigya_user, &$data ) {
		$config                = $this->container->config;
		$user_newsletter_names = $gigya_user['newsletters'];
		$user_newsletter_ids   = array();

		foreach ( $user_newsletter_names as $marketron_name ) {
			if ( $config->has_newsletter( $marketron_name ) ) {
				$newsletter            = $config->get_newsletter( $marketron_name );
				$user_newsletter_ids[] = strval( $newsletter['emma_group_id'] );
				$data[ $newsletter['gigya_field_key'] ] = true;
			}
		}

		$data['subscribedToList'] = $user_newsletter_ids;
	}

	function export_identities( &$gigya_user ) {
		$identities = array();
		$identities[] = array(
			'provider'    => 'facebook',
			'providerUID' => $gigya_user['facebook_id'],
		);

		return $identities;
	}

	function load_member_ids() {
		$member_ids_file = $this->container->config->get_member_ids_file();
		$file            = fopen( $member_ids_file, 'r' );
		$member_ids      = array();

		if ( $file !== false ) {
			$line = fgets( $file );
			if ( trim( $line ) === '*' ) {
				$this->can_import_all_members = true;
				return;
			}

			while ( $line !== false ) {
				$line = trim( $line );
				$line = rtrim( $line, ',' );

				if ( is_numeric( $line ) ) {
					$member_id                = $line;
					$member_ids[ $member_id ] = true;
				}

				$line = fgets( $file );
			}

			$this->can_import_all_members = false;
		} else {
			$this->can_import_all_members = true;
		}

		$this->member_ids = $member_ids;
	}

	function can_import_member( $member_id ) {
		if ( $this->can_import_all_members ) {
			return true;
		} else {
			return array_key_exists( $member_id, $this->member_ids );
		}
	}

	function can_import_gigya_user( &$gigya_user ) {
		$id = $gigya_user['id'];

		if ( $this->can_import_member( $id ) ) {
			$email = $gigya_user['email'];

			if ( $this->is_valid_email( $email ) ) {
				return true;
			} else {
				\WP_CLI::warning( "Ignored Member($id) with Invalid Email($email)" . $email );
				return false;
			}
		} else {
			return false;
		}
	}

	function is_valid_email( $email ) {
		return filter_var( $email, FILTER_VALIDATE_EMAIL );
	}

	function add_user_if_absent( $user_id ) {
		if ( ! $this->user_exists( $user_id ) ) {
			$fields = array(
				'id' => $user_id,
			);

			$this->add( $fields );
		}

	}

	function add_contest_entry( $user_id, $contest_entry ) {
		$this->add_user_if_absent( $user_id );

		if ( ! array_key_exists( 'contest_entries', $this->gigya_users[ $user_id ] ) ) {
			$this->gigya_users[ $user_id ]['contest_entries'] = array();
		}

		$this->gigya_users[ $user_id ]['contest_entries'][] = $contest_entry;
	}

	function add_survey_entry( $user_id, $survey_entry ) {
		$this->add_user_if_absent( $user_id );

		if ( ! array_key_exists( 'survey_entries', $this->gigya_users[ $user_id ] ) ) {
			$this->gigya_users[ $user_id ]['survey_entries'] = array();
		}

		$this->gigya_users[ $user_id ]['survey_entries'][] = $survey_entry;
	}

	function get_user_entries_list( $user_id, $store_key, $store_id ) {
		$ids = array();

		if ( ! empty( $this->gigya_users[ $user_id ][ $store_key ] ) ) {
			foreach ( $this->gigya_users[ $user_id ][ $store_key ] as $survey_entry ) {
				$ids[] = strval( $survey_entry[ $store_id ] );
			}
		}

		return $ids;
	}

	function get_user_contest_entries_list( $user_id ) {
		return $this->get_user_entries_list( $user_id, 'contest_entries', 'contest_id' );
	}

	function get_user_survey_entries_list( $user_id ) {
		return $this->get_user_entries_list( $user_id, 'survey_entries', 'survey_id' );
	}

	function get_user_survey_answers( $user_id, $user_survey_id ) {
		if ( $this->user_exists( $user_id ) ) {

			if ( ! empty( $this->gigya_users[ $user_id ][ 'survey_entries' ] ) ) {
				$user_survey_entries = $this->gigya_users[ $user_id ]['survey_entries'];

				foreach ( $user_survey_entries as $user_survey_entry ) {
					if ( ! empty( $user_survey_entry['user_survey_id'] ) ) {
						if ( $user_survey_entry['user_survey_id'] === $user_survey_id ) {
							if ( ! empty( $user_survey_entry['answers'] ) ) {
								return $user_survey_entry['answers'];
							}
						}
					}
				}
			}

			return array();
		} else {
			return array();
		}
	}

}
