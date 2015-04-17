<?php

namespace GreaterMedia\Import\Repair;

use GreaterMedia\Gigya\Sync\QueryPaginator;
use GreaterMedia\Gigya\GigyaRequest;
use WordPress\Utils\ProgressBar;

class OptinGroupsRepairer {

	public $groups;
	public $errors    = array();

	public $page_size      = 1000;
	public $delay_on_error = 0.5; // seconds
	public $max_attempts   = 5;

	function repair( $errors_file ) {
		$total        = $this->count_profiles();
		$msg          = "Repairing Optin Groups for $total users";
		$progress_bar = new ProgressBar( $msg, $total );
		$query        = $this->get_user_query();;
		$paginator    = new QueryPaginator( 'profile', $this->page_size );
		$cursor       = 0;
		$has_next     = true;

		while ( $has_next ) {
			$query_result = $paginator->fetch( $query, $cursor );
			$user_ids     = $query_result['results'];
			$user_ids     = array_column( $user_ids, 'UID' );
			$cursor       = $query_result['cursor'];
			$has_next     = $query_result['has_next'];

			foreach ( $user_ids as $user_id ) {
				$this->repair_profile( $user_id );
				$progress_bar->tick();
			}
		}

		$progress_bar->finish();
		$this->save_errors( $errors_file );

		return $total;
	}

	function save_errors( $errors_file ) {
		if ( count( $this->errors ) > 0 ) {
			\WP_CLI::warning( 'Saving ' . count( $this->errors ) . ' Errors ...' );
			$errors = json_encode( $this->errors, JSON_PRETTY_PRINT );
			file_put_contents( $errors_file, $errors );
			\WP_CLI::success( 'Saved Errors to ' . $errors_file );
		} else {
			\WP_CLI::success( 'No Errors Occurred.' );
		}
	}

	function repair_profile( $gigya_id ) {
		$profile_data = $this->get_profile_data( $gigya_id );

		if ( $profile_data !== false && ! empty( $profile_data['subscribedToList'] ) ) {
			$repaired_data = $this->repair_optin_groups( $profile_data );
			$this->set_profile_data( $gigya_id, $repaired_data );
		}
	}

	function repair_optin_groups( $profile_data ) {
		$groups     = $profile_data['subscribedToList'];
		$new_groups = array();

		foreach ( $groups as $group ) {
			if ( $this->groups->has_group( $group ) ) {
				// group exists switch to new group
				$replacement  = $this->groups->replacement_for( $group );
				$new_groups[] = $replacement['new_id'];
				$profile_data[ $replacement['field_key'] ] = true;
			} else {
				// if group doesn't exist in mapping keep it intact
				$new_groups[] = $group;
			}
		}

		$profile_data['subscribedToList'] = $new_groups;

		return $profile_data;
	}

	function count_profiles() {
		\WP_CLI::log( 'Fetching Profile Count ...' );
		$query   = $this->get_user_query();
		$query   = str_replace( '*', 'count(*)', $query );
		$request = new GigyaRequest( null, null, 'accounts.search' );
		$request->setParam( 'query', $query );

		$response      = $request->send();
		$response_text = $response->getResponseText();
		$json          = json_decode( $response_text, true );

		if ( $response->getErrorCode() === 0 ) {
			$total = $json['results'][0]['count(*)'];
			return $total;
		} else {
			error_log( $response_text );
			\WP_CLI::error( 'Failed to get count of profiles' );
		}
	}

	function get_profile_data( $gigya_id, $attempt = 1 ) {
		try {
			return get_gigya_user_profile_data( $gigya_id );
		} catch ( \Exception $e ) {
			if ( $attempt < $this->max_attempts ) {
				$this->sleep();
				return $this->get_profile_data( $gigya_id, $attempt + 1 );
			} else {
				$this->log_error( 'r', $gigya_id );
				return false;
			}
		}
	}

	function set_profile_data( $gigya_id, $profile_data, $attempt = 1 ) {
		try {
			return set_gigya_user_profile_data( $gigya_id, $profile_data );
		} catch ( \Exception $e ) {
			if ( $attempt < $this->max_attempts ) {
				$this->sleep();
				return $this->set_profile_data( $gigya_id, $profile_data, $attempt + 1 );
			} else {
				$this->log_error( 'w', $gigya_id );
				return false;
			}
		}
	}

	function log_error( $mode, $gigya_id ) {
		//error_log( "mode: $mode, id: $gigya_id" );
		$this->errors[] = array(
			'mode' => $mode,
			'id' => $gigya_id,
		);
	}

	function get_user_query() {
		//return 'select * from accounts where profile.age > 93';
		return 'select * from accounts';
	}

	function sleep() {
		if ( $this->delay_on_error > 0 ) {
			sleep( $this->delay_on_error );
		}
	}

}
