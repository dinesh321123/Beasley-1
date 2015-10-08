<?php

namespace GreaterMedia\Gigya\Sync;

class QueryTaskTest extends \WP_UnitTestCase {

	public $task;

	function setUp() {
		parent::setUp();

		wp_async_task_clear();

		$this->post_id = $this->factory->post->create();
		$this->task = new QueryTask();
		$this->task->params = array(
			'member_query_id' => $this->post_id,
			'mode'            => 'preview',
			'site_id'         => 1,
			'checksum'        => 'foo-checksum',
			'query'           => 'select UID from accounts where profile.age = 93',
			'store_type'      => 'profile',
			'conjunction'     => 'and',
			'subquery_count'  => 2,
		);

		$this->task->get_sentinel()->set_checksum( 'foo-checksum' );
	}

	function init_gigya_keys() {
		$settings = array(
			'gigya_api_key' => '3_e_T7jWO0Vjsd9y0WJcjnsN6KaFUBv6r3VxMKqbitvw-qKfmaUWysQKa1fra5MTb6',
			'gigya_secret_key' => 'trS0ufXWUXZ0JBcpr/6umiRfgUiwT7YhJMQSDpUz/p8=',
		);

		$settings = json_encode( $settings );
		update_option( 'member_query_settings', $settings, true );
	}

	function tearDown() {
		parent::tearDown();
	}

	function test_it_has_task_name() {
		$this->assertEquals( 'query_task', $this->task->get_task_name() );
	}

	function test_it_has_query_to_run() {
		$expected = 'select UID from accounts where profile.age = 93';
		$this->assertEquals( $expected, $this->task->get_query() );
	}

	function test_it_has_a_default_cursor() {
		$actual = $this->task->get_cursor();
		$this->assertEquals( 0, $actual );
	}

	function test_it_uses_cursor_specified_if_present() {
		$this->task->params['cursor'] = 100;
		$actual = $this->task->get_cursor();
		$this->assertEquals( 100, $actual );
	}

	function test_it_has_a_store_type() {
		$this->assertEquals( 'profile', $this->task->get_store_type() );
	}

	function test_it_find_users_from_results() {
		$results = array(
			array( 'UID' => 'lorem' ),
			array( 'UID' => 'ipsum' ),
			array( 'UID' => 'dolor' ),
			array( 'UID' => 'sit' ),
			array( 'UID' => 'amet' ),
		);

		$actual = $this->task->find_users( $results );
		$expected = array(
			'lorem',
			'ipsum',
			'dolor',
			'sit',
			'amet',
		);

		$this->assertEquals( $expected, $actual );
	}

	function test_it_has_a_user_collector() {
		$actual = $this->task->get_collector();
		$this->assertInstanceOf( 'GreaterMedia\Gigya\Sync\UserCollector', $actual );
	}

	function test_it_can_collect_users() {
		$this->task->collector = new StubUserCollector();
		$this->task->save_users( array( 'a', 'b', 'c' ) );
		$this->assertEquals( array( 'a', 'b', 'c' ), $this->task->collector->users );
	}

	function test_it_can_fetch_first_page_of_profile_query() {
		$this->init_gigya_keys();

		$this->task->page_size = 10000;
		$matches = $this->task->run();

		$this->assertEquals( 43, $matches['total_results'] );
	}

	function test_it_saves_collected_users_to_collector() {
		$this->init_gigya_keys();
		$collector = $this->task->collector = new StubUserCollector();

		$this->task->page_size = 10000;
		$this->task->run();

		$actual = $collector->users;
		$this->assertEquals( 43, count( $actual ) );
	}

	function test_it_enqueues_next_page_in_after_hook() {
		$matches = array(
			'has_next' => true,
			'cursor' => 101,
		);

		$this->task->after( $matches );
		$task = wp_async_task_last_added();
		$this->assertEquals( 101, $task['params']['cursor'] );
	}

	function test_it_does_not_enqueue_next_page_if_last_page() {
		$matches = array(
			'has_next' => false,
		);

		$this->task->after( $matches );
		$actual = wp_async_task_count();
		$this->assertEquals( 0, $actual );
	}

	function test_it_enqueues_compilation_task_if_query_finished() {
		$this->task->get_sentinel()->params['conjunction'] = 'any';
		$matches = array(
			'has_next' => false,
			'progress' => 100,
		);

		$this->task->after( $matches );
		$actual = wp_async_task_last_added();
		$this->assertEquals( 'compile_results_async_job', $actual['action'] );
	}

	function test_it_fetches_pages_of_query_until_completion() {
		$this->init_gigya_keys();

		$db = TempDatabase::get_instance()->get_db();
		$db->delete( 'member_query_users', '1=1' );

		wp_async_task_autorun();

		$query_task = new QueryTask();
		$query_task->register();

		$this->task->page_size = 10;
		$this->task->execute( $this->task->params );

		$result = $db->get_row( "select count(*) as total from member_query_users where site_id = 1 and member_query_id = {$this->post_id};" );
		$total  = $result->total;

		$this->assertEquals( 43, $total );
	}

	function test_it_updates_task_progress_in_sentinel() {
		$this->init_gigya_keys();
		$this->task->execute( $this->task->params );

		$progress = $this->task->get_sentinel()->get_task_progress( 'profile' );
		$this->assertEquals( 100, $progress );
	}
}

class StubUserCollector {

	public $users;

	function collect( $users ) {
		$this->users = $users;
	}

}
