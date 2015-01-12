<?php

namespace GreaterMedia\MyEmma\Ajax;

use GreaterMedia\Gigya\Ajax\AjaxHandler;
use GreaterMedia\MyEmma\EmmaAPI;

class ChangeMyEmmaSettings extends AjaxHandler {

	function get_action() {
		return 'change_myemma_settings';
	}

	function run( $params ) {
		$emma_account_id  = sanitize_text_field( $params['emma_account_id'] );
		$emma_public_key  = sanitize_text_field( $params['emma_public_key'] );
		$emma_private_key = sanitize_text_field( $params['emma_private_key'] );

		try {
			$this->check_emma_credentials( $emma_account_id, $emma_public_key, $emma_private_key );
		} catch( \Exception $e ) {
			throw new \Exception(
				'Invalid MyEmma Credentials: Please check the entered keys.'
			);
		}

		// TODO: breakout emma settings into new option
		$settings = get_option( 'member_query_settings', '{}' );
		$settings = json_decode( $settings, true );

		$settings['emma_account_id']  = $emma_account_id;
		$settings['emma_public_key']  = $emma_public_key;
		$settings['emma_private_key'] = $emma_private_key;

		update_option( 'member_query_settings', json_encode( $settings ) );
	}

	// TODO: Make this smarter
	public function check_emma_credentials( $account_id, $public_key, $private_key ) {
		$api      = new EmmaAPI( $account_id, $public_key, $private_key );
		$response = $api->myTriggers();
	}

}
