<?php

namespace GreaterMedia\Gigya\Ajax;

use GreaterMedia\Gigya\GigyaSession;

class GigyaLoginAjaxHandler extends AjaxHandler {

	public function get_action() {
		return 'gigya_login';
	}

	public function is_public() {
		return true;
	}

	public function run( $params ) {
		if ( is_gigya_user_logged_in() ) {
			if ( ! get_gigya_user_data_field( get_gigya_user_id(), 'EmmaSync' ) ) {
				$user_id              = get_gigya_user_id();
				$emma_group_sync_task = new EmmaGroupSyncTask();
				$emma_group_sync_task->enqueue( array( 'user_id' => $user_id ) );
			}
			do_action( 'gigya_login', get_gigya_user_id() );
			return true;
		} else {
			return false;
		}
	}

}
