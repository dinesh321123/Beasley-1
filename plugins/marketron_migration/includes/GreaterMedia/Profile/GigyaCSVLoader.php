<?php

namespace GreaterMedia\Profile;

class GigyaCSVLoader {

	function load( $path ) {
		$file           = fopen( $path, 'r' );
		$columns        = fgetcsv( $file, 0, ',', '"' );
		$accounts       = array();
		$account        = $this->read_line( $file, $columns );
		$total_accounts = 37832;
		$progress_bar   = new \cli\progress\Bar( "Importing $total_accounts Accounts in Gigya CSV", $total_accounts );

		while ( $account !== false ) {
			$account = $this->read_line( $file, $columns );
			if ( $account !== false ) {
				if ( empty ( $account['UID'] ) ) {
					var_dump( $account );
					\WP_CLI::error('stop');
				}
				$uid = $account['UID'];
				$accounts[ $uid ] = $account;
			}

			$progress_bar->tick();
		}

		$progress_bar->finish();

		return $accounts;
	}

	function read_line( $file, $columns ) {
		$account = array();
		//$fields  = fgetcsv( $file, 0, ',', '"' );
		$line = fgets( $file );
		$line = str_replace( '""', 'DBL_QUOTE', $line );
		if ( $fields === false ) {
			return false;
		}

		foreach ( $columns as $index => $column ) {
			$field = $fields[ $index ];
			var_dump( $field );
			$account[ $column ] = $field;
		}

		return $account;
	}

}
