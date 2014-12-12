<?php

namespace GreaterMedia\Gigya\Ajax;

class SaveGigyaActionAjaxHandler extends AjaxHandler {

	function get_action() {
		return 'save_gigya_action';
	}

	function is_public() {
		return false;
	}

	function run( $params ) {
		$action  = $params['action'];
		$user_id = $params['user_id'];

		// don't allow direct user_ids - ie:- A user can only save
		// actions as themselves or as a guest, and not under someone
		// else's user_id.
		if ( $user_id !== 'guest' && $user_id != 'logged_in_user' ) {
			$user_id = 'guest';
		}

		// if want to use against logged in user and not logged in - abort
		if ( $user_id === 'logged_in_user' && ! is_gigya_user_logged_in() ) {
			return false;
		} else {
			save_gigya_action( $action, $user_id );
			return true;
		}
	}

}
