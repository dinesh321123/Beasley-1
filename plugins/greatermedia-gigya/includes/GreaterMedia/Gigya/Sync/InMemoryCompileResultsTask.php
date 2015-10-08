<?php

namespace GreaterMedia\Gigya\Sync;

class InMemoryCompileResultsTask extends SyncTask {

	public $page_size = 1000;

	function get_task_name() {
		return 'compile_results';
	}

	function run() {
		$user_ids = $this->compile();
		return $this->save_users( $user_ids );
	}

	function after( $result ) {
		$this->get_sentinel()->set_task_progress( 'compile_results', 100 );

		$params = $this->export_params();
		$params['cursor'] = 0;

		if ( $this->get_mode() === 'preview' ) {
			$preview_task = new PreviewResultsTask();
			$preview_task->enqueue( $params );
		} else {
			$export_task = new ExportResultsTask();
			$export_task->enqueue( $params );
		}
	}

	function save_users( $user_ids ) {
		$user_ids     = array_values( $user_ids );
		$total        = count( $user_ids );
		$insert_query = <<<SQL
INSERT INTO member_query_results
	( site_id, member_query_id, user_id )
VALUES

SQL;

		$buffer_size   = 0;
		$total_pages   = ceil( $total / $this->page_size );
		$current_page  = 1;
		$db            = $this->get_job_db();
		$current_query = $insert_query;
		$values_query  = ' (%d, %d, %s) ';
		$sentinel      = $this->get_sentinel();
		$insert_values = array(
			$this->get_site_id(),
			$this->get_member_query_id(),
			'',
		);

		for ( $i = 0; $i < $total; $i++ ) {
			$next_i           = $i + 1;
			$insert_values[2] = $user_ids[ $i ];
			$current_query   .= $db->prepare( $values_query, $insert_values );
			$buffer_size++;

			if ( $buffer_size >= $this->page_size || $next_i === $total ) {
				$db->query( $current_query );

				$progress = ceil( $current_page / $total_pages * 100 );
				$sentinel->set_task_progress( 'compile_results', $progress );

				$current_query = $insert_query;
				$buffer_size   = 0;
				$current_page++;
			} else if ( $next_i < $total ) {
				$current_query .= ', ';
			}
		}

		return $total;
	}

	function compile() {
		$profile_user_ids    = $this->get_users_for_store_type( 'profile' );
		$data_store_user_ids = $this->get_users_for_store_type( 'data_store' );
		$conjunction         = $this->get_conjunction();

		if ( $conjunction === 'and' ) {
			return $this->join_with_and( $profile_user_ids, $data_store_user_ids );
		} else if ( $conjunction === 'or' ) {
			return $this->join_with_or( $profile_user_ids, $data_store_user_ids );
		} else if ( $conjunction === 'any' ){
			return $this->join_with_any( $profile_user_ids, $data_store_user_ids );
		}
	}

	function join_with_and( $profile_user_ids, $data_store_user_ids ) {
		return array_intersect( $profile_user_ids, $data_store_user_ids );
	}

	function join_with_or( $profile_user_ids, $data_store_user_ids ) {
		return array_unique(
			array_merge( $profile_user_ids, $data_store_user_ids )
		);
	}

	function join_with_any( $profile_user_ids, $data_store_user_ids ) {
		$profile_count = count( $profile_user_ids );

		if ( $profile_count > 0 ) {
			return $profile_user_ids;
		} else {
			return $data_store_user_ids;
		}
	}

	function get_users_for_store_type( $store_type ) {
		$values = array(
			$this->get_site_id(),
			$this->get_member_query_id(),
		);

		$db       = $this->get_job_db();
		$query    = $this->get_query_for_users_for_store_type( $store_type );
		$query    = $db->prepare( $query, $values );
		$user_ids = $db->get_col( $query, 0 );

		return $user_ids;
	}

	function get_query_for_users_for_store_type( $store_type ) {
		$query = <<<SQL
SELECT
	user_id
FROM
	member_query_users
WHERE
	site_id = %d AND
	member_query_id = %d AND
	store_type = '$store_type'
SQL;

		return $query;
	}

	function get_job_db() {
		return TempDatabase::get_instance()->get_db();
	}

	function get_conjunction() {
		return $this->get_param( 'conjunction' );
	}

}
