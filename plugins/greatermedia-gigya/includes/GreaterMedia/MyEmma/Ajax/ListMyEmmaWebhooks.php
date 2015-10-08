<?php

namespace GreaterMedia\MyEmma\Ajax;

use GreaterMedia\Gigya\Ajax\AjaxHandler;
use GreaterMedia\MyEmma\EmmaAPI;

class ListMyEmmaWebhooks extends AjaxHandler {

	function get_action() {
		return 'list_myemma_webhooks';
	}

	function run( $params ) {
		$api      = new EmmaAPI();
		$response = $api->myWebhooks();
		$json     = json_decode( $response, true );

		return $json;
	}

}
