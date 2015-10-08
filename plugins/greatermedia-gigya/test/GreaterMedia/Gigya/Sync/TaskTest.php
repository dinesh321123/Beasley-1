<?php

namespace GreaterMedia\Gigya\Sync;

class BaseTaskTest extends \WP_UnitTestCase {

	public $task;

	function setUp() {
		parent::setUp();

		wp_async_task_runner()->clear();
		$this->task = new Task();
	}

	function tearDown() {
		parent::tearDown();
	}

	function test_it_has_a_task_name() {
		$this->assertNotEmpty( $this->task->get_task_name() );
	}

	function test_it_has_an_async_job_name() {
		$pattern = '/^.*_async_job$/';
		$actual  = $this->task->get_async_action();
		$this->assertRegExp( $pattern, $actual );
	}

	function test_it_subscribes_to_async_job_on_registration() {
		$this->task->register();
		$this->assertEquals( 2, has_action( 'task_async_job' ) );
	}

	function test_it_can_log_first_attempt() {
		$this->task->log_attempt();
		$this->assertEquals( 1, $this->task->get_param( 'retries' ) );
	}

	function test_it_can_log_subsequent_attempts() {
		$this->task->log_attempt();
		$this->task->log_attempt();
		$this->task->log_attempt();
		$this->assertEquals( 3, $this->task->get_param( 'retries' ) );
	}

	function test_it_knows_if_param_does_not_exist() {
		$this->assertFalse( $this->task->has_param( 'foo' ) );
	}

	function test_it_knows_if_param_exists() {
		$this->task->set_param( 'foo', 'bar' );
		$this->assertTrue( $this->task->has_param( 'foo' ) );
	}

	function test_it_knows_value_for_param_key() {
		$this->task->set_param( 'foo', 'bar' );
		$this->assertEquals( 'bar', $this->task->get_param( 'foo' ) );
	}

	function test_it_has_no_retries_initially() {
		$actual = $this->task->get_retries();
		$this->assertEquals( 0, $actual );
	}

	function test_it_has_retries_after_an_attempt() {
		$this->task->log_attempt();
		$this->assertEquals( 1, $this->task->get_retries() );
	}

	function test_it_will_not_retry_if_retries_are_disabled() {
		$this->task->max_retries = 0;
		$this->assertFalse( $this->task->can_retry() );
	}

	function test_it_can_retry_if_less_than_max_retries() {
		$this->task->log_attempt();
		$this->assertTrue( $this->task->can_retry() );
	}

	function test_it_will_not_retry_if_equal_to_max_retries() {
		$this->task->log_attempt();
		$this->task->log_attempt();
		$this->task->log_attempt();
		$this->assertFalse( $this->task->can_retry() );
	}

	function test_it_will_not_retry_if_greater_than_max_retries() {
		$this->task->log_attempt();
		$this->task->log_attempt();
		$this->task->log_attempt();
		$this->task->log_attempt();
		$this->assertFalse( $this->task->can_retry() );
	}

	function test_it_will_store_params_on_enqueue() {
		$params = array( 'member_query_id' => 10 );
		$this->task->enqueue( $params );
		$this->assertEquals( $params, $this->task->params );
	}

	function test_it_adds_task_to_queue_on_enqueue() {
		$params = array( 'member_query_id' => 1 );
		$task_id = $this->task->enqueue( $params );

		$actual = wp_async_task_last_added();
		$this->assertEquals( $task_id, $actual[ 'task_id' ] );
	}

	function test_it_can_be_executed() {
		$params = array( 'foo' => 'bar' );
		$this->task->register();
		$task_id = $this->task->enqueue( $params );

		wp_async_task_run( $task_id );

		$this->assertEquals( 'bar', $this->task->get_param( 'foo' ) );
	}

	function test_it_will_not_proceed_if_before_hook_return_false() {
		$task = new BeforeTask( false );
		$task->register();

		wp_async_task_run( $task->enqueue() );

		$this->assertTrue( $task->aborted );
	}

