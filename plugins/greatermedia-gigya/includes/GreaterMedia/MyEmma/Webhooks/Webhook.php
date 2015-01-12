<?php

namespace GreaterMedia\MyEmma\Webhooks;

use GreaterMedia\Gigya\Ajax\AjaxHandler;
use GreaterMedia\MyEmma\EmmaAPI;

abstract class Webhook extends AjaxHandler {

	abstract function get_event_name();

	function get_url() {
		$ajax_url = admin_url( 'admin_ajax.php' );
		$params   = array(
			'action'     => $this->get_action(),
			'auth_token' => $this->get_required_auth_token()
		);

		$query = http_build_query( $params );

		return $ajax_url . '?' . $query;
	}

	function get_action() {
		return 'myemma_webhook_' . $this->get_event_name();
	}

	function handle_ajax() {
		$this->authorize();

		$json   = file_get_contents( 'php://input' );
		$params = json_decode( $json, true );

		if ( is_array( $params ) ) {
			$result = $this->run( $params );
			if ( ! $result ) {
				$result = true;
			}

			$this->send_json_success( $result );
		} else {
			throw new \Exception( 'Invalid params' );
		}
	}

	function authorize() {
		$required_auth_token = $this->get_required_auth_token();

		if ( $required_auth_token !== '' ) {
			$auth_token = array_key_exists( 'auth_token', $_GET ) ? $_GET['auth_token'] : '';

			if ( $auth_token !== '' && $auth_token === $required_auth_token ) {
				return true;
			} else {
				throw new \Exception( 'Invalid Auth Token' );
			}
		} else {
			throw new \Exception( 'MyEmma Auth Token not configured' );
		}
	}

	function get_required_auth_token() {
		$settings = get_option( 'member_query_settings' );
		$settings = json_decode( $settings, true );

		if ( array_key_exists( 'emma_webhook_auth_token', $settings ) ) {
			return $settings['emma_webhook_auth_token'];
		} else {
			return '';
		}
	}

}
