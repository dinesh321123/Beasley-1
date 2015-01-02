<?php

namespace GreaterMedia\Gigya\Sync;

class InMemoryCompileResultsTaskTest extends \WP_UnitTestCase {

	public $task;

	function setUp() {
		parent::setUp();

		$this->task = new InMemoryCompileResultsTask();
		$this->task->params = array(
			'member_query_id' => 11,
			'site_id' => 10,
			'mode' => 'preview',
			'checksum' => 'foo-checksum',
			'conjunction' => 'and',
		);
	}

	function tearDown() {
		$db = $this->task->get_job_db();
		$db->delete( 'member_query_results', '1=1' );
		$db->delete( 'member_query_users', '1=1' );

		parent::tearDown();
	}

	function insert_user_ids( $user_ids, $store_type = 'profile', $site_id = 10, $member_query_id = 11 ) {
		foreach ( $user_ids as $user_id ) {
			$this->insert_user(
				array(
					'site_id'         => $site_id,
					'member_query_id' => $member_query_id,
					'store_type'      => $store_type,
					'user_id'         => $user_id,
				)
			);
		}
	}

	function insert_user( $user ) {
		$formats = array( '%d', '%d', '%s', '%s' );
		$db = $this->task->get_job_db();
		$result = $db->insert(
			'member_query_users',
			$user,
			$formats
		);

		//var_dump( $user );
		//var_dump( $result );
	}

	function find_results_for( $site_id, $member_query_id ) {
		$db = $this->task->get_job_db();
		$query = $db->prepare(
			'select * from member_query_results where site_id = %d and member_query_id = %d order by user_id',
			array( $site_id, $member_query_id )
		);

		$results = $db->get_results( $query, ARRAY_A );
		return array_column( $results, 'user_id' );
	}

	function test_it_has_a_conjunction() {
		$actual = $this->task->get_conjunction();
		$this->assertEquals( 'and', $actual );
	}

	function test_it_has_a_job_db() {
		$actual = $this->task->get_job_db();
		$this->assertInstanceOf( 'wpdb', $actual );
	}

	function test_it_can_build_query_for_finding_users_for_profile_store() {
		$actual = $this->task->get_query_for_users_for_store_type( 'profile' );
		$this->assertContains( 'site_id = ', $actual );
		$this->assertContains( 'member_query_id = ', $actual );
		$this->assertContains( 'store_type = ', $actual );
	}

	function test_it_can_find_users_in_profile_store() {
		$this->insert_user_ids( array( 'a', 'b', 'c' ) );
		$actual = $this->task->get_users_for_store_type( 'profile' );
		$this->assertEquals( array( 'a', 'b', 'c' ), $actual );
	}

	function test_it_can_find_users_in_data_store() {
		$this->insert_user_ids( array( 'a', 'b', 'c' ), 'data_store' );
		$actual = $this->task->get_users_for_store_type( 'data_store' );
		$this->assertEquals( array( 'a', 'b', 'c' ), $actual );
	}

	function test_it_can_join_results_with_and_conjunction() {
		$a = array( 'a', 'b', 'c', 'd' );
		$b = array( 'a', 'b' );
		$actual = $this->task->join_with_and( $a, $b );

		$this->assertEquals( array( 'a', 'b' ), $actual );
	}

	function test_it_can_join_results_with_and_conjunction_larger_data_store() {
		$a = array( 'a', 'b' );
		$b = array( 'a', 'b', 'c', 'd' );
		$actual = $this->task->join_with_and( $a, $b );

		$this->assertEquals( array( 'a', 'b' ), $actual );
	}

	function test_it_can_join_results_with_or_conjunction() {
		$a = array( 'a', 'b', 'c', 'd' );
		$b = array( 'a', 'b', 'e', 'f' );
		$actual = $this->task->join_with_or( $a, $b );
		$actual = array_values( $actual );

		$this->assertEquals( array( 'a', 'b', 'c', 'd', 'e', 'f' ), $actual );
	}

