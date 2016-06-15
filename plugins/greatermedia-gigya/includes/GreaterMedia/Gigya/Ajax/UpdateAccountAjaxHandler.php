<?php

namespace GreaterMedia\Gigya\Ajax;

use GreaterMedia\Gigya\Sync\EmmaGroupSyncTask;

class UpdateAccountAjaxHandler extends AjaxHandler {

	function get_action() {
		return 'update_account';
	}

	function is_public() {
		return true;
	}

	function run( $params ) {
		if ( is_gigya_user_logged_in() ) {
			$user_id              = get_gigya_user_id();
			$emma_group_sync_task = new EmmaGroupSyncTask();
			$emma_group_sync_task->enqueue( array( 'user_id' => $user_id ) );

			wp_mail( 'elliottstocks@get10up.com', 'Emma Debug', sprintf( 'Updating Emma Account: %s', $user_id ) );
			
			$transient = 'gigya_user_profile_' . $user_id;
			delete_transient( $transient );
			do_action( 'update_gigya_account', $user_id );

			return true;
		} else {
			return false;
		}
	}

}
