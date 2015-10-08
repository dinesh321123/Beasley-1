<?php

namespace GreaterMedia\Gigya\Sync;

use GreaterMedia\Gigya\MemberQueryPostType;
use GreaterMedia\Gigya\MemberQuery;

class LauncherTest extends \WP_UnitTestCase {

	public $launcher;

	function setUp() {
		parent::setUp();

		$this->launcher = new Launcher();
		$post_type = new MemberQueryPostType();
		$post_type->register();
	}

	function tearDown() {
		parent::tearDown();
	}

	function test_it_can_initialize_tasks_lazily() {
		$this->assertEmpty( $this->launcher->tasks );

		$this->launcher->get_tasks();
		$this->assertNotEmpty( $this->launcher->tasks );
	}

	function test_it_can_register_async_actions() {
		$this->launcher->register();
		$actual = has_action( 'sync_initializer_async_job' );
		$this->assertEquals( 2, $actual );
	}

	function test_it_can_lookup_tasks_lazily() {
		$actual = $this->launcher->get_task( 'initializer' );
		$this->assertInstanceOf(
			'GreaterMedia\Gigya\Sync\InitializerTask', $actual
		);
	}

	function test_it_launches_initializer_task_with_correct_params() {
		$this->launcher->launch( 1, 'export' );
		$actual = wp_async_task_last_added();

		$this->assertEquals( 1, $actual['params']['member_query_id'] );
		$this->assertEquals( 'export', $actual['params']['mode'] );
	}

	function test_it_launches_initializer_task_with_a_valid_checksum() {
		$this->launcher->launch( 1, 'export' );
		$actual = wp_async_task_last_added();

		$this->assertNotEmpty( $actual['params']['checksum'] );
	}

	function test_it_launches_initializer_task_with_current_site_id() {
		$this->launcher->launch( 1, 'export' );
		$actual   = wp_async_task_last_added();
		$expected = get_current_blog_id();

		$this->assertEquals( $expected, $actual['params']['site_id'] );
	}

	function test_it_enqueues_initializer_on_launch() {
		$this->launcher->register();
		$this->launcher->launch( 1, 'export' );
		$actual = $this->launcher->get_task( 'initializer' );

		wp_async_task_run_last();

		$this->assertEquals( 1, $actual->get_param( 'member_query_id' ) );
		$this->assertEquals( 'export', $actual->get_param( 'mode' ) );
	}

	function test_it_can_create_preview_post() {
		$constraints = array();
		$actual = $this->launcher->create_preview( $constraints );

		$this->assertInternalType( 'int', $actual );

		$post = get_post( $actual );
		$this->assertEquals( 'member_query_preview', $post->post_type );
		$this->assertEquals( 'draft', $post->post_status );
	}

	function test_it_can_save_preview_constraints_into_member_query() {
		$constraints = array(
			array(
				'type'        => 'profile:city',
				'operator'    => 'contains',
				'conjunction' => 'or',
				'valueType'   => 'string',
				'value'       => 'New York',
			),
			array(
				'type'        => 'profile:city',
				'operator'    => 'equals',
				'conjunction' => 'or',
				'valueType'   => 'string',
				'value'       => 'Los Angeles',
			),
		);
		$actual = $this->launcher->create_preview( $constraints );

		$member_query = new MemberQuery( $actual );
		$this->assertEquals( $constraints, $member_query->get_constraints() );
	}

	function test_it_can_launch_preview() {
		$constraints = array(
			array(
				'type'        => 'profile:city',
				'operator'    => 'contains',
				'conjunction' => 'or',
				'valueType'   => 'string',
				'value'       => 'New York',
			),
			array(
				'type'        => 'profile:city',
				'operator'    => 'equals',
				'conjunction' => 'or',
				'valueType'   => 'string',
				'value'       => 'Los Angeles',
			),
		);

		$this->launcher->register();
		$post_id = $this->launcher->preview( $constraints );

		$actual = wp_async_task_last_added();
		wp_async_task_run_last();

		$this->assertEquals( $post_id, $actual['params']['member_query_id'] );
	}

	function test_it_assigns_a_checksum_to_a_task_at_time_of_launch() {
		$this->launcher->register();
		$this->launcher->launch( 1, 'export' );

		$task = $this->launcher->get_task( 'initializer' );

		$this->assertTrue( $task->verify_checksum() );
	}

}