	function test_it_will_proceed_if_before_hook_return_true() {
		$task = new BeforeTask( true );
		$task->register();

		wp_async_task_run( $task->enqueue() );

		$this->assertFalse( $task->aborted );
	}

	function test_it_will_log_attempt_if_task_was_not_aborted() {
		$task = new BeforeTask( true );
		$task->register();

		wp_async_task_run( $task->enqueue() );

		$this->assertEquals( 1, $task->get_retries() );
	}

	function test_it_will_attempt_upto_max_retries_on_errors() {
		$task = new ErrorTask();
		$task->register();

		wp_async_task_autorun();
		$task->enqueue();

		$this->assertEquals( 3, $task->get_retries() );
	}

	function test_it_will_attempt_at_least_min_times() {
		$task = new MinRetriesTask( 4 );
		$task->max_retries = 10;
		$task->register();

		wp_async_task_autorun();
		$task->enqueue();

		$this->assertEquals( 4, $task->get_retries() );
	}

	function test_it_can_fetch_pages_one_at_a_time() {
		$params = array(
			'start'     => 1,
			'page_size' => 10,
			'total'       => 55,
		);

		$task = new PagerTask();
		$task->register();

		wp_async_task_autorun();
		$task->enqueue( $params );

		$expected = range( $params['start'], $params['total'] );
		$this->assertEquals( $expected, $task->results );
	}

	function test_it_can_fetch_single_page_of_small_task() {
		$params = array(
			'start'     => 1,
			'page_size' => 10,
			'total'       => 5,
		);

		$task = new PagerTask();
		$task->register();

		wp_async_task_autorun();
		$task->enqueue( $params );

		$expected = range( $params['start'], $params['total'] );
		$this->assertEquals( $expected, $task->results );
	}

	function test_it_can_fetch_pages_using_cursors() {
		$params = array(
			'cursor'     => 'a',
		);

		$task = new CursorPagerTask();
		$task->max_retries = 10;
		$task->register();

		wp_async_task_autorun();
		$task->enqueue( $params );

		$expected = array(
			'a1', 'a2', 'a3', 'b1', 'b2', 'b3', 'c1', 'c2', 'c3',
		);

		$this->assertEquals( $expected, $task->results );
		$this->assertEquals( 5 + 2, $task->get_retries() );
	}

	function test_it_will_abort_task_if_checksums_dont_match() {
		$params = array(
			'checksum' => 'foo',
		);

		$task = new ChecksumTask( 'bar' );
		$task->register();

		wp_async_task_run( $task->enqueue( $params ) );

		$this->assertTrue( $task->aborted );
	}

	function test_it_will_not_abort_task_if_checksums_dont_match() {
		$params = array(
			'checksum' => 'foo',
		);

		$task = new ChecksumTask( 'foo' );
		$task->register();

		wp_async_task_run( $task->enqueue( $params ) );

		$this->assertFalse( $task->aborted );
	}

	function test_it_knows_it_cannot_log_unknown_message_type() {
		$this->assertFalse( $this->task->can_log( 'foo' ) );
	}

	function test_it_knows_it_should_not_log_messages_if_not_doing_async() {
		$this->task->message_types = array( 'foo' );
		$this->assertFalse( $this->task->can_log( 'foo' ) );
	}

	function test_it_knows_it_should_not_log_messages_if_not_a_allowed_message_type() {
		$this->assertFalse( $this->task->can_log( 'foo' ) );
	}

	function test_it_knows_it_should_log_message_if_async_and_allowed_message_type() {
		define( 'DOING_ASYNC', true );
		$this->task->message_types = array( 'foo' );
		$this->assertTrue( $this->task->can_log( 'foo' ) );
	}

