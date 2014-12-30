<?php

namespace GreaterMedia\Gigya\Sync;

class ResultPaginator {

	public $site_id;
	public $member_query_id;
	public $page_size;

	function __construct( $site_id, $member_query_id, $page_size ) {
		$this->site_id         = $site_id;
		$this->member_query_id = $member_query_id;
		$this->page_size       = $page_size;
	}

	function count() {
		$db    = $this->get_job_db();
		$query = $this->get_count_query();

		return $db->get_var( $query );
	}

	function fetch( $cursor = 0 ) {
		$db = $this->get_job_db();
		$query = $this->query_for( $cursor );

		return $db->get_col( $query );
	}

	function query_for( $cursor ) {
		$query = <<<SQL
SELECT user_id
FROM member_query_results
WHERE
	site_id = %d and
	member_query_id = %d
LIMIT %d, %d;
SQL;

		$db     = $this->get_job_db();
		$params = array(
			$this->site_id,
			$this->member_query_id,
			$cursor,
			$this->page_size,
		);

		return $db->prepare( $query, $params );
	}

	function get_count_query() {
		$query = <<<SQL
SELECT count(user_id) as total
FROM member_query_results
WHERE
	site_id = %d and
	member_query_id = %d;
SQL;

		$db     = $this->get_job_db();
		$params = array(
			$this->site_id,
			$this->member_query_id,
		);

		return $db->prepare( $query, $params );
	}

	function get_job_db() {
		return TempDatabase::get_instance()->get_db();
	}

}


