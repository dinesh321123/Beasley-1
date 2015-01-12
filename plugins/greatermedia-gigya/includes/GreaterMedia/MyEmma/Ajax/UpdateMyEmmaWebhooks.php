<?php

namespace GreaterMedia\MyEmma\Ajax;

use GreaterMedia\Gigya\Ajax\AjaxHandler;
use GreaterMedia\MyEmma\EmmaAPI;
use GreaterMedia\MyEmma\Webhooks\MemberOptout;

class UpdateMyEmmaWebhooks extends AjaxHandler {

	function get_action() {
		return 'update_myemma_webhooks';
	}

	function run( $params ) {
		$emma_webhook_auth_token = sanitize_text_field( $params['emma_webhook_auth_token'] );
		if ( $emma_webhook_auth_token === '' || ! ctype_alnum( $emma_webhook_auth_token ) ) {
			throw new \Exception( 'Invalid Auth Token, must be alphanumeric.' );
		}

		$settings = get_option( 'member_query_settings', '{}' );
		$settings = json_decode( $settings, true );

		$settings['emma_webhook_auth_token'] = $emma_webhook_auth_token;

		update_option( 'member_query_settings', json_encode( $settings ) );

		$this->update_webhooks();
		return $this->get_remote_webhooks();
	}

	function update_webhooks() {
		$webhooks = $this->get_webhooks();
		$api      = new EmmaAPI();

		$api->webhooksRemoveAll();

		foreach ( $webhooks as $webhook ) {
			$params = array(
				'url'   => $webhook->get_url(),
				'event' => $webhook->get_event_name()
			);

			$api->webhooksCreate( $params );
		}
	}

	function get_remote_webhooks() {
		$api      = new EmmaAPI();
		$response = $api->myWebhooks();
		$json     = json_decode( $response, true );

		return $json;
	}

	function get_webhooks() {
		$webhooks = array(
			new MemberOptout(),
		);

		return $webhooks;
	}

}
