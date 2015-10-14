<?php

namespace GreaterMedia\Gigya\Ajax;

use GreaterMedia\Gigya\MemberQueryPostType;
use GreaterMedia\Gigya\Sync\Launcher;

class PreviewResultsAjaxHandlerTest extends \WP_UnitTestCase {

	public $handler;
	public $post_id;

	function setUp() {
		parent::setUp();

		wp_async_task_clear();

		$this->post_id = $this->factory->post->create();
		$this->handler = new PreviewResultsAjaxHandler();

		$post_type = new MemberQueryPostType();
		$post_type->register();
	}

	function tearDown() {
		parent::tearDown();
	}

	function test_it_knows_sentinel_for_specified_query() {
		$actual = $this->handler->sentinel_for( 1 );
		$this->assertEquals( 1, $actual->member_query_id );
	}

	function test_it_has_an_action_name() {
		$actual = $this->handler->get_action();
		$this->assertEquals( 'preview_member_query', $actual );
	}

	function test_it_can_start_preview_sync_process() {
		$params = array(
			'constraints' => array()
		);

		$actual = $this->handler->start( $params );
		$this->assertInternalType( 'int', $actual['member_query_id'] );
	}

	function test_it_can_return_status_of_started_query() {
		$params = array(
			'constraints' => array(),
			'mode' => 'start',
		);

		$result = $this->handler->run( $params );
		$member_query_id = $result['member_query_id'];

		$params['member_query_id'] = $member_query_id;
		$params['mode'] = 'status';

		$actual = $this->handler->run( $params );

		$this->assertFalse( $actual['complete'] );
		$this->assertEquals( 0, $actual['progress'] );
	}

	function test_it_can_return_status_of_completed_query() {
		wp_async_task_autorun();

		$launcher = new Launcher();
		$launcher->register();

		$params = array(
			'constraints' => array(),
			'mode' => 'start',
		);

		$result = $this->handler->run( $params );
		$member_query_id = $result['member_query_id'];

		$params['member_query_id'] = $member_query_id;
		$params['mode'] = 'status';

		$actual = $this->handler->run( $params );

		$this->assertTrue( $actual['complete'] );
		$this->assertEquals( 100, $actual['progress'] );
	}

	function test_it_can_return_errors_for_failed_query() {
		wp_async_task_autorun();

		$launcher = new Launcher();
		$launcher->register();

		$params = array(
			'constraints' => array(),
			'mode' => 'start',
		);

		$result = $this->handler->run( $params );
		$member_query_id = $result['member_query_id'];

		$params['member_query_id'] = $member_query_id;
		$params['mode'] = 'status';

		$sentinel = $this->handler->sentinel_for( $member_query_id );
		$sentinel->add_error( 'foo' );
		$actual = $this->handler->run( $params );

		$this->assertTrue( $actual['complete'] );
		$this->assertEquals( 100, $actual['progress'] );
		$this->assertEquals( array( 'foo' ), $actual['errors'] );
	}

}
