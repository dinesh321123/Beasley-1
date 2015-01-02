<?php

namespace GreaterMedia\Gigya\Ajax;

use GreaterMedia\MyEmma\EmmaAPI;

class ChangeGigyaSettingsAjaxHandler extends AjaxHandler {

	public function get_action() {
		return 'change_gigya_settings';
	}

	public function run( $params ) {
		$gigya_api_key           = sanitize_text_field( $params['gigya_api_key'] );
		$gigya_secret_key        = sanitize_text_field( $params['gigya_secret_key'] );
		$gigya_auth_screenset    = sanitize_text_field( $params['gigya_auth_screenset'] );
		$gigya_account_screenset = sanitize_text_field( $params['gigya_account_screenset'] );
		$emma_account_id         = sanitize_text_field( $params['emma_account_id'] );
		$emma_public_key         = sanitize_text_field( $params['emma_public_key'] );
		$emma_private_key        = sanitize_text_field( $params['emma_private_key'] );

		try {
			$this->check_gigya_credentials( $gigya_api_key, $gigya_secret_key );
		} catch ( \Exception $e ) {
			throw new \Exception( 'Invalid Gigya Credentials: Please check the entered keys.' );
		}

		try {
			$this->check_emma_credentials( $emma_account_id, $emma_public_key, $emma_private_key );
		} catch( \Exception $e ) {
			throw new \Exception( 'Invalid MyEmma Credentials: Please check the entered keys.' );
		}

		$settings                            = get_option( 'member_query_settings', '{}' );
		$settings                            = json_decode( $settings, true );
		$settings['gigya_api_key']           = $gigya_api_key;
		$settings['gigya_secret_key']        = $gigya_secret_key;
		$settings['emma_account_id']         = $emma_account_id;
		$settings['emma_public_key']         = $emma_public_key;
		$settings['emma_private_key']        = $emma_private_key;
		$settings['gigya_auth_screenset']    = $gigya_auth_screenset;
		$settings['gigya_account_screenset'] = $gigya_account_screenset;

		update_option( 'member_query_settings', json_encode( $settings ) );
	}

	public function check_gigya_credentials( $api_key, $secret_key ) {
		// Not using GigyaRequest here because we want to test the
		// specified credentials, not the saved ones
		$request = new \GSRequest(
			$api_key,
			$secret_key,
			'accounts.search'
		);

		$request->setParam( 'query',  'select * from accounts limit 1' );
		$response = $request->send();

		if ( $response->getErrorCode() !== 0 ) {
			throw new \Exception( $response->getErrorMessage() );
		}
	}

	public function check_emma_credentials( $account_id, $public_key, $private_key ) {
		$api      = new EmmaAPI( $account_id, $public_key, $private_key );
		$response = $api->myTriggers();
	}

}
