<?php

namespace GreaterMedia\Profile;

class AccountComparer {

	function compare( $marketron_account, $gigya_account ) {
		$marketron_id = $marketron_account['UID'];
		$gigya_id     = $gigya_account['UID'];

		$failure = array();
		$result  = array();

		$this->chk( 'UID', $marketron_account, $gigya_account, $failure );

		$a = $marketron_account['profile'];
		$b = $gigya_account['profile'];

		foreach ( $a as $field => $value ) {
			$this->chk( $field, $a, $b, $failure, 'profile' );
		}

		$a = $marketron_account['data'];
		$b = $gigya_account['data'];

		foreach ( $a as $field => $value ) {
			$this->chk( $field, $a, $b, $failure, 'data' );
		}

		$result['id']       = $marketron_account['UID'];
		$result['failures'] = array( $failure );

		return $result;
	}

	function compare_field( $field, &$a, &$b ) {
		if ( ! array_key_exists( $field, $a ) ) {
			return false;
		}

		if ( ! array_key_exists( $field, $b ) ) {
			return false;
		}

		if ( $a[ $field ] === $b[ $field ] ) {
			return true;
		} else {
			return false;
		}
	}

	function chk( $field, &$a, &$b, &$failure, $path = null ) {
		$success = $this->compare_field( $field, $a, $b );

		if ( $success == false ) {
			$failure[] = array(
				'path'    => is_null( $path ) ? $field : "{$path}.{$field}",
				'expected' => $a[ $field ],
				'actual'   => $b[ $field ],
			);
		}
	}

}
