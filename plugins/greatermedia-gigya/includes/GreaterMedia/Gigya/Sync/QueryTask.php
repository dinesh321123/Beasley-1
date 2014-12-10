<?php

namespace GreaterMedia\Gigya\Sync;

class QueryTask extends SyncTask {

	public $page_size = 10;
	public $collector = null;
	public $message_types = array(
		'enqueue',
		'execute',
		'retry',
		'abort',
		'error',
		'after',
	);

	function get_task_name() {
		return 'query_task';
	}

	function run() {
		$query      = $this->get_query();
		$cursor     = $this->get_cursor();
		$store_type = $this->get_store_type();

		$paginator = new QueryPaginator( $store_type, $this->page_size );
		$matches   = $paginator->fetch( $query, $cursor );

		$users = $this->find_users( $matches['results'] );
		$this->save_users( $users );

		return $matches;
	}

	function find_users( $results ) {
		if ( function_exists( 'array_column' ) ) {
			return array_column( $results, 'UID' );
		} else {
			$users = array();

			foreach ( $results as $result ) {
				$users[] = $result['UID'];
			}

			return $users;
		}
	}

	function save_users( $users ) {
		$collector = $this->get_collector();
		$collector->collect( $users, $this->get_store_type() );
	}

	function after( $matches ) {
		if ( array_key_exists( 'progress', $matches ) ) {
			$this->get_sentinel()->set_task_progress(
				$this->get_store_type(),
				$matches['progress']
			);
		}

		if ( $matches['has_next'] ) {
			$params           = $this->export_params();
			$params['cursor'] = $matches['cursor'];

			$this->enqueue( $params );
		} else if ( $this->get_sentinel()->can_compile_results() ) {
			$params = $this->export_params();
			$compile_results_task = new CompileResultsTask();
			$compile_results_task->enqueue( $params );
		}
	}

	function get_query() {
		return $this->get_param( 'query' );
	}

	function get_cursor() {
		if ( $this->has_param( 'cursor' ) ) {
			return $this->get_param( 'cursor' );
		} else {
			return 0;
		}
	}

	function get_store_type() {
		return $this->get_param( 'store_type' );
	}

	function get_collector() {
		if ( is_null( $this->collector ) ) {
			$this->collector = new UserCollector(
				$this->get_site_id(),
				$this->get_member_query_id()
			);
		}

		return $this->collector;
	}

}
