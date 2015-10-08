<?php

namespace GreaterMedia\MyEmma\Webhooks;

class MemberOptoutTest extends \WP_UnitTestCase {

	function setUp() {
		parent::setUp();

		$settings    = array(
			'emma_account_id'  => '1746533',
			'emma_public_key'  => '3e89a3b76be875952b48',
			'emma_private_key' => '519231e76466c2f0bfc0',
			'gigya_api_key' => '3_e_T7jWO0Vjsd9y0WJcjnsN6KaFUBv6r3VxMKqbitvw-qKfmaUWysQKa1fra5MTb6',
			'gigya_secret_key' => 'trS0ufXWUXZ0JBcpr/6umiRfgUiwT7YhJMQSDpUz/p8=',
		);

		update_option( 'member_query_settings', json_encode( $settings ) );
		$this->webhook = new MemberOptout();
	}

	function tearDown() {
		parent::tearDown();
	}

	function test_it_has_an_event_name() {
		$event_name = $this->webhook->get_event_name();
		$this->assertEquals( 'member_optout', $event_name );
	}

	function test_it_has_emma_groups() {
		$emma_groups = array(
			array( 'group_id' => '1', 'group_name' => 'foo', 'field_key' => 'myGroup' ),
		);

		update_option( 'emma_groups', json_encode( $emma_groups, true ) );
		$actual = $this->webhook->get_emma_groups();
		$this->assertEquals( $emma_groups, $actual );
	}

	function test_it_can_unsubscribe_gigya_user() {
		$emma_groups = array(
			array( 'group_id' => '1', 'group_name' => 'foo1', 'field_key' => 'fooGroup1' ),
			array( 'group_id' => '2', 'group_name' => 'foo2', 'field_key' => 'fooGroup2' ),
			array( 'group_id' => '3', 'group_name' => 'foo3', 'field_key' => 'fooGroup3' ),
		);

		$uid = '34dc27adf622457abfa161c906f32fb4';

		update_option( 'emma_groups', json_encode( $emma_groups, true ) );
		$this->webhook->unsubscribe( $uid );
	}

	function test_it_can_run_webhook_with_invalid_member_id() {
		$params = array(
			'event_name' => 'member_optout',
			'data' => array(
				'member_id' => '123',
			)
		);

		$this->setExpectedException( 'Emma_Invalid_Response_Exception' );
		$this->webhook->run( $params );
	}

	function test_it_can_run_webhook_with_valid_member_id() {
		$params = array(
			'event_name' => 'member_optout',
			'data' => array(
				'member_id' => '715065957',
			)
		);

		$this->webhook->run( $params );
	}

}
