<?php

namespace GreaterMedia\Gigya\Sync;

class PreviewResultsTask extends SyncTask {

	public $page_size    = 5;

	function get_task_name() {
		return 'preview_results';
	}

	function run() {
		$paginator = new ResultPaginator(
			$this->get_site_id(),
			$this->get_member_query_id(),
			$this->page_size
		);

		$count = $paginator->count();

		if ( $count > 0 ) {
			$user_ids = $paginator->fetch();
			$finder   = new GigyaUserFinder();
			$users    = $finder->find( $user_ids );
		} else {
			$user_ids = array();
			$users    = array();
		}

		$this->save_users( $users, $count );
	}

	function save_users( $users, $count ) {
		$results = array(
			'total' => $count,
			'users' => $users,
		);

		$json = json_encode( $results );

		update_post_meta(
			$this->get_member_query_id(),
			'member_query_preview_results',
			$json
		);
	}

	function after( $result ) {
		$this->get_sentinel()->set_task_progress( 'preview_results', 100 );
	}

}
