<?php

namespace GreaterMedia\Gigya\Ajax;

use GreaterMedia\Gigya\GigyaSession;

class SaveGigyaActionAjaxHandlerTest extends \WP_UnitTestCase {

	public $handler;

	function setUp() {
		parent::setUp();

		GigyaSession::$instance = null;
		$this->handler = new SaveGigyaActionAjaxHandler();
	}

	function tearDown() {
		parent::tearDown();
	}

	function init_cookie( $data ) {
		$text = json_encode( $data );
		$_COOKIE['gigya_profile'] = base64_encode( $text );
	}

	function test_it_has_an_action_name() {
		$actual = $this->handler->get_action();
		$this->assertEquals( 'save_gigya_action', $actual );
	}

	function test_it_is_not_public() {
		$this->assertFalse( $this->handler->is_public() );
	}

	function test_it_will_not_save_action_if_not_logged_in_and_saving_as_logged_in_user() {
		$params = array(
			'action' => array(),
			'user_id' => 'logged_in_user',
		);

		$actual = $this->handler->run( $params );
		$this->assertFalse( $actual );
	}

	function test_it_will_raise_exception_for_invalid_action_schema() {
		$this->init_cookie( array( 'UID' => '37F5E08F-74D3-40FC-8F4B-296AD29DACBB', 'age' => 25 ) );
		$params = array(
			'action' => array(),
			'user_id' => 'logged_in_user',
		);

		$this->setExpectedException( 'Exception' );
		$actual = $this->handler->run( $params );
	}

	function test_it_will_send_action_to_publisher_if_valid() {
		$user_id = '37F5E08F-74D3-40FC-8F4B-296AD29DACBB';
		$this->init_cookie( array( 'UID' => $user_id, 'age' => 25 ) );
		$params = array(
			'action' => array(
				'actionType' => 'lorem',
				'actionID' => 'foo',
				'actionData' => array(
					array( 'name' => 'a1', 'value' => 'v1' ),
				),
			),
			'user_id' => 'logged_in_user',
		);

		$actual = $this->handler->run( $params );
		$task = wp_async_task_last_added();

		$this->assertEquals( $params['action'], $task['params']['actions'][0] );
		$this->assertEquals( $user_id, $task['params']['user_id'] );
	}

}
