<?php

namespace GreaterMedia\Import\Repair;

use GreaterMedia\Gigya\Sync\QueryPaginator;
use GreaterMedia\Gigya\GigyaRequest;
use WordPress\Utils\ProgressBar;

class OptinGroupsRepairer {

	public $groups;
	public $errors    = array();
	public $total = null;

	public $page_size      = 1000;
	public $delay_on_error = 0.5; // seconds
	public $max_attempts   = 5;

	function repair( $errors_file ) {
		$user_ids     = $this->fetch_user_ids();
		$total        = count( $user_ids );
		$msg          = "Repairing Optin Groups for $total users";
		$progress_bar = new ProgressBar( $msg, $total );
		$repair_count = 0;

		foreach ( $user_ids as $user_id ) {
			$repaired = $this->repair_profile( $user_id );
			if ( $repaired ) {
				$repair_count++;
			}
			$progress_bar->tick();
		}

		$progress_bar->finish();
		$this->save_errors( $errors_file );

		return $repair_count;
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

			if ( $repaired_data !== false ) {
				return $this->set_profile_data( $gigya_id, $repaired_data );
			}
		}

		return false;
	}

	function repair_optin_groups( $profile_data ) {
		$groups     = $profile_data['subscribedToList'];
		$new_groups = array();
		$changed    = false;

		foreach ( $groups as $group ) {
			if ( $this->groups->has_group( $group ) ) {
				// group exists switch to new group
				$replacement  = $this->groups->replacement_for( $group );
				$new_groups[] = $replacement['new_id'];
				$profile_data[ $replacement['field_key'] ] = true;
				$changed = true;
			} else {
				// if group doesn't exist in mapping keep it intact
				$new_groups[] = $group;
			}
		}

		if ( $changed ) {
			$profile_data['subscribedToList'] = $new_groups;
			return $profile_data;
		} else {
			return false;
		}
	}

	function count_profiles() {
		if ( is_null( $this->total ) ) {
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
				$this->total = $total;
			} else {
				error_log( $response_text );
				\WP_CLI::error( 'Failed to get count of profiles' );
			}
		}

		return $this->total;
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
		return 'select UID from accounts';
	}

	function sleep() {
		if ( $this->delay_on_error > 0 ) {
			sleep( $this->delay_on_error );
		}
	}

	function fetch_user_ids() {
		\WP_CLI::log( 'Fetching User IDs ...' );

		$progress_bar = null;
		$query        = $this->get_user_query();;
		$paginator    = new QueryPaginator( 'profile', $this->page_size );
		$cursor       = 0;
		$has_next     = true;
		$all_user_ids = array();

		while ( $has_next ) {
			$query_result = $this->fetch_user_id_page( $paginator, $query, $cursor );
			$user_ids     = $query_result['results'];
			$user_ids     = array_column( $user_ids, 'UID' );
			$cursor       = $query_result['cursor'];
			$has_next     = $query_result['has_next'];
			$all_user_ids = array_merge( $all_user_ids, $user_ids );
			$total        = $query_result['total_results'];

			if ( is_null( $progress_bar ) ) {
				$msg          = "Fetching $total User IDs";
				$progress_bar = new ProgressBar( $msg, $total );
			} else {
				$progress_bar->tick();
			}
		}

		$progress_bar->finish();

		return $all_user_ids;
	}

	function fetch_user_id_page( $paginator, $query, $cursor, $attempt = 1 ) {
		try {
			return $paginator->fetch( $query, $cursor );
		} catch ( \Exception $e ) {
			if ( $attempt < $this->max_attempts ) {
				$this->sleep();
				return $this->fetch_user_id_page( $paginator, $query, $cursor, $attempt + 1 );
			} else {
				\WP_CLI::error( "Failed to fetch user ids at $cursor for: $query" );
			}
		}
	}

}
