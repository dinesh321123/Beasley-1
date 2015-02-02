<?php

namespace GreaterMedia\MyEmma\Webhooks;

class MemberSignupTest extends \WP_UnitTestCase {

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
		$this->webhook = new MemberSignup();
	}

	function tearDown() {
		parent::tearDown();
	}

	function test_it_has_an_event_name() {
		$event_name = $this->webhook->get_event_name();
		$this->assertEquals( 'member_signup', $event_name );
	}

	function test_it_can_subscribe_gigya_user() {
		$uid = '34dc27adf622457abfa161c906f32fb4';
		$this->webhook->subscribe( $uid );

		$data = get_gigya_user_profile_data( $uid );
		$this->assertFalse( $data['optout'] );
	}

}
