<?php

namespace GreaterMedia\Gigya\Sync;

class PreviewResultsTaskTest extends \WP_UnitTestCase {

	public $task;

	function setUp() {
		parent::setUp();

		wp_async_task_clear();

		$this->post_id = $this->factory->post->create();
		$this->task = new PreviewResultsTask();
		$this->task->params = array(
			'member_query_id' => $this->post_id,
			'mode'            => 'preview',
			'site_id'         => 1,
			'checksum'        => 'foo-checksum',
			'query'           => 'select UID from accounts where profile.age = 93',
			'store_type'      => 'profile',
		);

		$this->task->get_sentinel()->set_checksum( 'foo-checksum' );
	}

	function tearDown() {
		$db = TempDatabase::get_instance()->get_db();
		$db->delete( 'member_query_results', '1=1' );

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

	function test_it_has_a_task_name() {
		$actual = $this->task->get_task_name();
		$this->assertEquals( 'preview_results', $actual );
	}

	function test_it_can_save_users_to_post_meta() {
		$users = array(
			array( 'user_id' => 'a', 'email' => 'a@foo.com' ),
			array( 'user_id' => 'b', 'email' => 'b@foo.com' ),
			array( 'user_id' => 'c', 'email' => 'c@foo.com' ),
		);

		$this->task->save_users( $users, 500 );
		$actual = get_post_meta( $this->post_id, 'member_query_preview_results', true );
		$actual = json_decode( $actual, true );

		$this->assertEquals( 500, $actual['total'] );
		$this->assertEquals( $users, $actual['users'] );
	}

	function test_it_updates_task_progress_on_preview_completion() {
		$this->task->after( null );

		$sentinel = $this->task->get_sentinel();
		$actual   = $sentinel->get_task_progress( 'preview_results' );
		$this->assertEquals( 100, $actual );
	}

	function test_it_can_save_users_for_compiled_results() {
		$this->init_gigya_keys();
		$db = TempDatabase::get_instance()->get_db();
		$formats = array( '%d', '%d', '%s' );

		$values = array(
			'site_id' => 1,
			'member_query_id' => $this->post_id,
			'user_id' => '0000407c1ec144a5a2e80ac5f1e055bc',
		);
		$db->insert( 'member_query_results', $values, $formats );

		$values = array(
			'site_id' => 1,
			'member_query_id' => $this->post_id,
			'user_id' => '0000a650727545b6a3380a4876045332',
		);
		$db->insert( 'member_query_results', $values, $formats );

		$values = array(
			'site_id' => 1,
			'member_query_id' => $this->post_id,
			'user_id' => '0000af8dad554c0ba5e3a90ffede8c0f',
		);
		$db->insert( 'member_query_results', $values, $formats );

		$this->task->run();

		$json = get_post_meta( $this->post_id, 'member_query_preview_results', true );
		$results = json_decode( $json, true );
		$expected = array(
			array( 'user_id' => '0000407c1ec144a5a2e80ac5f1e055bc', 'email' => 'helmer48@lynchoberbrunner.com' ),
			array( 'user_id' => '0000a650727545b6a3380a4876045332', 'email' => 'xmorar@hotmail.com' ),
			array( 'user_id' => '0000af8dad554c0ba5e3a90ffede8c0f', 'email' => 'cara71@oconner.info' ),
		);

		$this->assertEquals( $expected, $results['users'] );
		$this->assertEquals( 3, $results['total'] );
	}

	function test_it_can_handle_query_without_any_results() {
		$this->init_gigya_keys();
		$db = TempDatabase::get_instance()->get_db();
		$db->delete( 'member_query_users', '1=1' );

		$this->task->run();

		$json = get_post_meta( $this->post_id, 'member_query_preview_results', true );
		$results = json_decode( $json, true );
		$expected = array();

		$this->assertEquals( $expected, $results['users'] );
		$this->assertEquals( 0, $results['total'] );
	}

}
