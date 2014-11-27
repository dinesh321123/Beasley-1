<?php

namespace GreaterMedia\Gigya\Sync;

class CompileResultsTask extends SyncTask {

	function get_task_name() {
		return 'compile_results';
	}

	function run() {
		$query = $this->get_compilation_query();
		$db    = $this->get_job_db();

		$db->query( $query );
	}

	function after( $result ) {

	}

	function get_compilation_query() {
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
		} else {
			$query = $this->template_for_or_conjunction();
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
	user_id;
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
	count(a.user_id) >= 2;
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
