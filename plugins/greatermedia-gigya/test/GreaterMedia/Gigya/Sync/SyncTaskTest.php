<?php

namespace GreaterMedia\Gigya\Sync;

class SyncTaskTest extends \WP_UnitTestCase {

	public $task;

	function setUp() {
		parent::setUp();

		$this->task = new SyncTask();
		$this->task->params = array(
			'member_query_id' => 1,
			'mode' => 'export',
			'checksum' => 'foo-checksum'
		);

	}

	function tearDown() {
		parent::tearDown();
	}

	function test_it_has_a_lazy_loaded_sentinel() {
		$this->assertNull( $this->task->sentinel );
		$this->assertNotNull( $this->task->get_sentinel() );
	}

	function test_it_gives_sentinel_its_own_params() {
		$this->assertEquals( $this->task->params, $this->task->get_sentinel()->params );
	}

	function test_it_knows_its_member_query_id() {
		$this->assertEquals( 1, $this->task->get_member_query_id() );
	}

	function test_it_knows_its_mode() {
		$this->assertEquals( 'export', $this->task->get_mode() );
	}

	function test_it_knows_its_checksum() {
		$this->assertEquals( 'foo-checksum', $this->task->get_checksum() );
	}

	function test_it_knows_if_checksum_is_not_valid() {
		$this->assertFalse( $this->task->verify_checksum() );
	}

	function test_it_knows_if_checksum_is_valid() {
		$this->task->get_sentinel()->set_checksum( 'foo-checksum' );
		$this->assertTrue( $this->task->verify_checksum() );
	}

	function test_it_verifies_checksum_in_before_hook() {
		$this->task->get_sentinel()->set_checksum( 'foo-checksum' );
		$this->assertTrue( $this->task->before() );

		$this->task->get_sentinel()->set_checksum( 'bar' );
		$this->assertFalse( $this->task->before() );
	}

	function test_it_aborts_task_if_checksum_is_invalid() {
		$task = new SyncTask();
		$task->register();
		$task->enqueue( $this->task->params );

		wp_async_task_run_last();
		$this->assertTrue( $task->aborted );
	}

	function test_it_does_not_abort_task_if_checksum_is_valid() {
		$params = $this->task->params;
		$params['checksum'] = 'foo-checksum';

		$this->task->get_sentinel()->set_checksum( 'foo-checksum' );

		$task = new SyncTask();
		$task->register();
		$task->enqueue( $params );

		wp_async_task_run_last();
		$this->assertFalse( $task->aborted );
	}

	function test_it_stores_error_messages_in_sentinel_on_failure() {
		$params = $this->task->params;
		$params['checksum'] = 'foo-checksum';

		$sentinel = $this->task->get_sentinel();
		$sentinel->set_checksum( 'foo-checksum' );

		$task = new SyncTask();
		$task->register();
		$task->enqueue( $params );

		$error = new \Exception( 'foo' );
		$task->fail( $error );

		$this->assertTrue( $sentinel->has_errors() );
		$this->assertEquals( array( 'foo' ), $sentinel->get_errors() );
	}
}
