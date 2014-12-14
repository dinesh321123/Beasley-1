<?php

namespace GreaterMedia\Gigya\Action;

use GreaterMedia\Gigya\GigyaSession;

class ActionDispatcherTest extends \WP_UnitTestCase {

	public $dispatcher;
	public $action;

	function setUp() {
		parent::setUp();

		GigyaSession::$instance = null;
		$this->dispatcher = new Dispatcher();
		$this->action = array(
			'actionType' => 'action:contest',
			'actionID' => '1',
			'actionData' => array(
				array( 'name' => 'a1', 'value' => 'a1v' ),
			),
		);
	}

	function tearDown() {
		parent::tearDown();
		wp_async_task_clear();
	}

	function init_cookie( $data ) {
		$text = json_encode( $data );
		$_COOKIE['gigya_profile'] = base64_encode( $text );
	}

	function test_it_can_split_actions_into_pages() {
		$actions = range( 'a', 'z' );
		$this->dispatcher->page_size = 10;
		$actual = $this->dispatcher->actions_to_pages( $actions );

		$this->assertEquals( range( 'a', 'j' ), $actual[0] );
		$this->assertEquals( range( 'k', 't' ), $actual[1] );
		$this->assertEquals( range( 'u', 'z' ), $actual[2] );
	}

	function test_it_knows_if_current_user_is_a_guest() {
		$actual = $this->dispatcher->get_user_id();
		$this->assertEquals( 'guest', $actual );
	}

	function test_it_can_build_publisher_params_for_anonymous_user() {
		$actions = range( 'a', 'z' );
		$actual  = $this->dispatcher->params_for_page( $actions );

		$this->assertEquals( 'guest', $actual['user_id'] );
		$this->assertEquals( $actions, $actual['actions'] );
	}

	function test_it_can_build_publisher_params_for_logged_in_user() {
		$this->init_cookie( array( 'UID' => 'foo', 'age' => 25 ) );

		$actions = range( 'a', 'z' );
		$actual  = $this->dispatcher->params_for_page( $actions, 'logged_in_user' );

		$this->assertEquals( 'foo', $actual['user_id'] );
		$this->assertEquals( $actions, $actual['actions'] );
	}

	function test_it_can_enqueue_publisher_task_on_publish() {
		$actions = array(
			$this->action
		);

		$this->dispatcher->publish( $actions );
		$actual = wp_async_task_last_added();

		$this->assertEquals( 'action_publisher_async_job', $actual['action'] );
		$this->assertEquals( $actions, $actual['params']['actions'] );
	}

	function test_it_can_enqueue_publisher_task_on_single_save_action() {
		$action = $this->action;

		$this->dispatcher->save_action( $action );
		$actual = wp_async_task_last_added();

		$this->assertEquals( 'action_publisher_async_job', $actual['action'] );
		$this->assertEquals( array( $action ), $actual['params']['actions'] );
	}

	function test_it_can_enqueue_publisher_task_on_batch_save_actions() {
		$actions = array();
		for ( $i = 0; $i < 30; $i++ ) {
			$actions[] = $this->action;
		}

		$this->dispatcher->page_size = 10;
		$this->dispatcher->save_actions( $actions );
		$actual = wp_async_task_last_added();

		$this->assertEquals( 'action_publisher_async_job', $actual['action'] );
		$this->assertEquals( 3, wp_async_task_count() );
	}

	function test_it_has_a_static_instance() {
		$instance = Dispatcher::get_instance();
		$instance2 = Dispatcher::get_instance();

		$this->assertSame( $instance, $instance2 );
	}

	// api helpers
	function test_it_has_api_helper_to_enqueue_publisher_task_on_single_save_action() {
		$action = $this->action;

		save_gigya_action( $action );
		$actual = wp_async_task_last_added();

		$this->assertEquals( 'action_publisher_async_job', $actual['action'] );
		$this->assertEquals( array( $action ), $actual['params']['actions'] );
	}

	function test_it_has_api_helper_to_enqueue_publisher_task_on_batch_save_actions() {
		$actions = array();
		for ( $i = 0; $i < 30; $i++ ) {
			$actions[] = $this->action;
		}

		get_gigya_action_dispatcher()->page_size = 10;
		save_gigya_actions( $actions );
		$actual = wp_async_task_last_added();

		$this->assertEquals( 'action_publisher_async_job', $actual['action'] );
		$this->assertEquals( 3, wp_async_task_count() );
	}
}
