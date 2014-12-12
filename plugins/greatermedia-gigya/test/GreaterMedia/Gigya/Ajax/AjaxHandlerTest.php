<?php

namespace GreaterMedia\Gigya\Ajax;

class AjaxHandlerTest extends \WP_UnitTestCase {

	public $handler;

	function setUp() {
		parent::setUp();

		$this->handler = new ConcreteAjaxHandler();
	}

	function test_it_is_admin_only_by_default() {
		$this->assertFalse( $this->handler->is_public() );
	}

	function test_it_is_not_async_by_default() {
		$this->assertFalse( $this->handler->is_async() );
	}

	function test_it_has_an_action_name() {
		$this->assertEquals( 'concrete', $this->handler->get_action() );
	}

	function test_it_knows_wordpress_ajax_action_name() {
		$actual = $this->handler->get_action_to_register();
		$this->assertEquals( 'wp_ajax_concrete', $actual );
	}

	function test_it_has_valid_nonce_name() {
		$actual = $this->handler->get_nonce_name();
		$this->assertEquals( 'concrete_nonce', $actual );
	}

	function test_it_has_blank_nonce_value_if_absent_in_GET() {
		$actual = $this->handler->get_nonce_value();
		$this->assertEmpty( $actual );
	}

	/* Using _REQUEST, because the target code uses _REQUEST, and using
	 * _GET or _POST here does not update the _REQUEST global */
	function test_it_has_valid_nonce_value_if_present_in_GET() {
		$_REQUEST['concrete_nonce'] = 'foo';
		$actual = $this->handler->get_nonce_value();
		$this->assertEquals( 'foo', $actual );
	}

	function test_it_has_valid_nonce_value_if_present_in_POST() {
		$_REQUEST['concrete_nonce'] = 'foo';
		$actual = $this->handler->get_nonce_value();
		$this->assertNotEmpty( $actual );
	}

	function test_it_does_not_quit_immediately_under_php_unit() {
		$this->handler->quit();
	}

	function test_it_can_send_json_success_data() {
		$this->handler->send_json_success( 'foo' );
		$this->assertEquals( 'foo', $this->handler->sent_json );
	}

	function test_it_can_send_json_error_data() {
		$this->handler->send_json_error( 'foo' );
		$this->assertEquals( 'foo', $this->handler->sent_json );
	}

	function test_it_knows_blank_nonce_is_not_valid() {
		ob_start();
		$this->assertFalse( $this->handler->authorize() );
		ob_get_clean();
	}

	function test_it_knows_invalid_nonce_is_not_valid() {
		ob_start();
		$_GET['concrete_nonce'] = 'foo';
		$this->assertFalse( $this->handler->authorize() );
		ob_get_clean();
	}

	function test_it_knows_new_nonce_is_valid() {
		wp_set_current_user(1);
		$_REQUEST['concrete_nonce'] = wp_create_nonce( 'concrete' );
		$actual = $this->handler->authorize();
		$this->assertTrue( $actual );
	}

	function test_it_returns_empty_array_if_action_data_is_absent() {
		$params = $this->handler->get_params();
		$this->assertEquals( array(), $params );
	}

	function test_it_throws_exception_if_invalid_params_were_sent() {
		$_POST['action_data'] = '{ foo }';
		$this->setExpectedException( 'Exception' );
		$params = $this->handler->get_params();
		$this->assertEquals( array(), $params );
	}

	function test_it_returns_array_of_params_if_specified_in_POST() {
		$_POST['action_data'] = '{ "a": 1, "b": "foo" }';
		$params = $this->handler->get_params();

		$this->assertEquals( array( 'a' => 1, 'b' => 'foo' ), $params );
	}

	function test_it_correctly_handles_wordpress_slashed_post_params() {
		$_POST['action_data'] = wp_slash( '{ "a": "lorem \"ipsum\" dolor" }' );
		$params = $this->handler->get_params();

		$this->assertEquals( array( 'a' => 'lorem "ipsum" dolor' ), $params );
	}

	function test_it_knows_if_current_user_does_not_have_capabilities() {
		$this->assertFalse( $this->handler->has_capabilities() );
	}

	function test_it_knows_if_current_user_has_capabilities() {
		wp_set_current_user( 1 );
		$this->assertTrue( $this->handler->has_capabilities() );
	}

	function test_it_knows_public_ajax_handler_does_not_need_to_check_permissions() {
		$this->handler = new PublicAjaxHandler();
		$this->assertTrue( $this->handler->has_permissions() );
	}

	function test_it_does_not_do_capabilities_check_for_public_handlers_for_logged_in_users() {
		wp_set_current_user( 1 );
		$this->handler = new PublicAjaxHandler();
		$this->assertTrue( $this->handler->has_permissions() );
	}

	function test_it_knows_if_current_user_does_not_have_permissions() {
		$this->assertFalse( $this->handler->has_permissions() );
	}

	function test_it_knows_if_current_user_has_permissions() {
		wp_set_current_user( 1 );
		$this->assertTrue( $this->handler->has_permissions() );
	}

	function test_it_can_be_registered() {
		$this->handler->register();
		$this->assertEquals( 2, has_action( 'wp_ajax_concrete' ) );
	}

	function test_it_registers_admin_and_nopriv_actions_for_public_ajax() {
		$this->handler = new PublicAjaxHandler();
		$this->handler->register();
		$this->assertEquals( 2, has_action( 'wp_ajax_public' ) );
		$this->assertEquals( 2, has_action( 'wp_ajax_nopriv_public' ) );
	}

	function test_it_registers_async_action_for_async_handlers() {
		$this->handler = new AsyncAjaxHandler();
		$this->handler->register();
		$this->assertEquals( 2, has_action( 'do_later_async_job' ) );
	}

	function test_it_can_be_run() {
		wp_set_current_user( 1 );
		$_REQUEST['example_one_nonce'] = wp_create_nonce( 'example_one' );

		$this->handler = new ExampleAjaxHandler();
		$this->handler->register();

		do_action( 'wp_ajax_example_one' );
		$response = $this->handler->sent_json;

		$this->assertEquals( 'one', $response );
	}

	function test_it_sends_exceptions_to_the_client() {
		wp_set_current_user( 1 );

		$this->handler = new ExceptionAjaxHandler();
		$this->handler->register();

		do_action( 'wp_ajax_exception_one' );
		$response = $this->handler->sent_json;

		$this->assertEquals( 'an_exception_occurred', $response );
	}

}

class ConcreteAjaxHandler extends AjaxHandler {

	function get_action() {
		return 'concrete';
	}

	function run( $params ) {
		return 'ok';
	}

}

class PublicAjaxHandler extends AjaxHandler {

	function is_public() {
		return true;
	}

	function get_action() {
		return 'public';
	}

	function run( $params ) {
		return 'ok';
	}

}

class ExampleAjaxHandler extends AjaxHandler {

	function get_action() {
		return 'example_one';
	}

	function run( $params ) {
		return 'one';
	}

}

class ExceptionAjaxHandler extends AjaxHandler {

	function get_action() {
		return 'exception_one';
	}

	function run( $params ) {
		throw new \Exception( 'an_exception_occurred' );
	}

}

class AsyncAjaxHandler extends AjaxHandler {

	function get_action() {
		return 'do_later';
	}

	function run( $params ) {
		return 'ok';
	}

	function is_async() {
		return true;
	}

}
