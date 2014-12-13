<?php

namespace GreaterMedia\Gigya\Sync;

class QueryTask extends SyncTask {

	public $page_size         = 1000;
	public $preview_page_size = 5;
	public $collector         = null;
	public $message_types     = array(
		'enqueue',
		'execute',
		'retry',
		'abort',
		'error',
	);

	function get_task_name() {
		return 'query_task';
	}

	function run() {
		$query          = $this->get_query();
		$cursor         = $this->get_cursor();
		$store_type     = $this->get_store_type();
		$subquery_count = $this->get_subquery_count();

		if ( ! $this->is_fast_preview() ) {
			$paginator = new QueryPaginator( $store_type, $this->page_size );
			$matches   = $paginator->fetch( $query, $cursor );
			$users     = $this->find_users( $matches['results'] );

			$this->save_users( $users );

			return $matches;
		} else {
			return $this->run_fast_preview();
		}
	}

	function run_fast_preview() {
		$store_type = $this->get_store_type();
		$paginator  = new QueryPaginator( $store_type, $this->preview_page_size );
		$query      = $this->get_query();
		$query      = str_replace( 'select *', 'select profile.email, UID', $query );
		$matches    = $paginator->fetch( $query, 0 );
		$users      = $this->find_preview_users( $matches['results'] );

		$this->save_preview_users( $users, $matches['total_results'] );

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

	function find_preview_users( $results ) {
		$finder = new GigyaUserFinder();
		return $finder->results_to_users( $results );
	}

	function save_users( $users ) {
		$collector = $this->get_collector();
		$collector->collect( $users, $this->get_store_type() );
	}

	// KLUDGE - Duplication
	function save_preview_users( $users, $count ) {
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

	function after( $matches ) {
		$sentinel = $this->get_sentinel();

		if ( $this->is_fast_preview() ) {
			$sentinel->set_task_progress( 'profile', 100 );
			$sentinel->set_task_progress( 'data_store', 100 );
			$sentinel->set_task_progress( 'compile_results', 100 );
			$sentinel->set_task_progress( 'preview_results', 100 );
			return;
		}

		if ( array_key_exists( 'progress', $matches ) ) {
			$sentinel->set_task_progress(
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

	function get_subquery_count() {
		return $this->get_param( 'subquery_count' );
	}

	function is_fast_preview() {
		return $this->get_mode() === 'preview' &&
			$this->get_subquery_count() === 1;
	}

}
