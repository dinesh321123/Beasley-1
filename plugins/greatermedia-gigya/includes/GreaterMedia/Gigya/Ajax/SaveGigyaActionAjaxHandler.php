<?php

namespace GreaterMedia\Gigya\Ajax;

class SaveGigyaActionAjaxHandler extends AjaxHandler {

	// only whitelisted actions are allowed
	public $allowed_actions = array(
		'action:contest',
		'action:comment',
		'action:social_share',
	);

	function get_action() {
		return 'save_gigya_action';
	}

	function is_public() {
		return false;
	}

	function run( $params ) {
		$action  = $params['action'];
		$user_id = $params['user_id'];

		if ( ! $this->is_allowed_action( $action['actionType'] ) ) {
			return false;
		}

		if ( is_gigya_user_logged_in() ) {
			$user_id = 'logged_in_user';
		} else {
			$user_id = 'guest';
		}

		save_gigya_action( $action, $user_id );

		return true;
	}

	function is_allowed_action( $action_type ) {
		return in_array( $action_type, $this->allowed_actions );
	}

}
