<?php

namespace GreaterMedia\Gigya;

class GigyaSessionTest extends \WP_UnitTestCase {

	public $session;

	function setUp() {
		parent::setUp();

		unset( $_COOKIE['gigya_profile'] );
		GigyaSession::$instance = null;
		$this->session = new GigyaSession();
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

	function tearDown() {
		delete_option( 'member_query_settings' );
	}

	function test_it_can_parse_invalid_cookie_data() {
		$actual = $this->session->deserialize( 'foo{}' );
		$this->assertEquals( array(), $actual );
	}

	function test_it_can_parse_valid_cookie_data() {
		$text = json_encode( array( 'a' => 1 ) );
		$text = base64_encode( $text );
		$actual = $this->session->deserialize( $text );
		$this->assertEquals( array( 'a' => 1 ), $actual );
	}

	function test_it_can_parse_non_base64_data_for_old_browsers() {
		$text = json_encode( array( 'UID' => 'foo' ) );
		$actual = $this->session->deserialize( $text );

		$this->assertEquals( 'foo', $actual['UID'] );
	}

	function test_it_has_a_cookie_name() {
		$actual = $this->session->get_cookie_name();
		$this->assertEquals( 'gigya_profile', $actual );
	}

	function test_it_can_load_raw_cookie_data_if_absent() {
		$this->session->load();
		$actual = $this->session->cookie_value;
		$this->assertEquals( array(), $actual );
	}

	function test_it_can_load_raw_cookie_data_if_present() {
		$this->init_cookie( array( 'UID' => 'foo' ) );
		$this->session->load();
		$actual = $this->session->cookie_value;
		$this->assertEquals( 'foo', $actual['UID'] );
	}

	function test_it_can_load_invalid_cookie_data() {
		$this->session->load();
		$this->assertEquals( array(), $this->session->cookie_value );
	}

	function test_it_does_not_reload_cookie_data_once_loaded() {
		$this->session->load();
		$this->session->loaded = 'already_loaded';
		$this->session->load();
		$this->assertEquals( 'already_loaded', $this->session->loaded );
	}

	function test_it_knows_if_user_is_not_logged_in() {
		$actual = $this->session->is_logged_in();
		$this->assertFalse( $actual );
	}

	function test_it_has_a_singleton_instance() {
		$session1 = GigyaSession::get_instance();
		$session2 = GigyaSession::get_instance();

		$this->assertSame( $session1, $session2 );
	}

	function test_it_has_null_user_id_if_not_logged_in() {
		$this->assertNull( $this->session->get_user_id() );
	}

	function test_it_knows_user_id_if_logged_in() {
		$this->init_cookie( array( 'UID' => 'foo' ) );
		$this->assertEquals( 'foo', $this->session->get_user_id() );
	}

	function test_it_knows_user_age_from_cookie() {
		$this->init_cookie( array( 'UID' => 'foo', 'age' => 25 ) );
		$this->assertEquals( 25, $this->session->get_user_field( 'age' ) );
	}

	/* api helpers tests */
	function test_api_helper_knows_user_is_not_logged_in() {
		$this->assertFalse( is_gigya_user_logged_in() );
	}

	function test_api_helper_knows_if_user_is_logged_in() {
		$this->init_cookie( array( 'UID' => 'foo', 'age' => 25 ) );
		$this->assertTrue( is_gigya_user_logged_in() );
	}

	function test_api_helper_has_null_user_id_if_not_logged_in() {
		$this->assertNull( get_gigya_user_id() );
	}

	function test_api_helper_knows_logged_in_users_id() {
		$this->init_cookie( array( 'UID' => 'foo', 'age' => 25 ) );
		$this->assertEquals( 'foo', get_gigya_user_id() );
	}

	function test_api_helper_knows_logged_in_users_age() {
		$this->init_cookie( array( 'UID' => 'foo', 'age' => 25 ) );
		$this->assertEquals( 25, get_gigya_user_field( 'age' ) );
	}

	function test_api_helper_throws_exception_when_fetching_user_profile_and_not_logged_in() {
		$this->setExpectedException( 'Exception' );
		$this->session->get_user_profile();
	}

	function test_api_helper_throws_exception_when_fetching_invalid_user_profile() {
		$this->init_gigya_keys();
		$this->init_cookie( array( 'UID' => 'foo', 'age' => 25 ) );

		$this->setExpectedException( 'Exception' );
		$this->session->get_user_profile();
	}

	function test_api_helper_can_fetch_full_gigya_profile() {
		$this->init_gigya_keys();
		$this->init_cookie( array( 'UID' => '37F5E08F-74D3-40FC-8F4B-296AD29DACBB', 'age' => 25 ) );

		$profile = $this->session->get_user_profile();
		$this->assertNotEmpty( $profile['country'] );
	}

}
