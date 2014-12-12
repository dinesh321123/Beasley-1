<?php

namespace GreaterMedia\Gigya\Sync;

class ResultPaginatorTest extends \WP_UnitTestCase {

	public $paginator;

	function setUp() {
		parent::setUp();

		$this->paginator = new ResultPaginator( 10, 20, 10 );
	}

	function tearDown() {
		$db = $this->paginator->get_job_db();
		$db->delete( 'member_query_results', '1=1' );

		parent::tearDown();
	}

	function init_results() {
		$db = $this->paginator->get_job_db();
		$formats = array( '%d', '%d', '%s' );

		foreach ( range( 'a', 'z' ) as $letter ) {
			$values = array(
				'site_id'         => 10,
				'member_query_id' => 20,
				'user_id'         => $letter,
			);
			$db->insert( 'member_query_results', $values, $formats );
		}
	}

	function test_it_stores_the_result_site_id() {
		$actual = $this->paginator->site_id;
		$this->assertEquals( 10, $actual );
	}

	function test_it_stores_the_result_member_query_id() {
		$actual = $this->paginator->member_query_id;
		$this->assertEquals( 20, $actual );
	}

	function test_it_stores_the_page_size() {
		$actual = $this->paginator->page_size;
		$this->assertEquals( 10, $actual );
	}

	function test_it_stores_has_a_job_db(){
		$db = $this->paginator->get_job_db();
		$this->assertInstanceOf( 'wpdb', $db );
	}

	function test_it_can_build_query_for_cursor() {
		$query = $this->paginator->query_for( 50 );

		$this->assertContains( 'site_id = 10', $query );
		$this->assertContains( 'member_query_id = 20', $query );
		$this->assertContains( 'LIMIT 50, 10', $query );
	}

	function test_it_can_fetch_results_for_default_cursor() {
		$this->init_results();
		$actual   = $this->paginator->fetch();
		$expected = range( 'a', 'j' );

		$this->assertEquals( $expected, $actual );
		$this->assertEquals( 10, count( $actual ) );
	}

	function test_it_can_fetch_results_for_specified_cursor() {
		$this->init_results();
		$actual   = $this->paginator->fetch( 10 );
		$expected = range( 'k', 't' );

		$this->assertEquals( $expected, $actual );
		$this->assertEquals( 10, count( $actual ) );
	}

	function test_it_can_fetch_last_page_of_results() {
		$this->init_results();
		$actual = $this->paginator->fetch( 20 );
		$expected = range( 'u', 'z' );

		$this->assertEquals( $expected, $actual );
		$this->assertEquals( 6, count( $actual ) );
	}

	function test_it_can_fetch_count_of_total_results() {
		$this->init_results();
		$actual = $this->paginator->count();

		$this->assertEquals( 26, $actual );
	}

}
