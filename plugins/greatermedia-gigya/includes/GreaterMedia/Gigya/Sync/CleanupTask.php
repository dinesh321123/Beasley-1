<?php

namespace GreaterMedia\Gigya\Sync;

class CleanupTask extends SyncTask {

	function get_task_name() {
		return 'cleanup';
	}

	function run() {
		$sentinel = $this->get_sentinel();
		$mode     = $this->get_mode();

		$this->clear_users();
		$this->clear_results();

		if ( $mode === 'preview' || $this->is_auto_sync_query() ) {
			$sentinel->reset();
			wp_delete_post( $this->get_member_query_id(), true );
		}
	}

	function get_job_db() {
		return TempDatabase::get_instance()->get_db();
	}

	function clear_users() {
		$db    = $this->get_job_db();
		$where = $this->get_where_clause();

		$db->delete( 'member_query_users', $where );
	}

	function clear_results() {
		$db    = $this->get_job_db();
		$where = $this->get_where_clause();

		$db->delete( 'member_query_results', $where );
	}

	function get_where_clause() {
		return array(
			'site_id'         => $this->get_site_id(),
			'member_query_id' => $this->get_member_query_id(),
		);
	}

	function verify_checksum() {
		return true;
	}

	/* Kludge: auto sync queries use post_type of 'preview' but
	 * are run with mode 'export' */
	function is_auto_sync_query() {
		$query_id = $this->get_member_query_id();
		$post     = get_post( $query_id );

		return $post->post_type === 'member_query_preview';
	}

}
