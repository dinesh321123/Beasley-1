<?php

namespace GreaterMedia\Gigya\Ajax;

use GreaterMedia\Gigya\GigyaRequest;

class EmmaMemberOptoutAjaxHandler extends AjaxHandler {

	function is_public() {
		return true;
	}

	function get_action() {
		return 'emma_member_optout';
	}

	function handle_ajax() {
		error_log( 'Handle optout - $_GET = ' . print_r( $_GET, true ) );
		error_log( 'Handle optout - $_POST = ' . print_r( $_POST, true ) );
		error_log( 'Handle optout - json = ' . print_r( file_get_contents( 'php://input' ), true ) );

		wp_send_json_success( true );
	}

	function run( $params ) {
		// no op
	}

}
