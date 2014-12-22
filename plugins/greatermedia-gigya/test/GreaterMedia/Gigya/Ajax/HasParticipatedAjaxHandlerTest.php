<?php

namespace GreaterMedia\Gigya\Ajax;

use GreaterMedia\Gigya\GigyaSession;

class HasParticipatedAjaxHandlerTest extends \WP_UnitTestCase {

	public $handler;

	function setUp() {
		parent::setUp();

		GigyaSession::$instance = null;
		$this->handler = new HasParticipatedAjaxHandler();
		$this->uid = '37F5E08F-74D3-40FC-8F4B-296AD29DACBB';
	}

	function tearDown() {
		parent::tearDown();
	}

	function init_gigya_keys() {
		$settings = array(
			'gigya_api_key' => '3_e_T7jWO0Vjsd9y0WJcjnsN6KaFUBv6r3VxMKqbitvw-qKfmaUWysQKa1fra5MTb6',
			'gigya_secret_key' => 'trS0ufXWUXZ0JBcpr/6umiRfgUiwT7YhJMQSDpUz/p8=',
		);

		$settings = json_encode( $settings );
		update_option( 'member_query_settings', $settings, true );
	}

	function init_cookie( $data ) {
		$text = json_encode( $data );
		$_COOKIE['gigya_profile'] = base64_encode( $text );
	}

	function test_it_has_valid_action_name() {
		$actual = $this->handler->get_action();
		$this->assertEquals( 'has_participated', $actual );
	}

	function test_it_knows_if_user_has_not_entered_contest() {
		$this->init_gigya_keys();

		$actual = $this->handler->has_user_entered_contest( '1000000', $this->uid );
		$this->assertFalse( $actual );
	}

	function test_it_knows_if_user_has_entered_contest() {
		$this->init_gigya_keys();

		$actual = $this->handler->has_user_entered_contest( '1500000', $this->uid );
		$this->assertTrue( $actual );
	}

	function test_it_knows_if_email_has_not_entered_contest() {
		$this->init_gigya_keys();
		$actual = $this->handler->has_email_entered_contest( '1000000', 'me@foo.com' );

		$this->assertFalse( $actual );
	}

	function test_it_knows_if_email_has_entered_contest() {
		$this->init_gigya_keys();

		$actual = $this->handler->has_email_entered_contest( '2500000', 'me@foo.com' );
		$this->assertTrue( $actual );
	}

	function test_it_knows_if_currently_logged_in_user_has_not_entered_contest() {
		$this->init_gigya_keys();
		$params = array(
			'contest_id' => '000000',
		);

		$actual = $this->handler->run( $params );
		$this->assertFalse( $actual );
	}

	function test_it_knows_if_currently_logged_in_user_has_participated_in_contest() {
		$this->init_gigya_keys();
		$this->init_cookie( array( 'UID' => $this->uid ) );
		$params = array(
			'contest_id' => '1500000',
		);

		$actual = $this->handler->run( $params );
		$this->assertTrue( $actual );
	}

	function test_it_knows_if_specified_email_has_not_entered_contest() {
		$this->init_gigya_keys();
		$params = array(
			'contest_id' => '1500000',
			'email' => 'foo@bar.com'
		);

		$actual = $this->handler->run( $params );
		$this->assertFalse( $actual );
	}

	function test_it_knows_if_specified_email_has_entered_contest() {
		$this->init_gigya_keys();
		$params = array(
			'contest_id' => '2500000',
			'email' => 'me@foo.com'
		);

		$actual = $this->handler->run( $params );
		$this->assertTrue( $actual );
	}

	function test_it_has_api_helper_that_knows_if_currently_logged_in_user_has_not_entered_contest() {
		$this->init_gigya_keys();
		$this->assertFalse( has_user_entered_contest( '000' ) );
	}

	function test_it_has_api_helper_that_knows_if_currently_logged_in_user_has_entered_contest() {
		$this->init_gigya_keys();
		$this->init_cookie( array( 'UID' => $this->uid ) );

		$this->assertTrue( has_user_entered_contest( '1500000' ) );
	}

	function test_it_has_api_helper_that_knows_if_email_has_not_entered_contest() {
		$this->init_gigya_keys();
		$this->assertFalse( has_email_entered_contest( '000', 'me@foo.com' ) );
	}

	function test_it_has_api_helper_that_knows_if_email_has_entered_contest() {
		$this->init_gigya_keys();
		$this->assertTrue( has_email_entered_contest( '2500000', 'me@foo.com' ) );
	}

	function test_it_knows_invalid_email_has_not_entered_contest() {
		$this->assertFalse( has_email_entered_contest( '2500000', 'foo' ) );
	}

}
