<?php

namespace GreaterMedia\Gigya\Ajax;

use GreaterMedia\Gigya\GigyaRequest;

class ResetPasswordAjaxHandler extends AjaxHandler {

	function get_action() {
		return 'reset_password';
	}

	function is_public() {
		return true;
	}

	function run( $params ) {
		$api_key              = $params['api_key'];
		$new_password         = $params['new_password'];
		$password_reset_token = $params['password_reset_token'];

		$settings      = get_option( 'member_query_settings' );
		$settings      = json_decode( $settings, true );
		$valid_api_key = $settings['gigya_api_key'];

		if ( $api_key !== $valid_api_key ) {
			throw new \Exception( 'Error: Invalid Credentials' );
		}

		$request = new GigyaRequest( null, null, 'accounts.resetPassword' );
		$request->setParam( 'passwordResetToken', $password_reset_token );
		$request->setParam( 'newPassword', $new_password );
		$response = $request->send();

		if ( $response->getErrorCode() === 0 ) {
			return true;
		} else {
			$response_text = $response->getResponseText();
			$json          = json_decode( $response_text, true );
			throw new \Exception( 'Error: ' . $json['errorDetails'] );
		}

		return true;
	}

}
