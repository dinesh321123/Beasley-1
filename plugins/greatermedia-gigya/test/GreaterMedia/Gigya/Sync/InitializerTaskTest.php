<?php

namespace GreaterMedia\Gigya\Sync;

class InitializerTaskTest extends \WP_UnitTestCase {

	public $task;
	public $post_id;

	function setUp() {
		parent::setUp();

		wp_async_task_clear();

		$this->post_id = $this->factory->post->create();
		$this->task = new InitializerTask();
		$this->task->params = array(
			'member_query_id' => $this->post_id,
			'mode' => 'preview',
			'site_id' => 1,
			'checksum' => 'foo-checksum',
			'store_type' => 'profile',
		);

		$this->task->get_sentinel()->set_checksum( 'foo-checksum' );

		$factory = $this->task->get_task_factory();
		$factory->set_task( 'profile', 'GreaterMedia\Gigya\Sync\StubProfileQueryTask' );
		$factory->set_task( 'data_store', 'GreaterMedia\Gigya\Sync\StubDataStoreQueryTask' );
	}

	function tearDown() {
		parent::tearDown();
	}

	function test_it_has_an_associated_member_query() {
		$member_query = $this->task->get_member_query();
		$this->assertEquals( $this->post_id, $member_query->post_id );
	}

	function test_it_has_a_task_factory() {
		$actual = $this->task->get_task_factory();
		$this->assertInstanceOf( 'GreaterMedia\Gigya\Sync\TaskFactory', $actual );
	}

	function test_it_can_create_task_for_profile_store_type() {
		$actual = $this->task->get_task_for_store_type( 'profile' );
		$this->assertInstanceOf( 'GreaterMedia\Gigya\Sync\QueryTask', $actual );
	}

	function test_it_can_build_params_object_for_subquery() {
		$subquery = array(
			'store_type' => 'profile',
			'query' => 'select * from accounts',
		);

		$actual = $this->task->get_params_for_subquery( $subquery );
		$expected = array(
			'member_query_id' => $this->post_id,
			'mode'            => 'preview',
			'checksum'        => 'foo-checksum',
			'site_id'         => 1,
			'query'           => 'select * from accounts',
			'conjunction'     => 'any',
			'store_type'      => 'profile',
		);

		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_enqueue_profile_subquery() {
		$subquery = array(
			'store_type' => 'profile',
			'query' => 'select * from accounts',
		);

		$profile_task = new StubProfileQueryTask();
		$profile_task->register();

		$this->task->enqueue_subquery( $subquery );

		$queue_task = wp_async_task_last_added();

		wp_async_task_run_last();

		$actual = $profile_task->params;
		$expected = array(
			'member_query_id' => $this->post_id,
			'mode'            => 'preview',
			'checksum'        => 'foo-checksum',
			'site_id'         => 1,
			'query'           => 'select * from accounts',
		);

		foreach ( $expected as $key => $value ) {
			$this->assertEquals( $value, $actual[ $key ] );
		}
	}

	function test_it_has_a_list_of_subqueries() {
		$constraints = array(
			array(
				'type'        => 'profile:city',
				'operator'    => 'contains',
				'conjunction' => 'and',
				'valueType'   => 'string',
				'value'       => 'New York',
			),
			array(
				'type'         => 'record:contest',
				'operator'     => 'equals',
				'conjunction'  => 'and',
				'valueType'    => 'string',
				'value'        => 'foo',
				'entryTypeID'  => 100,
				'entryFieldID' => 200,
			),
		);

		$json = json_encode( $constraints );
		update_post_meta( $this->post_id, 'member_query_constraints', $json, true );

		$actual = $this->task->get_subqueries();
		$this->assertEquals( 2, count( $actual ) );

		$expected = "select * from accounts where profile.city contains 'New York'";
		$this->assertEquals( 'profile', $actual[0]['store_type'] );
		$this->assertEquals( $expected, $actual[0]['query'] );

		$expected = "select * from actions where data.entries.entryType_s = 'record:contest' and data.entries.entryTypeID_i = 100 and data.entries.entryFieldID_s = '200' and data.entries.entryValue_s = 'foo'";
		$this->assertEquals( 'data_store', $actual[1]['store_type'] );
		$this->assertEquals( $expected, $actual[1]['query'] );
	}

	function test_it_can_enqueue_subqueries() {
		$constraints = array(
			array(
				'type'        => 'profile:city',
				'operator'    => 'contains',
				'conjunction' => 'and',
				'valueType'   => 'string',
				'value'       => 'New York',
			),
			array(
				'type'         => 'record:contest',
				'operator'     => 'equals',
				'conjunction'  => 'and',
				'valueType'    => 'string',
				'value'        => 'foo',
				'entryTypeID'  => 100,
				'entryFieldID' => 200,
			),
		);

		$json = json_encode( $constraints );
		update_post_meta( $this->post_id, 'member_query_constraints', $json, true );

		$this->task->enqueue_subqueries();
		$this->assertEquals( 2, wp_async_task_count() );
	}

	function test_it_has_a_subquery_conjunction() {
		$constraints = array(
			array(
				'type'        => 'profile:city',
				'operator'    => 'contains',
				'conjunction' => 'and',
				'valueType'   => 'string',
				'value'       => 'New York',
			),
			array(
				'type'         => 'record:contest',
				'operator'     => 'equals',
				'conjunction'  => 'and',
				'valueType'    => 'string',
				'value'        => 'foo',
				'entryTypeID'  => 100,
				'entryFieldID' => 200,
			),
		);

		$json = json_encode( $constraints );
		update_post_meta( $this->post_id, 'member_query_constraints', $json, true );

		$actual = $this->task->get_subquery_conjunction();
		$this->assertEquals( 'and', $actual );
	}

	function test_it_reset_sentinel_on_run() {
		$this->task->run();

		$actual = get_post_meta( $this->post_id, 'mqsm_mode', true );
		$this->assertEquals( '', $actual );
	}

	function test_it_enqueues_subqueries_on_run() {
		$constraints = array(
			array(
				'type'        => 'profile:city',
				'operator'    => 'contains',
				'conjunction' => 'and',
				'valueType'   => 'string',
				'value'       => 'New York',
			),
			array(
				'type'         => 'record:contest',
				'operator'     => 'equals',
				'conjunction'  => 'and',
				'valueType'    => 'string',
				'value'        => 'foo',
				'entryTypeID'  => 100,
				'entryFieldID' => 200,
			),
		);

		$json = json_encode( $constraints );
		update_post_meta( $this->post_id, 'member_query_constraints', $json, true );

		$this->task->run();
		$this->assertEquals( 2, wp_async_task_count() );
	}

	function test_it_tees_compile_results_task_if_no_subqueries_to_run() {
		$constraints = array();
		$json        = json_encode( $constraints );

		update_post_meta( $this->post_id, 'member_query_constraints', $json, true );

		$this->task->run();

		$actual = wp_async_task_last_added();

		$this->assertEquals( 'compile_results_async_job', $actual['action'] );
	}

}

class StubProfileQueryTask extends QueryTask {

	function run() {

	}

}

class StubDataStoreQueryTask extends QueryTask {

	function run() {

	}

}
