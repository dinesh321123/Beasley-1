<?php

namespace GreaterMedia\Profile;

class GigyaAccountImportVerifier {

	function verify( $marketron_accounts, $errors_file, $page_size = 10 ) {
		$cursor       = 0;
		$total        = count( $marketron_accounts );
		$failures     = array();
		$msg          = "Verifying $total Gigya Accounts";
		$progress_bar = new \WordPress\Utils\ProgressBar( $msg, $total );

		while ( $cursor < $total ) {
			$page     = array_slice( $marketron_accounts, $cursor, $page_size );
			$cursor  += $page_size;
			$failures = array_merge( $failures, $this->verify_by_page( $page, $progress_bar ) );
		}

		$progress_bar->finish();
		$total_failures = count( $failures );

		if ( $total_failures > 0 ) {
			\WP_CLI::warning( "There were $total_failures errors with the import." );
			$this->save_failures( $failures, $errors_file );
		} else {
			\WP_CLI::success( 'All Marketron Accounts imported correctly!' );
		}
	}

	function verify_by_page( $marketron_accounts, $progress_bar ) {
		$failures       = array();
		$gigya_accounts = $this->get_gigya_accounts( $marketron_accounts );
		$comparer       = new AccountComparer();

		foreach ( $marketron_accounts as $index => $marketron_account ) {
			$gigya_account = $gigya_accounts[ $index ];
			$failure       = $comparer->compare( $marketron_account, $gigya_account );

			if ( $failure !== true ) {
				$failures[] = $failure;
			}

			$progress_bar->tick();
		}

		return $failures;
	}

	function get_gigya_accounts( $marketron_accounts ) {
		$user_ids = array();

		foreach( $marketron_accounts as $marketron_account ) {
			$user_ids[] = $marketron_account['UID'];
		}

		return $this->fetch_gigya_accounts( $user_ids );
	}

	function fetch_gigya_accounts( $user_ids ) {
		$where_clause   = "'" . implode( "', '", $user_ids ) . "'";
		$query = <<<GQL
select * from accounts
where UID in ($where_clause)
GQL;

		$request = new \GreaterMedia\Gigya\GigyaRequest( null, null, 'accounts.search' );
		$request->setParam( 'query', $query );
		$response      = $request->send();
		$response_text = $response->getResponseText();
		$user_ids_flipped = array_flip( $user_ids );

		if ( $response->getErrorCode() === 0 ) {
			$json = json_decode( $response_text, true );
			$accounts = $json['results'];
			usort( $accounts, function( $a, $b ) use ( &$user_ids_flipped ) {
				$a_id = $a['UID'];
				$b_id = $b['UID'];
				$a_index = $user_ids_flipped[ $a_id ];
				$b_index = $user_ids_flipped[ $b_id ];

				if ( $a_index > $b_index ) {
					return 1;
				} else if ( $b_index > $a_index ) {
					return -1;
				} else {
					return 0;
				}
		   	});

			return $accounts;
		} else if ( $attempts < $this->max_attempts ) {
			\WP_CLI::warning( "Failed to fetch gigya accounts, retry #$attempt ..." );
			return $this->fetch_gigya_accounts( $user_ids );
		} else {
			\WP_CLI::error( 'Failed to fetch gigya accounts' );
		}
	}

	function save_failures( $failures, $errors_file ) {
		\WP_CLI::success( "Saving Errors ..." );

		file_put_contents(
			$errors_file, json_encode( $failures, JSON_PRETTY_PRINT )
		);

		\WP_CLI::success( "Saved Errors to $errors_file" );
	}

}
