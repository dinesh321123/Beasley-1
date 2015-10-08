<?php

namespace GreaterMedia\Gigya;

class GigyaSessionTest extends \WP_UnitTestCase {

	public $session;

	function setUp() {
		parent::setUp();

		unset( $_COOKIE['gigya_profile'] );
		GigyaSession::$instance = null;
		$this->session = new GigyaSession();
		$this->uid = '34dc27adf622457abfa161c906f32fb4';
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

	/* vary_cache_on_function helpers */
	function get_cache_variant() {
		/*
		 * When making changes to the batcache variant algorithm use
		 * dev = true, then copy-paste and switch dev to false
		 */
		$dev = false;

		if ( $dev ) {
			return $this->session->get_cache_variant();
		} else {
			return $this->get_batcache_variant();
		}
	}

	/**
	 * Creates the cache variant anonymous function, executes it and
	 * returns it's result.
	 *
	 */
	function get_batcache_variant() {
		$func = create_function( '', $this->session->get_cache_variant_func() );
		return $func();
	}

	/* vary_cache_on_function tests */
	function test_it_knows_variant_for_guest_user_with_cookie() {
		$actual = $this->get_cache_variant();
		$this->assertEquals( 'no', $actual );
	}

	function test_it_knows_variant_for_invalid_session_cookie() {
		$_COOKIE['gigya_profile'] = 'foo';
		$actual = $this->get_cache_variant();
		$this->assertEquals( 'no', $actual );
	}

	function test_it_knows_variant_for_invalid_json_inside_session_cookie() {
		$_COOKIE['gigya_profile'] = '{"foo":"}';
		$actual = $this->get_cache_variant();
		$this->assertEquals( 'no', $actual );
	}

	function test_it_knows_variant_for_session_cookie_without_gigya_user_id() {
		$_COOKIE['gigya_profile'] = '{"stuff":123}';
		$actual = $this->get_cache_variant();
		$this->assertEquals( 'no', $actual );
	}

	function test_it_knows_variant_for_valid_session_cookie_without_age() {
		$this->init_cookie( array( 'UID' => $this->uid ) );
		$actual = $this->get_cache_variant();
		$this->assertEquals( 'yes_0', $actual );
	}

	function test_it_knows_variant_for_valid_session_cookie_with_0_age() {
		$this->init_cookie( array( 'UID' => $this->uid, 'age' => 0 ) );
		$actual = $this->get_cache_variant();
		$this->assertEquals( 'yes_0', $actual );
	}

	function test_it_knows_variant_for_valid_session_cookie_with_age_less_than_18() {
		$this->init_cookie( array( 'UID' => $this->uid, 'age' => 15 ) );
		$actual = $this->get_cache_variant();
		$this->assertEquals( 'yes_0', $actual );
	}

	function test_it_knows_variant_for_valid_session_cookie_with_age_equal_to_18() {
		$this->init_cookie( array( 'UID' => $this->uid, 'age' => 18 ) );
		$actual = $this->get_cache_variant();
		$this->assertEquals( 'yes_18', $actual );
	}

	function test_it_knows_variant_for_valid_session_cookie_with_age_between_18_and_21() {
		$this->init_cookie( array( 'UID' => $this->uid, 'age' => 20 ) );
		$actual = $this->get_cache_variant();
		$this->assertEquals( 'yes_18', $actual );
	}

	function test_it_knows_variant_for_valid_session_cookie_with_age_equal_to_21() {
		$this->init_cookie( array( 'UID' => $this->uid, 'age' => 21 ) );
		$actual = $this->get_cache_variant();
		$this->assertEquals( 'yes_21', $actual );
	}

	function test_it_knows_variant_for_valid_session_cookie_with_age_greater_than_21() {
		$this->init_cookie( array( 'UID' => $this->uid, 'age' => 50 ) );
		$actual = $this->get_cache_variant();
		$this->assertEquals( 'yes_21', $actual );
	}

	/* profile data storage tests */

	function test_it_can_find_full_user_data_profile() {
		$this->init_gigya_keys();
		$this->init_cookie( array( 'UID' => $this->uid, 'age' => 50 ) );

		$actual = $this->session->get_user_profile_data( $this->uid );
		$this->assertContains( 'listeningLoyalty', $actual );
	}

	function test_it_can_find_single_user_data_field() {
		$this->init_gigya_keys();
		$this->init_cookie( array( 'UID' => $this->uid, 'age' => 50 ) );
		$actual = $this->session->get_user_data_field( $this->uid, 'listeningLoyalty' );

		$this->assertEquals( '0', $actual );
	}

	function test_it_can_find_single_unknown_user_data_field() {
		$this->init_gigya_keys();
		$this->init_cookie( array( 'UID' => $this->uid, 'age' => 50 ) );
		$actual = $this->session->get_user_data_field( $this->uid, 'unknown' );

		$this->assertNull( $actual );
	}

	function test_it_can_change_user_data_field() {
		$this->init_gigya_keys();
		$this->init_cookie( array( 'UID' => $this->uid, 'age' => 50 ) );
		$this->session->set_user_data_field( $this->uid, 'test1', ['a', 'b', 'c'] );
		$actual = $this->session->get_user_data_field( $this->uid, 'test1' );

		$this->assertEquals( array( 'a', 'b', 'c' ), $actual );
	}

	function test_it_can_store_array_of_integers() {
		$this->init_gigya_keys();
		$this->init_cookie( array( 'UID' => $this->uid, 'age' => 50 ) );
		$contests = array( time(), time(), time() );
		$this->session->set_user_data_field( $this->uid, 'test2', $contests );
		$actual = $this->session->get_user_data_field( $this->uid, 'test2' );

		$this->assertEquals( $contests, $actual );
	}

	/* profile data api helpers */
	function test_it_has_api_helper_to_find_full_user_data_profile_not_logged_in() {
		$this->init_gigya_keys();

		$actual = get_gigya_user_profile_data( $this->uid );
		$this->assertContains( 'listeningLoyalty', $actual );
	}

	function test_it_has_api_helper_to_find_single_user_data_field_not_logged_in() {
		$this->init_gigya_keys();
		$actual = get_gigya_user_data_field( $this->uid, 'listeningLoyalty' );

		$this->assertEquals( '0', $actual );
	}

	function test_it_has_api_helper_to_find_single_unknown_user_data_field_not_logged_in() {
		$this->init_gigya_keys();
		$actual = get_gigya_user_data_field( $this->uid, 'unknown' );

		$this->assertNull( $actual );
	}

	function test_it_has_api_helper_to_change_user_data_field_not_logged_in() {
		$this->init_gigya_keys();
		set_gigya_user_data_field( $this->uid, 'test1', ['a', 'b', 'c'] );
		$actual = get_gigya_user_data_field( $this->uid, 'test1' );

		$this->assertEquals( array( 'a', 'b', 'c' ), $actual );
	}

	function test_it_has_api_helper_to_store_array_of_integers_not_logged_in() {
		$this->init_gigya_keys();
		$contests = array( time(), time(), time() );
		set_gigya_user_data_field( $this->uid, 'test2', $contests );
		$actual = get_gigya_user_data_field( $this->uid, 'test2' );

		$this->assertEquals( $contests, $actual );
	}

	/* logged in api helpers */
	function test_it_has_api_helper_to_find_full_user_data_profile() {
		$this->init_gigya_keys();
		$this->init_cookie( array( 'UID' => $this->uid, 'age' => 50 ) );

		$actual = get_gigya_user_profile_data( $this->uid );
		$this->assertContains( 'listeningLoyalty', $actual );
	}

	function test_it_has_api_helper_to_find_single_user_data_field() {
		$this->init_gigya_keys();
		$this->init_cookie( array( 'UID' => $this->uid, 'age' => 50 ) );
		$actual = get_gigya_user_data_field( $this->uid, 'listeningLoyalty' );

		$this->assertEquals( '0', $actual );
	}

	function test_it_has_api_helper_to_find_single_unknown_user_data_field() {
		$this->init_gigya_keys();
		$this->init_cookie( array( 'UID' => $this->uid, 'age' => 50 ) );
		$actual = get_gigya_user_data_field( $this->uid, 'unknown' );

		$this->assertNull( $actual );
	}

	function test_it_has_api_helper_to_change_user_data_field() {
		$this->init_gigya_keys();
		$this->init_cookie( array( 'UID' => $this->uid, 'age' => 50 ) );
		set_gigya_user_data_field( $this->uid, 'test1', ['a', 'b', 'c'] );
		$actual = get_gigya_user_data_field( $this->uid, 'test1' );

		$this->assertEquals( array( 'a', 'b', 'c' ), $actual );
	}

	function test_it_has_api_helper_to_store_array_of_integers() {
		$this->init_gigya_keys();
		$this->init_cookie( array( 'UID' => $this->uid, 'age' => 50 ) );
		$contests = array( time(), time(), time() );
		set_gigya_user_data_field( $this->uid, 'test2', $contests );
		$actual = get_gigya_user_data_field( $this->uid, 'test2' );

		$this->assertEquals( $contests, $actual );
	}
}
