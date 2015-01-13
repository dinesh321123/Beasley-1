<?php

namespace GreaterMedia\Gigya\Sync;

class EmmaGroupSyncTask extends Task {

	public $max_retries = 0;
	public $message_types = array(
		'enqueue',
		'execute',
		'retry',
		'abort',
		'error',
		'after',
	);

	function get_task_name() {
		return 'emma_group_sync';
	}

	function run() {
		$user_id = $this->get_user_id();
		$syncer  = new EmmaGroupSyncer( $user_id );
		$syncer->sync();
	}

	function get_user_id() {
		return $this->get_param( 'user_id' );
	}

}
