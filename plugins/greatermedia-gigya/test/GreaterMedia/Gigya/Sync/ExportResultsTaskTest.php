<?php

namespace GreaterMedia\Gigya\Sync;

class ExportResultsTaskTest extends \WP_UnitTestCase {

	public $task;

	function setUp() {
		parent::setUp();

		wp_async_task_clear();

		$this->post_id = $this->factory->post->create();
		$this->task = new ExportResultsTask();
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

	function _test_it_can_create_new_email_segment() {
		$actual = $this->task->create_email_segment();
		$this->assertNotEmpty( $actual );
	}

	function test_it_can_remove_all_members_in_segment() {
		$segment_id = 2097427;
		$actual = $this->task->remove_all_members_in_segment( $segment_id );
		$this->assertTrue( $actual );
	}

	function _test_it_can_add_members_in_batches() {
		$members = array(
			array( 'email' => 'd0006@10up.com' ),
			array( 'email' => 'd0007@10up.com' ),
			array( 'email' => 'd0008@10up.com' ),
			array( 'email' => 'd0009@10up.com' ),
			array( 'email' => 'd00010@10up.com' ),
		);

		$segment_id = 2097427;
		$actual = $this->task->add_members_to_segment( $members, $segment_id );
		$this->assertTrue( $actual );

		$result = $this->task->get_members_in_segment( $segment_id );
		var_dump( $result );
	}

}
