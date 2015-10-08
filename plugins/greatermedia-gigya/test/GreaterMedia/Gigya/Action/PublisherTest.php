<?php

namespace GreaterMedia\Gigya\Action;

class ActionPublisherTest extends \WP_UnitTestCase {

	public $publisher;

	function setUp() {
		parent::setUp();

		$this->publisher = new Publisher();
		$this->publisher->params = array(
			'user_id' => 'foo',
			'actions' => array(
				array(
					'actionType' => 'action:contest',
					'actionID' => '1',
					'actionData' => array(
						array( 'name' => 'a1', 'value' => 'v1' ),
					),
				),
			),
		);

		$this->publisher->store_name = 'actions_aaa';
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


	function test_it_has_a_user_id() {
		$actual = $this->publisher->get_user_id();
		$this->assertEquals( 'foo', $actual );
	}

	function test_it_has_a_guest_uid() {
		$actual = $this->publisher->get_guest_uid();
		$this->assertEquals( $this->publisher->guest_uid, $actual );
	}

	function test_it_has_an_action_uid_for_guests() {
		$this->publisher->params['user_id'] = 'guest';
		$actual = $this->publisher->get_action_uid();
		$this->assertEquals( $this->publisher->guest_uid, $actual );
	}

	function test_it_has_an_action_uid_for_logged_in_users() {
		$actual = $this->publisher->get_action_uid();
		$this->assertEquals( 'foo', $actual );
	}

	function test_it_has_a_member_query() {
		$actual = $this->publisher->get_member_query();
		$this->assertInstanceOf( 'GreaterMedia\Gigya\MemberQuery', $actual );
	}

	function test_it_knows_value_type_for_strings() {
		$actual = $this->publisher->value_type_for( 'foo' );
		$this->assertEquals( 'string', $actual );
	}

	function test_it_knows_value_type_for_booleans() {
		$actual = $this->publisher->value_type_for( true );
		$this->assertEquals( 'boolean', $actual );
	}

	function test_it_knows_value_type_for_floats() {
		$actual = $this->publisher->value_type_for( pi() );
		$this->assertEquals( 'float', $actual );
	}

	function test_it_knows_suffix_for_strings() {
		$actual = $this->publisher->suffix_for( 'string' );
		$this->assertEquals( '_s', $actual );
	}

	function test_it_knows_suffix_for_booleans() {
		$actual = $this->publisher->suffix_for( 'boolean' );
		$this->assertEquals( '_b', $actual );
	}

	function test_it_knows_suffix_for_integers() {
		$actual = $this->publisher->suffix_for( 'integer' );
		$this->assertEquals( '_i', $actual );
	}

	function test_it_knows_field_name_for_string_value() {
		$actual = $this->publisher->field_name_for( 'value', 'foo' );
		$this->assertEquals( 'value_s', $actual );
	}

	function test_it_knows_field_name_for_boolean_value() {
		$actual = $this->publisher->field_name_for( 'value', true );
		$this->assertEquals( 'value_b', $actual );
	}

	function test_it_knows_field_name_for_integer_value() {
		$actual = $this->publisher->field_name_for( 'value', 1001 );
		$this->assertEquals( 'value_i', $actual );
	}

	function test_it_can_prepare_action_for_storage() {
		$action = array(
			'actionType' => 'action:contest',
			'actionID' => '1',
			'actionData' => array(
				array( 'name' => 'a1', 'value' => 'v1' ),
			),
		);

		$json = $this->publisher->prepare_action_for_storage( $action );
		$actual = json_decode( $json, true );

		$this->assertEquals( 'v1', $actual['actions'][0]['actionData'][0]['value_s'] );
	}

	function test_it_can_publish_an_anonymous_action_to_data_store() {
		$this->init_gigya_keys();
		$action = array(
			'actionType' => 'action:contest',
			'actionID' => '1',
			'actionData' => array(
				array( 'name' => 'a1', 'value' => 'v1' ),
			),
		);

		$this->publisher->params['user_id'] = 'guest';
		$actual = $this->publisher->publish( $action );

		$this->assertEquals( 200, $actual['statusCode'] );
		$this->assertEquals( $this->publisher->guest_uid, $actual['UID'] );
	}

	function test_it_can_publish_a_logged_in_users_action_to_data_store() {
		$this->init_gigya_keys();
		$action = array(
			'actionType' => 'action:contest',
			'actionID' => '1',
			'actionData' => array(
				array( 'name' => 'a1', 'value' => 'v1' ),
			),
		);

		$uid = '37F5E08F-74D3-40FC-8F4B-296AD29DACBB';
		$this->publisher->params['user_id'] = $uid;
		$actual = $this->publisher->publish( $action );

		$this->assertEquals( 200, $actual['statusCode'] );
		$this->assertEquals( $uid, $actual['UID'] );
	}

	function test_it_can_publish_multiple_actions_to_data_store() {
		$this->init_gigya_keys();
		$actions = array(
			array(
				'actionType' => 'action:contest',
				'actionID' => '1',
				'actionData' => array(
					array( 'name' => 'a1', 'value' => 'v1' ),
				),
			),
			array(
				'actionType' => 'action:contest',
				'actionID' => '2',
				'actionData' => array(
					array( 'name' => 'b1', 'value' => 'v2' ),
				),
			),
		);

		$uid = '37F5E08F-74D3-40FC-8F4B-296AD29DACBB';
		$this->publisher->params['user_id'] = $uid;
		$this->publisher->publish_actions( $actions );
	}

	function test_it_can_be_publish_task_with_valid_params() {
		$this->init_gigya_keys();
		$this->publisher->params['actions'] = array(
			array(
				'actionType' => 'action:contest',
				'actionID' => '1',
				'actionData' => array(
					array( 'name' => 'a1', 'value' => 'v1' ),
				),
			),
			array(
				'actionType' => 'action:contest',
				'actionID' => '2',
				'actionData' => array(
					array( 'name' => 'b1', 'value' => 'v2' ),
				),
			),
		);

		$uid = '37F5E08F-74D3-40FC-8F4B-296AD29DACBB';
		$this->publisher->params['user_id'] = $uid;
		$this->publisher->run();
	}


	function test_it_can_get_account_info_for_uid() {
		$this->init_gigya_keys();

		$uid = 'a3b4d6d7f8d24f069c66d580c2cb9fdc';
		$actual = $this->publisher->get_account_info( $uid );

		$this->assertEquals( $actual['UID'], $uid );
	}

	function test_it_knows_comment_action_has_counter() {
		$this->assertTrue( $this->publisher->is_counter_action( 'comment' ) );
	}

	function test_it_knows_unknown_action_does_not_have_counter() {
		$this->assertFalse( $this->publisher->is_counter_action( 'foo' ) );
	}

	function test_it_knows_action_subtype_for_action() {
		$actual = $this->publisher->action_subtype_for( 'action:comment' );
		$this->assertEquals( 'comment', $actual );
	}

	function test_it_can_return_new_account_data_with_initial_counter() {
		$this->init_gigya_keys();
		$uid = 'a3b4d6d7f8d24f069c66d580c2cb9fdc';

		$actual = $this->publisher->get_new_account_data( $uid, 'fooaaa_count' );
		$this->assertEquals( 1, $actual['fooaaa_count'] );
	}

	function test_it_can_increment_counter_on_profile() {
		$this->init_gigya_keys();
		$uid = 'a3b4d6d7f8d24f069c66d580c2cb9fdc';

		$counter_name = 'foo' . time();

		$this->publisher->increment_counter( $uid, $counter_name );
		$this->publisher->increment_counter( $uid, $counter_name );

		$actual = $this->publisher->get_account_info( $uid );
		$this->assertEquals( 2, $actual['data'][ $counter_name ] );

	}

	/* dynamic list actions */
	function test_it_knows_if_not_a_list_action() {
		$this->assertFalse( $this->publisher->is_list_action( 'foo' ) );
	}

	function test_it_knows_if_a_list_action() {
		$this->assertTrue( $this->publisher->is_list_action( 'contest' ) );
	}

}
