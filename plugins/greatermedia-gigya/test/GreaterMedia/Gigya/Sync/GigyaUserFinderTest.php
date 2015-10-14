<?php

namespace GreaterMedia\Gigya\Sync;

class GigyaUserFinderTest extends \WP_UnitTestCase {

	public $finder;

	function setUp() {
		parent::setUp();

		$this->finder = new GigyaUserFinder();
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

	function test_it_can_build_query_for_list_of_user_ids() {
		$user_ids = array( 'a', 'b', 'c' );
		$actual   = $this->finder->query_for( $user_ids );

		$this->assertEquals( "select profile.email, UID from accounts where UID in ('a', 'b', 'c')", $actual );
	}

	function test_it_can_convert_result_without_email_to_user() {
		$result = array(
			'UID' => 'foo',
			'profile' => array(),
		);
		$actual = $this->finder->result_to_user( $result );

		$this->assertEquals( 'foo', $actual['user_id'] );
		$this->assertNotContains( 'email', $actual );
	}

	function test_it_can_convert_result_with_email_to_user() {
		$result = array(
			'UID' => 'foo',
			'profile' => array( 'email' => 'foo@bar.com' ),
		);
		$actual = $this->finder->result_to_user( $result );

		$this->assertEquals( 'foo@bar.com', $actual['email'] );
	}

	function test_it_can_convert_list_of_users_to_results() {
		$results = array(
			array(
				'UID' => 'a',
				'profile' => array( 'email' => 'a@foo.com' ),
			),
			array(
				'UID' => 'b',
				'profile' => array( 'email' => 'b@foo.com' ),
			),
			array(
				'UID' => 'c',
				'profile' => array( 'email' => 'c@foo.com' ),
			),
		);

		$actual = $this->finder->results_to_users( $results );
		$actual = array_column( $actual, 'email' );

		$expected = array(
			'a@foo.com',
			'b@foo.com',
			'c@foo.com',
		);

		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_find_gigya_users_for_specified_user_ids() {
		$this->init_gigya_keys();

		$user_ids = array(
			'0000407c1ec144a5a2e80ac5f1e055bc',
			'0000a650727545b6a3380a4876045332',
			'0000af8dad554c0ba5e3a90ffede8c0f',
		);

		$actual = $this->finder->find( $user_ids );
		$expected = array(
			array( 'user_id' => '0000407c1ec144a5a2e80ac5f1e055bc', 'email' => 'helmer48@lynchoberbrunner.com' ),
			array( 'user_id' => '0000a650727545b6a3380a4876045332', 'email' => 'xmorar@hotmail.com' ),
			array( 'user_id' => '0000af8dad554c0ba5e3a90ffede8c0f', 'email' => 'cara71@oconner.info' ),
		);

		$this->assertEquals( $expected, $actual );
	}

	function test_it_returns_empty_list_if_gigya_users_not_found() {
		$this->init_gigya_keys();

		$user_ids = array(
			'lorem',
			'ipsum',
			'dolor',
		);

		$actual = $this->finder->find( $user_ids );

		$this->assertEmpty( $actual );
	}

}
