<?php

namespace GreaterMedia\Gigya\Sync;

class CompileResultsTaskTest extends \WP_UnitTestCase {

	public $task;

	function setUp() {
		parent::setUp();

		$this->task = new CompileResultsTask();
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

	function test_it_has_a_conjunction() {
		$actual = $this->task->get_conjunction();
		$this->assertEquals( 'and', $actual );
	}

	function test_it_has_a_job_db() {
		$actual = $this->task->get_job_db();
		$this->assertInstanceOf( 'wpdb', $actual );
	}

	function test_it_has_template_for_and_conjunction() {
		$actual = $this->task->template_for_and_conjunction();
		$this->assertNotEmpty( $actual );
	}

	function test_it_has_template_for_or_conjunction() {
		$actual = $this->task->template_for_or_conjunction();
		$this->assertNotEmpty( $actual );
	}

	function test_it_can_build_select_query_for_and_conjunction() {
		$query = $this->task->get_select_query();
		$this->assertContains( 'a.site_id = 10', $query );
		$this->assertContains( 'a.member_query_id = 11', $query );
	}

	function test_it_can_build_select_query_for_or_conjunction() {
		$this->task->params['conjunction'] = 'or';
		$query = $this->task->get_select_query();

		$this->assertContains( 'site_id = 10', $query );
		$this->assertContains( 'member_query_id = 11', $query );
	}

	function test_it_can_build_compilation_query() {
		$query = $this->task->get_compilation_query();

		$this->assertContains( 'INSERT INTO member_query_results', $query );
		$this->assertContains( 'a.site_id = 10', $query );
		$this->assertContains( 'a.member_query_id = 11', $query );
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

	function find_results_for( $site_id, $member_query_id ) {
		$db = $this->task->get_job_db();
		$query = $db->prepare(
			'select * from member_query_results where site_id = %d and member_query_id = %d order by user_id',
			array( $site_id, $member_query_id )
		);

		$results = $db->get_results( $query, ARRAY_A );
		return array_column( $results, 'user_id' );
	}

	function test_it_can_combine_profile_or_data_store_users() {
		$this->insert_user_ids(
			array( 'a', 'b', 'c', 'd', 'e' )
		);

		$this->insert_user_ids(
			array( 'f', 'g', 'h' ), 'data_store'
		);

		$this->task->params['conjunction'] = 'or';
		$this->task->run();

		$actual = $this->find_results_for( 10, 11 );
		$expected = array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h' );
		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_combine_profile_and_data_store_users() {
		$this->insert_user_ids(
			array( 'a', 'b', 'c', 'd', 'e' )
		);

		$this->insert_user_ids(
			array( 'a', 'e', 'd' ), 'data_store'
		);

		$this->task->params['conjunction'] = 'and';
		$this->task->run();

		$actual = $this->find_results_for( 10, 11 );
		$expected = array( 'a', 'd', 'e' );
		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_compile_only_profile_users() {
		$this->insert_user_ids(
			array( 'a', 'b', 'c', 'd', 'e' )
		);

		$this->task->params['conjunction'] = 'any';
		$this->task->run();

		$actual = $this->find_results_for( 10, 11 );
		$expected = array( 'a', 'b', 'c', 'd', 'e' );
		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_compile_only_data_store_users() {
		$this->insert_user_ids(
			array( 'a', 'b', 'd', 'e' ), 'data_store'
		);

		$this->task->params['conjunction'] = 'any';
		$this->task->run();

		$actual = $this->find_results_for( 10, 11 );
		$expected = array( 'a', 'b', 'd', 'e' );
		$this->assertEquals( $expected, $actual );
	}

	function test_it_updates_task_progress_in_after_hook() {
		$this->task->after( null );

		$actual = $this->task->get_sentinel()->get_task_progress( 'compile_results' );
		$this->assertEquals( 100, $actual );
	}

	function test_it_enqueues_preview_task_in_after_hook_if_in_preview_mode() {
		$this->task->params['mode'] = 'preview';
		$this->task->after( null );

		$actual = wp_async_task_last_added();
		$this->assertEquals( 'preview_results_async_job', $actual['action'] );
	}

	function test_it_enqueues_export_task_in_after_hook_if_in_standard_mode() {
		$this->task->params['mode'] = 'export';
		$this->task->after( null );

		$actual = wp_async_task_last_added();
		$this->assertEquals( 'export_results_async_job', $actual['action'] );
	}
}