	function test_it_can_log_a_message() {
		$this->task->params = array(
			'member_query_id' => 1,
			'store_type' => 'profile',
		);

		$this->task->message_types = array( 'hello' );
		$this->task->log( 'hello', 'one', array( 'two' ), array( 'three' => 3 ) );
	}

	function test_it_can_export_params_without_internals() {
		$params = array(
			'start'     => 1,
			'page_size' => 10,
			'total'     => 55,
			'retries'   => 5,
		);

		$task = new PagerTask();
		$task->register();

		wp_async_task_autorun();
		$task->enqueue( $params );

		$actual = $task->export_params();
		$this->assertFalse( array_key_exists( 'retries', $actual ) );
	}

	function test_it_knows_if_task_did_not_fail() {
		$params = array( 'foo' => 'bar' );
		$this->task->register();
		$task_id = $this->task->enqueue( $params );

		wp_async_task_run( $task_id );

		$this->assertFalse( $this->task->did_fail() );
	}

	function test_it_knows_if_task_failed() {
		$task = new ErrorTask();
		$task->register();

		wp_async_task_autorun();
		$task->enqueue();

		$this->assertTrue( $task->did_fail() );
	}

	function test_it_knows_reason_of_task_failure() {
		$task = new ErrorTask();
		$task->register();

		wp_async_task_autorun();
		$task->enqueue();

		$this->assertEquals( 'FooException', $task->failure->getMessage() );
	}

}

class BeforeTask extends Task {

	public $before_result;

	function __construct( $before_result ) {
		$this->before_result = $before_result;
	}

	function before() {
		return $this->before_result;
	}

}

class ErrorTask extends Task {

	function run() {
		//error_log( "ErrorTask: attempt = " . $this->get_retries() );
		throw new \Exception( 'FooException' );
	}

}

class MinRetriesTask extends Task {

	public $min_retries = 10;

	function __construct( $min_retries ) {
		$this->min_retries = $min_retries;
	}

	function run() {
		//error_log( "ErrorTask: attempt = " . $this->get_retries() );
		if ( $this->get_retries() === $this->min_retries ) {
			// success
		} else {
			throw new \Exception( 'FooException' );
		}
	}

}

class PagerTask extends Task {

	public $results = array();

	function run() {
		$start = $this->get_param( 'start' );
		$end   = $start + $this->get_param( 'page_size' ) - 1;
		$end   = min( $end, $this->get_param( 'total' ) );
		$items = range( $start, $end );

		//$count_items = json_encode( $items );
		//error_log( "run: {$start} - {$end} {$count_items}\n" );

		$this->results = array_merge( $this->results, $items );
	}

	function after( $result ) {
		if ( count( $this->results ) !== $this->get_param( 'total' ) ) {
			$params = $this->params;
			$params['start'] = $params['start'] + $params['page_size'];
			$this->enqueue( $params );
	   	}
	}

}

class CursorPagerTask extends Task {

	public $pages = array(
		'a' => array( 'a1', 'a2', 'a3' ),
		'b' => array( 'b1', 'b2', 'b3' ),
		'c' => array( 'c1', 'c2', 'c3' ),
	);

	public $results = array();

	function run() {
		$cursor = $this->get_param( 'cursor' );
		if ( $cursor === 'b' && $this->get_retries() <= 5 ) {
			throw new \Exception( 'FooException' );
		}

		$page = $this->pages[ $cursor ];
		$this->results = array_merge( $this->results, $page );
	}

	function after( $result ) {
		$new_params = $this->params;
		$cursor     = $this->params['cursor'];

		if ( $cursor === 'a' ) {
			$new_params['cursor'] = 'b';
			$this->enqueue( $new_params );
		} else if ( $cursor === 'b' ) {
			$new_params['cursor'] = 'c';
			$this->enqueue( $new_params );
		}
	}

}

class ChecksumTask extends Task {

	public $checksum;

	function __construct( $checksum ) {
		$this->checksum = $checksum;
	}

	function before() {
		return $this->checksum === $this->get_param( 'checksum' );
	}

}
