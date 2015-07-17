<?php

namespace GreaterMedia\Profile;

class GigyaAccountRepairer {

	public $accounts  = array();
	public $debug = false;

	function repair( $page_size = 10 ) {
		$accounts = $this->fetch( $page_size );
		\WP_CLI::success( 'Fetched ' . count( $accounts ) . ' Gigya Profiles.' );

		$this->repair_accounts( $accounts );
	}

	function repair_accounts( &$accounts ) {
		$repair_count = 0;
		$total        = count( $accounts );
		$msg          = "Repairing $total accounts ...";
		$progress_bar = new \WordPress\Utils\ProgressBar( $msg, $total );

		foreach ( $accounts as $account_id ) {
			try {
				$repaired = $this->repair_account( $account_id );

				if ( $repaired ) {
					$repair_count++;
				}
			} catch ( \Exception $e ) {
				\WP_CLI::log( "Failed to Repair account($account_id): " . $e->getMessage() );
			}

			$progress_bar->tick();
		}

		$progress_bar->finish();

		\WP_CLI::success( "Total Repaired Accounts: $repair_count" );
	}

	function repair_account( $account_id ) {
		$profile_repaired = $this->repair_profile( $account_id );
		$data_repaired    = $this->repair_data( $account_id );

		return $profile_repaired || $data_repaired;
	}

	function repair_profile( $account_id ) {
		$profile         = get_gigya_user_profile( $account_id );
		$repaired_result = $this->get_repaired_profile( $profile );

		if ( $repaired_result['repaired'] ) {
			set_gigya_user_profile( $account_id, $repaired_result['profile'] );
			return true;
		}

		return false;
	}

	function repair_data( $account_id ) {
		return false;

		/* TODO: Is this required anymore? */
		$data = get_gigya_user_profile_data( $account_id );
		$repaired_result = $this->get_repaired_data( $data );

		if ( $repaired_result['repaired'] ) {
			set_gigya_user_profile_data( $account_id, $repaired_result['data'] );
			return true;
		}

		return false;
	}

	function get_repaired_data( $data ) {
		$repaired = false;
		$integer_fields = array(
		);

		foreach ( $integer_fields as $field ) {
			if ( array_key_exists( $field, $data ) ) {
				if ( is_string( $data[ $field ] ) ) {
					$data[ $field ] = intval( $data[ $field ] );
					$repaired = true;
				}
			}
		}

		foreach ( $data as $data_field => $data_field_value ) {
			if ( preg_match( '/_count$/', $data_field ) === 1 ) {
				if ( is_string( $data_field_value ) ) {
					$data[ $data_field ] = intval( $data_field_value );
					$repaired = true;
				}
			}
		}

		/* *
		if ( $this->debug ) {
			$data['contest_count']       = "5";
			$data['survey_count']        = "20";
			$data['registeredTimestamp'] = strval( time() );
			$data['lastUpdatedTimestamp'] = strval( time() );
			$repaired = true;
		}
		/* */

		return array(
			'data' => $data,
			'repaired' => $repaired,
		);
	}

	function get_repaired_profile( $profile ) {
		$repaired = false;

		if ( array_key_exists( 'zip', $profile ) ) {
			if ( strlen( $profile['zip'] ) === 4 ) {
				$profile['zip'] = '0' . $profile['zip'];
				$repaired = true;
			}
		}

		/* *
		if ( $this->debug ) {
			$profile['zip'] = '1500';
			$repaired = true;
		}
		/* */

		if ( isset( $profile['age'] ) ) {
			unset( $profile['age'] );
		}

		return array(
			'profile' => $profile,
			'repaired' => $repaired
		);
	}

	function fetch( $page_size = 10 ) {
		\WP_CLI::log( "Fetching Gigya Profiles: Page Size = $page_size" );

		$accounts      = array();
		$progress_bar  = null;
		$cursor        = 0;

		if ( $this->debug ) {
			$query = 'select * from accounts where UID = "60eec81520df415c983da5ea856c90a1"';
		} else {
			$query = 'select * from accounts';
		}

		$has_next_page = true;
		$paginator     = new \GreaterMedia\Gigya\Sync\QueryPaginator(
			'profile', $page_size, true
		);

		while ( $has_next_page ) {
			$result = $paginator->fetch(
				$query, $cursor, false
			);

			$fetched_accounts = $result['results'];
			$has_next_page    = $result['has_next'];
			$cursor           = $result['cursor'];
			$total            = $result['total_results'];
			$total_pages      = $total / $page_size;

			if ( ! empty( $fetched_accounts ) ) {
				foreach ( $fetched_accounts as $fetched_account ) {
					$accounts[] = $fetched_account['UID'];
				}
			}

			if ( is_null( $progress_bar ) ) {
				$msg          = "Fetching $total Gigya Accounts ...";
				$progress_bar = new \WordPress\Utils\ProgressBar( $msg, $total_pages );
				$progress_bar->tick();
			} else {
				$progress_bar->tick();
			}
		}

		if ( ! is_null( $progress_bar ) ) {
			$progress_bar->finish();
		}

		return $accounts;
	}

}
