<?php

namespace GreaterMedia\Gigya\Ajax;

use GreaterMedia\Gigya\Sync\EmmaGroupSyncTask;

class RegisterAccountAjaxHandler extends AjaxHandler {

	function get_action() {
		return 'register_account';
	}

	function is_public() {
		return true;
	}

	function run( $params ) {
		if ( is_gigya_user_logged_in() ) {
			return true;
		} else {
			return false;
		}
	}

}
