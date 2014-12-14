<?php

namespace GreaterMedia\Gigya\Action;

class ContestEntryDispatcherTest extends \WP_UnitTestCase {

	public $dispatcher;

	function setUp() {
		parent::setUp();

		$this->dispatcher = new ContestEntryDispatcher();
	}

	function tearDown() {
		parent::tearDown();
	}

	function test_it_can_build_action_data_from_entry_reference() {
		$entry_reference = array(
			'c1' => 'a',
			'c2' => 'b',
			'c3' => 'c',
		);

		$actual = $this->dispatcher->action_data_for_entry_reference( $entry_reference );
		$expected = array(
			array( 'name' => 'c1', 'value' => 'a' ),
			array( 'name' => 'c2', 'value' => 'b' ),
			array( 'name' => 'c3', 'value' => 'c' ),
		);

		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_build_action_from_entry_object() {
		$entry = new \stdClass();
		$entry->post = new \stdClass();
		$entry->post->post_parent = 10;
		$entry->entry_reference = array(
			'c1' => 'a',
			'c2' => 'b',
			'c3' => 'c',
		);

		$actual = $this->dispatcher->action_for_entry( $entry );
		$expected = array(
			'actionType' => 'action:contest',
			'actionID' => 10,
			'actionData' => array(
				array( 'name' => 'c1', 'value' => 'a' ),
				array( 'name' => 'c2', 'value' => 'b' ),
				array( 'name' => 'c3', 'value' => 'c' ),
			),
		);

		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_hook_into_contest_entry_saves() {
		$this->dispatcher->register();
		$this->assertEquals( 2, has_action( 'greatermedia_contest_entry_save' ) );
	}

	function test_it_can_publish_contest_entry_on_save() {
		$entry = new \stdClass();
		$entry->post = new \stdClass();
		$entry->post->post_parent = 10;
		$entry->entry_reference = array(
			'c1' => 'a',
			'c2' => 'b',
			'c3' => 'c',
		);

		$this->dispatcher->register();
		do_action( 'greatermedia_contest_entry_save', $entry );

		$task = wp_async_task_last_added();
		$expected = array(
			'actionType' => 'action:contest',
			'actionID' => 10,
			'actionData' => array(
				array( 'name' => 'c1', 'value' => 'a' ),
				array( 'name' => 'c2', 'value' => 'b' ),
				array( 'name' => 'c3', 'value' => 'c' ),
			),
		);

		$this->assertEquals( 'action_publisher_async_job', $task['action'] );
		$this->assertEquals( $expected, $task['params']['actions'][0] );
	}

}
