<?php

namespace GreaterMedia\MyEmma;

require_once __DIR__ . '/../../MyEmma/Emma.php';

class EmmaAPI extends \Emma {

	function __construct( $account_id = null, $public_key = null, $private_key = null ) {
		if ( is_null( $account_id ) && is_null( $public_key ) && is_null( $private_key ) ) {
			$settings    = $this->get_member_query_settings();
			$account_id  = $settings['emma_account_id'];
			$public_key  = $settings['emma_public_key'];
			$private_key = $settings['emma_private_key'];

			parent::__construct( $account_id, $public_key, $private_key );
		} else {
			parent::__construct( $account_id, $public_key, $private_key );
		}
		//$this->_debug = true;
	}

	public function get_member_query_settings() {
		$settings = get_option( 'member_query_settings' );
		if ( $settings === '' ) {
			throw new \Exception( 'Fatal Error: Emma settings not found.' );
		} else {
			$settings = json_decode( $settings, true );
		}

		return $settings;
	}
}
