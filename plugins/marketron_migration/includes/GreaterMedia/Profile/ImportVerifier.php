<?php

namespace GreaterMedia\Profile;

class ImportVerifier {

	public $marketron_accounts;
	public $gigya_accounts;
	public $errors;

	function __construct( $marketron_accounts, $gigya_accounts ) {
		$this->marketron_accounts = $marketron_accounts;
		$this->gigya_accounts     = $gigya_accounts;
		$this->errors             = array();
	}

	function verify() {
		$total_accounts = count( $this->marketron_accounts );
		$progress_bar   = new \cli\progress\Bar( "Verifying $total_accounts Accounts", $total_accounts );

		foreach ( $this->marketron_accounts as $marketron_account ) {
			$uid       = $marketron_account['UID'];
			$user_info = $marketron_account['userInfo'];

			if ( array_key_exists( $uid, $this->gigya_accounts ) ) {
				$gigya_account = $this->gigya_accounts[ $uid ];
			} else {
				$this->log_error( "User not imported: $uid" );
				continue;
			}

			foreach ( $user_info as $field_name => $field_value ) {
				$field_path     = "profile.$field_name";
				$imported_value = trim( $gigya_account[$field_path] );
				$expected       = trim( $user_info[ $field_name ] );

				if ( $imported_value !== $expected ) {
					$display_value = empty( $imported_value ) ? 'null' : "($imported_value)";
					$this->log_error(
						"Error: UID($uid) Expected Field($field_name) to be ($expected) but was $display_value"
					);
				}
			}

			$progress_bar->tick();
		}

		$progress_bar->finish();
		return count( $this->errors ) === 0;
	}

	function log_error( $message ) {
		$this->errors[] = $message;
	}

}
