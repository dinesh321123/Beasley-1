<?php

namespace GreaterMedia\Gigya\Sync;

class ExportResultsTask extends SyncTask {

	function get_task_name() {
		return 'export_results';
	}

	function run() {
		$sentinel = $this->get_sentinel();
		$sentinel->set_email_segment_id( '2086163' );
	}

	function after( $result ) {
		$sentinel = $this->get_sentinel();
		$sentinel->set_task_progress( 'export_results', 100 );
	}

}
