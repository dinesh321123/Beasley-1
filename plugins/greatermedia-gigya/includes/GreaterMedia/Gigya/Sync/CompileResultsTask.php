<?php

namespace GreaterMedia\Gigya\Sync;

class CompileResultsTask extends SyncTask {

	public $page_size = 1000;

	function get_task_name() {
		return 'compile_results';
	}

	function run() {
		$select_query = $this->get_select_query();
		$count        = $this->count( $select_query );
		$query        = $this->get_compilation_query();

		if ( $count > 0 ) {
			$has_next = true;
			$cursor   = 0;
			$page     = 1;
			$pages    = ceil( $count / $this->page_size );

			while ( $has_next ) {
				$next_cursor = $cursor + $this->page_size;
				$progress    = ceil( ( $page ) / $pages * 100 );

				$this->insert_page( $cursor, $query );
				$this->get_sentinel()->set_task_progress(
					'compile_results', $progress
				);

				$page++;
				$cursor  += $this->page_size;
				$has_next = $cursor < $count;
			}
		}
	}

	function insert_page( $cursor, $query ) {
		$query = "$query LIMIT {$cursor}, {$this->page_size}";
		$db    = $this->get_job_db();

		$db->query( $query );
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

	function get_compilation_query( $cursor = 0 ) {
		$select_query = $this->get_select_query();
		$query = <<<SQL
INSERT INTO member_query_results
	(site_id, member_query_id, user_id)
{$select_query}
SQL;

		return $query;
	}

	function get_select_query() {
		if ( $this->get_conjunction() === 'and' ) {
			$query = $this->template_for_and_conjunction();
		} else if ( $this->get_conjunction() === 'or' ) {
			$query = $this->template_for_or_conjunction();
		} else {
			$query = $this->template_for_any_conjunction();
		}

		$db     = $this->get_job_db();
		$values = array(
			$this->get_site_id(),
			$this->get_member_query_id(),
		);

		return $db->prepare( $query, $values );
	}

	function template_for_or_conjunction() {
		$query = <<<SQL
SELECT
	site_id, member_query_id, user_id
FROM
	member_query_users
WHERE
	site_id = %d AND
	member_query_id = %d
GROUP BY
	user_id
ORDER BY
	user_id
SQL;

		return $query;
	}

	function template_for_and_conjunction() {
		$query = <<<SQL
SELECT
	a.site_id         AS site_id,
	a.member_query_id AS member_query_id,
	a.user_id         AS user_id
FROM
	member_query_users AS a
INNER JOIN
	member_query_users AS b
ON
	a.user_id = b.user_id
WHERE
	a.site_id = %d AND
	a.member_query_id = %d AND
	a.store_type      = 'profile' OR
	b.store_type      = 'data_store'
GROUP BY
	user_id
HAVING
	count(a.user_id) >= 2
ORDER BY
	user_id
SQL;

		return $query;
	}

	function count( $subquery ) {
		$db    = $this->get_job_db();
		$query = <<<SQL
SELECT COUNT(*) AS total
FROM ( $subquery ) AS temp_table;
SQL;

		return $db->get_var( $query );
	}

	function template_for_any_conjunction() {
		return $this->template_for_or_conjunction();
	}

	function get_job_db() {
		return TempDatabase::get_instance()->get_db();
	}

	function get_conjunction() {
		return $this->get_param( 'conjunction' );
	}

}
