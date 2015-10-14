<?php

namespace GreaterMedia\MyEmma\Webhooks;

class WebhookTest extends \WP_UnitTestCase {

	public $webhook;

	function setUp() {
		parent::setUp();

		$this->webhook = new ConcreteWebhook();
		$settings    = array(
			'emma_account_id'  => '1746533',
			'emma_public_key'  => '3e89a3b76be875952b48',
			'emma_private_key' => '519231e76466c2f0bfc0',
			'emma_webhook_auth_token' => 'foo',
		);

		update_option( 'member_query_settings', json_encode( $settings ) );
	}

	function tearDown() {
		parent::tearDown();
	}

	function test_it_has_an_action() {
		$actual = $this->webhook->get_action();
		$this->assertEquals( 'myemma_webhook_foo', $actual );
	}

	function test_it_has_an_emma_api() {
		$api1 = $this->webhook->get_emma_api();
		$api2 = $this->webhook->get_emma_api();
		$this->assertSame( $api1, $api2 );
	}

	function test_it_has_an_auth_token() {
		$actual = $this->webhook->get_required_auth_token();
		$this->assertEquals( 'foo', $actual );
	}

	function test_it_will_throw_an_exception_if_auth_token_is_absent() {
		$this->setExpectedException( 'Exception' );
		$this->webhook->authorize();
	}

	function test_it_will_throw_an_exception_if_auth_token_is_invalid() {
		$_GET['auth_token'] = 'invalid';
		$this->setExpectedException( 'Exception' );
		$this->webhook->authorize();
	}

	function test_it_knows_if_auth_token_is_valid() {
		$_GET['auth_token'] = 'foo';
		$actual = $this->webhook->authorize();
		$this->assertEquals( true, $actual );
	}

	function test_it_can_build_url_to_webhook() {
		$actual = $this->webhook->get_url();
		$this->assertContains( 'action=myemma_webhook_foo', $actual );
		$this->assertContains( 'auth_token=foo', $actual );
	}

	function test_it_can_run_webhook_without_auth_token() {
		$this->setExpectedException( 'Exception' );
		$this->webhook->handle_ajax();
	}

	function test_it_can_run_webhook_without_params() {
		$_GET['auth_token'] = 'foo';
		$this->setExpectedException( 'Exception' );
		$this->webhook->handle_ajax();
	}

	function test_it_can_run_webhook_with_params() {
		$_GET['auth_token'] = 'foo';
		$this->webhook->params = array( 'a', 'b', 'c' );
		$this->webhook->handle_ajax();

		$sent_json = $this->webhook->sent_json;
		$this->assertEquals( array( 'a', 'b', 'c' ), $sent_json );
	}

	function test_it_knows_the_emma_member_id() {
		$params = array( 'data' => array( 'member_id' => 123 ) );
		$actual = $this->webhook->get_emma_member_id( $params );
		$this->assertEquals( '123', $actual );
	}

	function test_it_knows_the_gigya_user_id() {
		$actual = $this->webhook->get_gigya_user_id( '715065957' );
		$this->assertEquals( '34dc27adf622457abfa161c906f32fb4', $actual );
	}

}

class ConcreteWebhook extends Webhook {

	function get_event_name() {
		return 'foo';
	}

	function run( $params ) {
		return $params;
	}

}
