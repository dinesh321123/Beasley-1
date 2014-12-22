<?php

namespace GreaterMedia\Gigya;

class ProfilePathTest extends \WP_UnitTestCase {

	public $path;

	function setUp() {
		parent::setUp();

		$this->path = new ProfilePath();
	}

	function tearDown() {
		parent::tearDown();
	}

	function test_it_can_build_path_for_login_action() {
		$actual = $this->path->path_for( 'login' );
		$this->assertEquals( '/members/login', $actual );
	}

	function test_it_can_build_path_for_logout_action() {
		$actual = $this->path->path_for( 'logout' );
		$this->assertEquals( '/members/logout', $actual );
	}

	function test_it_can_build_path_for_register_action() {
		$actual = $this->path->path_for( 'register' );
		$this->assertEquals( '/members/register', $actual );
	}

	function test_it_can_build_path_for_settings_action() {
		$actual = $this->path->path_for( 'settings' );
		$this->assertEquals( '/members/settings', $actual );
	}

	function test_it_can_build_path_for_forgot_password_action() {
		$actual = $this->path->path_for( 'forgot-password' );
		$this->assertEquals( '/members/forgot-password', $actual );
	}

	function test_it_can_build_path_for_cookies_required_action() {
		$actual = $this->path->path_for( 'cookies-required' );
		$this->assertEquals( '/members/cookies-required', $actual );
	}

	function test_it_can_build_path_with_dest_parameter() {
		$params = array(
			'dest' => '/foo/bar',
		);

		$actual = $this->path->path_for( 'login', $params );
		$expected = '/members/login?dest=%2Ffoo%2Fbar';

		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_build_path_with_extra_parameters() {
		$params = array(
			'dest' => '/foo/bar',
			'a' => 'lorem',
			'b' => 'ipsum',
			'c' => 'dolor',
		);

		$actual = $this->path->path_for( 'login', $params );
		$parts  = parse_url( $actual );
		$query  = $parts['query'];
		$actual_params = array();
		parse_str( $query, $actual_params );

		$this->assertEquals( $params, $actual_params );
	}

	function test_it_can_build_path_with_anchor() {
		$params = array(
			'dest'   => '/foo/bar',
			'a'      => 'lorem',
			'b'      => 'ipsum',
			'c'      => 'dolor',
			'anchor' => 'comments',
		);

		$actual        = $this->path->path_for( 'login', $params );
		$parts         = parse_url( $actual );
		$query         = $parts['query'];
		$actual_params = array();

		parse_str( $query, $actual_params );

		$this->assertEquals( $params, $actual_params );
	}

	function test_it_has_a_singleton_instance() {
		$instance = ProfilePath::get_instance();
		$instance2 = ProfilePath::get_instance();

		$this->assertSame( $instance, $instance2 );
	}

	function test_it_can_build_path_using_api_helper() {
		$params = array(
			'dest'   => '/foo/bar',
			'a'      => 'lorem',
			'b'      => 'ipsum',
			'c'      => 'dolor',
			'anchor' => 'comments',
		);

		$actual        = gigya_profile_path( 'login', $params );
		$parts         = parse_url( $actual );
		$query         = $parts['query'];
		$actual_params = array();

		parse_str( $query, $actual_params );

		$this->assertEquals( $params, $actual_params );
	}
}