	function test_it_can_join_results_with_any_conjunction() {
		$a = array( 'a', 'b', 'c', 'd' );
		$b = array();
		$actual = $this->task->join_with_or( $a, $b );
		$actual = array_values( $actual );

		$this->assertEquals( array( 'a', 'b', 'c', 'd' ), $actual );
	}

	function test_it_can_join_results_with_any_conjunction_larger_data_store() {
		$a = array();
		$b = array( 'a', 'b', 'c', 'd' );
		$actual = $this->task->join_with_or( $a, $b );
		$actual = array_values( $actual );

		$this->assertEquals( array( 'a', 'b', 'c', 'd' ), $actual );
	}

	function test_it_can_compile_profile_store_only_query() {
		$this->task->params['conjunction'] = 'any';
		$this->insert_user_ids( array( 'a', 'b', 'c' ) );
		$actual = $this->task->compile();
		$this->assertEquals( array( 'a', 'b', 'c' ), $actual );
	}

	function test_it_can_compile_data_store_only_query() {
		$this->task->params['conjunction'] = 'any';
		$this->insert_user_ids( array( 'a', 'b', 'c' ), 'data_store' );
		$actual = $this->task->compile();
		$this->assertEquals( array( 'a', 'b', 'c' ), $actual );
	}

	function test_it_can_compile_large_profile_and_small_data_store_query() {
		$this->task->params['conjunction'] = 'and';
		$this->insert_user_ids( array( 'a', 'b', 'c' ) );
		$this->insert_user_ids( array( 'a' ), 'data_store' );
		$actual = $this->task->compile();
		$this->assertEquals( array( 'a' ), $actual );
	}

	function test_it_can_compile_small_profile_and_large_data_store_query() {
		$this->task->params['conjunction'] = 'and';
		$this->insert_user_ids( array( 'a' ), 'profile' );
		$this->insert_user_ids( array( 'a', 'b', 'c' ), 'data_store' );
		$actual = $this->task->compile();
		$this->assertEquals( array( 'a' ), $actual );
	}

	function test_it_can_compile_large_profile_or_small_data_store_query() {
		$this->task->params['conjunction'] = 'or';
		$this->insert_user_ids( array( 'a', 'b', 'c' ) );
		$this->insert_user_ids( array( 'a', 'd', 'e' ), 'data_store' );
		$actual = $this->task->compile();
		$actual = array_values( $actual );
		$this->assertEquals( array( 'a', 'b', 'c', 'd', 'e' ), $actual );
	}

	function test_it_can_compile_small_profile_or_large_data_store_query() {
		$this->task->params['conjunction'] = 'or';
		$this->insert_user_ids( array( 'a', 'd', 'e' ), 'data_store' );
		$this->insert_user_ids( array( 'a' ), 'profile' );
		$actual = $this->task->compile();
		$actual = array_values( $actual );
		$this->assertEquals( array( 'a', 'd', 'e' ), $actual );
	}

	function test_it_can_save_user_ids_to_db() {
		$user_ids = range( 'a', 'z' );
		$total = $this->task->save_users( $user_ids );
		$this->assertEquals( 26, $total );

		$actual = $this->find_results_for( 10, 11 );
		$this->assertEquals( range( 'a', 'z' ), $actual );
	}

	function test_it_can_save_user_ids_to_db_in_pages() {
		//$page_size = 1000;
		//$max = 100000;
		$page_size = 100;
		$max = 2327;

		$this->task->page_size = $page_size;
		$user_ids = range( 1, $max );
		$total = $this->task->save_users( $user_ids );
		$this->assertEquals( $max, $total );

		$actual = $this->find_results_for( 10, 11 );
		sort( $actual );
		$expected = array_map( 'strval', range( 1, $max ) );
		$this->assertEquals( $expected, $actual );
	}

}
