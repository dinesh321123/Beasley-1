<?php

namespace GreaterMedia\Gigya\Sync;

use GreaterMedia\MyEmma\EmmaAPI;

class ExportResultsTask extends SyncTask {

	public $emma_api    = null;
	public $page_size   = 500;
	public $max_retries = 0;

	function get_task_name() {
		return 'export_results';
	}

	function run() {
		$segment_id = $this->create_or_update_email_segment();
		$cursor     = $this->get_cursor();
		$paginator  = new ResultPaginator(
			$this->get_site_id(),
			$this->get_member_query_id(),
			$this->page_size
		);

		$count = $paginator->count();

		if ( $cursor === 0 ) {
			$this->remove_all_members_in_segment( $segment_id );
		}

		if ( $count > 0 ) {
			$user_ids = $paginator->fetch( $cursor );
			$finder   = new GigyaUserFinder();
			$users    = $finder->find( $user_ids );
		} else {
			$user_ids = array();
			$users    = array();

			return array(
				'total_results' => 0,
				'has_next'      => false,
				'cursor'        => $cursor,
				'progress'      => 100,
			);
		}

		$import_id = $this->add_members_to_segment( $users, $segment_id );
		//$stats     = $this->get_member_import_stats( $import_id );

		$results_in_page = count( $users );
		$total_results   = $count;
		$has_next        = $cursor + $this->page_size < $count;
		$progress        = ceil( ( $cursor + $results_in_page ) / $total_results * 100 );

		return array(
			'total_results' => $total_results,
			'has_next'      => $has_next,
			'cursor'        => $cursor + $this->page_size,
			'progress'      => $progress,
		);
	}

	function after( $result ) {
		$sentinel = $this->get_sentinel();
		$sentinel->set_task_progress( 'export_results', $result['progress'] );

		if ( $result['has_next'] ) {
			$params           = $this->export_params();
			$params['cursor'] = $result['cursor'];

			$this->enqueue( $params );
		} else {
			$sentinel->set_last_export( time() );
			$sentinel->set_status_code( 'completed' );

			$cleanup_task = new CleanupTask();
			$cleanup_task->enqueue( $this->export_params() );
		}
	}

	function get_members_in_segment( $segment_id ) {
		$api      = $this->get_emma_api();
		$response = $api->groupsGetMembers( $segment_id );
		$json     = json_decode( $response, true );

		return $json;
	}

	function add_members_to_segment( $members, $segment_id ) {
		$api    = $this->get_emma_api();
		$params = array(
			'members'   => $members,
			'group_ids' => array( intval( $segment_id ) )
		);

		$response = $api->membersBatchAdd( $params );
		$json     = json_decode( $response, true );

		return $json['import_id'];
	}

	function get_member_import_stats( $import_id ) {
		$api      = $this->get_emma_api();
		$response = $api->membersImportStats( $import_id );
		//error_log( print_r( $response, true ) );
		$json     = json_decode( $response, true );

		return $json;
	}

	function remove_all_members_in_segment( $segment_id ) {
		$api = $this->get_emma_api();
		$api->groupsRemoveAllMembers( $segment_id );

		return true;
	}

	function create_or_update_email_segment() {
		if ( ! $this->has_email_segment() || ! $this->has_remote_email_segment() ) {
			$segment_id = $this->create_email_segment();
			$this->get_sentinel()->set_email_segment_id( $segment_id );
		} else {
			$segment_id = $this->get_email_segment_id();
			$this->update_email_segment( $segment_id );
		}

		return $segment_id;
	}

	function create_email_segment() {
		$api = $this->get_emma_api();
		$groups = array(
			'groups' => array(
				array(
					'group_name' => $this->get_group_name()
				),
			)
		);

		$response = $api->groupsAdd( $groups );
		$json     = json_decode( $response, true );

		if ( is_array( $json ) ) {
			return strval( $json[0]['member_group_id'] );
		} else {
			return '';
		}
	}

	function update_email_segment( $segment_id ) {
		$api = $this->get_emma_api();
		$group = array(
			'group_name' => $this->get_group_name()
		);

		$response = $api->groupsUpdateSingle( $segment_id, $group );

		return $segment_id;
	}

	function get_group_name() {
		return get_the_title( $this->get_member_query_id() );
	}

	function get_email_segment_id() {
		$segment_id = $this->get_sentinel()->get_email_segment_id();
		if ( is_numeric( $segment_id ) ) {
			return intval( $segment_id );
		} else {
			return $segment_id;
		}
	}

	function has_email_segment() {
		$segment_id = $this->get_email_segment_id();
		return $segment_id !== '';
	}

	function has_remote_email_segment() {
		$segment_id = $this->get_email_segment_id();
		$api        = $this->get_emma_api();

		try {
			$response = $api->groupsGetById( $segment_id );
			return true;
		} catch ( \Exception $e ) {
			return false;
		}
	}

	function get_emma_api() {
		if ( is_null( $this->emma_api ) ) {
			$this->emma_api = new EmmaAPI();
		}

		return $this->emma_api;
	}

	function get_cursor() {
		if ( $this->has_param( 'cursor' ) ) {
			return $this->get_param( 'cursor' );
		} else {
			return 0;
		}
	}

}
