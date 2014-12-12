<?php

namespace GreaterMedia\Gigya\Sync;

use GreaterMedia\Gigya\MemberQuery;

class Launcher {

	public $tasks = null;
	public $last_member_query = null;

	function get_tasks() {
		if ( is_null( $this->tasks ) ) {
			$this->tasks = array(
				'initializer'     => new InitializerTask(),
				'profile'         => new ProfileQueryTask(),
				'data_store'      => new DataStoreQueryTask(),
				'compile_results' => new CompileResultsTask(),
				'preview_results' => new PreviewResultsTask(),
				'export_results'  => new ExportResultsTask(),
				'cleanup'         => new CleanupTask(),
			);
		}

		return $this->tasks;
	}

	function register() {
		foreach ( $this->get_tasks() as $task ) {
			$task->register();
		}

		if ( ! defined( 'PHPUNIT_RUNNER' ) ) {
			set_error_handler( array( $this, 'did_system_error' ) );
		}
	}

	function did_system_error() {
		$args = func_get_args();
		error_log( 'System Error: ' . implode( "\n", $args ) );
	}

	function get_task( $task_id ) {
		$tasks = $this->get_tasks();
		return $tasks[ $task_id ];
	}

	function launch( $member_query_id, $mode = 'preview' ) {
		$params       = $this->get_launch_params( $member_query_id, $mode );
		$checksum     = $params['checksum'];
		$initializer  = $this->get_task( 'initializer' );
		$sentinel     = new Sentinel( $params['member_query_id'], $params );

		$sentinel->set_checksum( $checksum );
		$initializer->enqueue( $params );
	}

	function preview( $constraints ) {
		$member_query_id = $this->create_preview( $constraints );
		$this->launch( $member_query_id, 'preview' );

		return $member_query_id;
	}

	function create_preview( $constraints ) {
		$params = array(
			'post_type'   => 'member_query_preview',
			'post_status' => 'draft',
		);

		$post_id      = wp_insert_post( $params );
		$member_query = new MemberQuery( $post_id, $constraints );
		$json         = json_encode( $constraints );

		$member_query->save( $json );
		$this->last_member_query = $member_query;

		return $post_id;
	}

	function get_launch_params( $member_query_id, $mode = 'preview' ) {
		$params = array(
			'site_id'         => get_current_blog_id(),
			'member_query_id' => $member_query_id,
			'mode'            => $mode,
			'checksum'        => md5( strtotime( 'now' ) )
		);

		$member_query          = $this->member_query_for( $member_query_id );
		$params['conjunction'] = $member_query->get_subquery_conjunction();

		return $params;
	}

	// KLUDGE
	// TODO: get rid of the extra state here
	function member_query_for( $member_query_id ) {
		if ( ! is_null( $this->last_member_query ) &&
			$this->last_member_query->post_id === $member_query_id ) {
			return $this->last_member_query;
		} else {
			return new MemberQuery( $member_query_id );
		}
	}

}
