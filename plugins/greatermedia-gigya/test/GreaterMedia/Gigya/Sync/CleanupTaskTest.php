<?php

namespace GreaterMedia\Gigya\Sync;

class CleanupTaskTest extends \WP_UnitTestCase {

	public $task;
	public $post_id;

	function setUp() {
		parent::setUp();

		$this->post_id = $this->factory->post->create();

		$this->task = new CleanupTask();
		$this->task->params = array(
			'member_query_id' => $this->post_id,
			'mode'            => 'preview',
			'site_id'         => 1,
			'checksum'        => 'foo-checksum',
			'query'           => 'select UID from accounts where profile.age = 93',
			'store_type'      => 'profile',
			'conjunction'     => 'and',
		);
	}

	function tearDown() {
		$db = $this->task->get_job_db();
		$db->delete( 'member_query_users', '1=1' );

		$db = $this->task->get_job_db();
		$db->delete( 'member_query_results', '1=1' );

		parent::tearDown();
	}


	function test_it_has_a_task_name() {
		$this->assertEquals( 'cleanup', $this->task->get_task_name() );
	}

	function test_it_has_a_job_db() {
		$actual = $this->task->get_job_db();
		$this->assertInstanceOf( 'wpdb', $actual );
	}

	function test_it_has_a_where_clause() {
		$actual = $this->task->get_where_clause();
		$this->assertEquals( 1, $actual['site_id'] );
		$this->assertEquals( $this->post_id, $actual['member_query_id'] );
	}

	function test_it_can_clear_all_users_in_db() {
		$db = $this->task->get_job_db();
		$formats = array( '%d', '%d', '%s' );

		$values = array( 'site_id' => 1, 'member_query_id' => $this->post_id, 'user_id' => 'a' );
		$db->insert( 'member_query_users', $values, $formats );

		$values = array( 'site_id' => 1, 'member_query_id' => $this->post_id, 'user_id' => 'b' );
		$db->insert( 'member_query_users', $values, $formats );

		$values = array( 'site_id' => 1, 'member_query_id' => $this->post_id, 'user_id' => 'c' );
		$db->insert( 'member_query_users', $values, $formats );

		$values = array( 'site_id' => 2, 'member_query_id' => $this->post_id, 'user_id' => 'c' );
		$db->insert( 'member_query_users', $values, $formats );

		$values = array( 'site_id' => 3, 'member_query_id' => $this->post_id, 'user_id' => 'c' );
		$db->insert( 'member_query_users', $values, $formats );

		$this->task->clear_users();
		$actual = $db->get_var( "select count(*) from member_query_users where site_id = 1 and member_query_id = {$this->post_id}" );

		$this->assertEquals( 0, $actual );

		$actual = $db->get_var( "select count(*) from member_query_users" );

		$this->assertEquals( 2, $actual );
	}

	function test_it_can_clear_all_members_in_db() {
		$db = $this->task->get_job_db();
		$formats = array( '%d', '%d', '%s' );

		$values = array( 'site_id' => 1, 'member_query_id' => $this->post_id, 'user_id' => 'a' );
		$db->insert( 'member_query_results', $values, $formats );

		$values = array( 'site_id' => 1, 'member_query_id' => $this->post_id, 'user_id' => 'b' );
		$db->insert( 'member_query_results', $values, $formats );

		$values = array( 'site_id' => 1, 'member_query_id' => $this->post_id, 'user_id' => 'c' );
		$db->insert( 'member_query_results', $values, $formats );

		$values = array( 'site_id' => 2, 'member_query_id' => $this->post_id, 'user_id' => 'c' );
		$db->insert( 'member_query_results', $values, $formats );

		$values = array( 'site_id' => 3, 'member_query_id' => $this->post_id, 'user_id' => 'c' );
		$db->insert( 'member_query_results', $values, $formats );

		$this->task->clear_results();
		$actual = $db->get_var( "select count(*) from member_query_results where site_id = 1 and member_query_id = {$this->post_id}" );

		$this->assertEquals( 0, $actual );

		$actual = $db->get_var( 'select count(*) from member_query_results' );

		$this->assertEquals( 2, $actual );
	}

}
