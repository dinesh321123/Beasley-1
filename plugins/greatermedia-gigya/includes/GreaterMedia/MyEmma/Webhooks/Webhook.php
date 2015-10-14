<?php

namespace GreaterMedia\MyEmma\Webhooks;

use GreaterMedia\Gigya\Ajax\AjaxHandler;
use GreaterMedia\MyEmma\EmmaAPI;

abstract class Webhook extends AjaxHandler {

	abstract function get_event_name();
	public $emma_api;

	function get_action() {
		return 'myemma_webhook_' . $this->get_event_name();
	}

	function is_public() {
		return true;
	}

	function get_url() {
		$ajax_url = admin_url( 'admin-ajax.php' );
		$params   = array(
			'action'     => $this->get_action(),
			'auth_token' => $this->get_required_auth_token()
		);

		$query = http_build_query( $params );

		return $ajax_url . '?' . $query;
	}

	function handle_ajax() {
		$this->authorize();

		$params = $this->get_params();

		if ( is_array( $params ) ) {
			$result = $this->run( $params );
			if ( is_null( $result ) ) {
				$result = true;
			}

			$this->send_json_success( $result );
		} else {
			throw new \Exception( 'Invalid params' );
		}
	}

	function get_params() {
		if ( ! defined( 'PHPUNIT_RUNNER' ) ) {
			$json   = file_get_contents( 'php://input' );
			$params = json_decode( $json, true );

			return $params;
		} else {
			return $this->params;
		}

		return $params;
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

	function get_emma_api() {
		if ( is_null( $this->emma_api ) ) {
			$this->emma_api = new EmmaAPI();
		}

		return $this->emma_api;
	}

	function get_emma_member_id( $params ) {
		return $params['data']['member_id'];
	}

	function get_gigya_user_id( $emma_member_id ) {
		$emma_member_id = intval( $emma_member_id );
		$api            = $this->get_emma_api();
		$response       = $api->membersListById( $emma_member_id );
		$json           = json_decode( $response, true );

		if ( array_key_exists( 'gigya_user_id', $json['fields'] ) ) {
			return $json['fields']['gigya_user_id'];
		} else {
			throw new \Exception(
				"Error: Emma Member does not contain gigya_user_id field - $emma_member_id"
			);
		}
	}
}
