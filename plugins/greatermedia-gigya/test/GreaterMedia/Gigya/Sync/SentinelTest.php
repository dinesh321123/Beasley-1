<?php

namespace GreaterMedia\Gigya\Sync;

class SentinelTest extends \WP_UnitTestCase {

	public $sentinel;

	function setUp() {
		parent::setUp();

		$this->sentinel = new Sentinel( 1 );
	}

	function tearDown() {
		parent::tearDown();
	}

	function test_it_stores_member_query_id() {
		$sentinel = new Sentinel( 1 );
		$this->assertEquals( 1, $sentinel->member_query_id );
	}

	function test_it_can_store_checksum_meta_key() {
		$checksum = md5( strtotime( 'now' ) );
		$this->sentinel->set_checksum( $checksum );

		$actual = $this->sentinel->get_checksum();
		$this->assertEquals( $checksum, $actual );
	}

	function test_it_can_store_task_progress() {
		$progress  = 50;
		$task_type = 'profile';
		$this->sentinel->set_task_progress( $task_type, $progress );

		$actual = $this->sentinel->get_task_progress( $task_type );
		$this->assertEquals( 50, $actual );
	}

	function test_it_knows_task_type_has_not_completed() {
		$actual = $this->sentinel->is_task_type_complete( 'profile' );
		$this->assertFalse( $actual );
	}

	function test_it_knows_task_type_has_completed() {
		$this->sentinel->set_task_progress( 'profile', 100 );
		$actual = $this->sentinel->is_task_type_complete( 'profile' );
		$this->assertTrue( $actual );
	}

	function test_it_knows_overall_progress_for_export_mode() {
		$this->sentinel->set_task_meta( 'mode',  'export' );
		$this->sentinel->set_task_progress( 'profile', 50 );
		$this->sentinel->set_task_progress( 'data_store', 50 );
		$this->sentinel->set_task_progress( 'compile_results',  50 );
		$this->sentinel->set_task_progress( 'export_results', 50 );

		$this->assertEquals( 50, $this->sentinel->get_progress() );
	}

	function test_it_knows_overall_progress_for_preview_mode() {
		$this->sentinel->set_task_meta( 'mode',  'preview' );
		$this->sentinel->set_task_progress( 'profile', 50 );
		$this->sentinel->set_task_progress( 'data_store', 50 );
		$this->sentinel->set_task_progress( 'compile_results',  50 );

		$this->assertEquals( 50, $this->sentinel->get_progress() );
	}

	function test_it_knows_overall_progress_for_intermediate_progress() {
		$this->sentinel->set_task_meta( 'mode',  'preview' );
		$this->sentinel->set_task_progress( 'profile', 10 );
		$this->sentinel->set_task_progress( 'data_store', 70 );
		$this->sentinel->set_task_progress( 'compile_results',  0 );

		$this->assertEquals( 26, $this->sentinel->get_progress() );
	}

	function test_it_knows_overall_progress_on_completion() {
		$this->sentinel->set_task_meta( 'mode',  'preview' );
		$this->sentinel->set_task_progress( 'profile', 100 );
		$this->sentinel->set_task_progress( 'data_store', 100 );
		$this->sentinel->set_task_progress( 'compile_results',  100 );

		$this->assertEquals( 100, $this->sentinel->get_progress() );
	}

	function test_it_knows_it_can_not_compile_results_if_profile_query_is_pending() {
		$this->sentinel->set_task_progress( 'profile', 10 );
		$actual = $this->sentinel->can_compile_results();

		$this->assertFalse( $actual );
	}

	function test_it_knows_it_can_not_compile_results_if_data_store_query_is_pending() {
		$this->sentinel->set_task_progress( 'data_store', 20 );
		$actual = $this->sentinel->can_compile_results();

		$this->assertFalse( $actual );
	}

	function test_it_can_compile_results_if_fetch_queries_have_completed() {
		$this->sentinel->set_task_progress( 'data_store', 100 );
		$this->sentinel->set_task_progress( 'profile', 100 );
		$actual = $this->sentinel->can_compile_results();

		$this->assertTrue( $actual );
	}

	function test_it_can_not_export_results_if_results_not_compiled() {
		$actual = $this->sentinel->can_export_results();
		$this->assertFalse( $actual );
	}

	function test_it_can_export_results_if_results_were_compiled() {
		$this->sentinel->set_task_progress( 'data_store', 100 );
		$this->sentinel->set_task_progress( 'profile', 100 );
		$this->sentinel->set_task_progress( 'compile_results', 100 );

		$actual = $this->sentinel->can_export_results();
		$this->assertTrue( $actual );
	}

	function test_it_knows_if_checksum_is_invalid() {
		$this->assertFalse( $this->sentinel->verify_checksum( 'foo' ) );
	}

	function test_it_knows_if_checksum_is_valid() {
		$this->sentinel->set_task_meta( 'checksum', 'foo' );
		$this->assertTrue( $this->sentinel->verify_checksum( 'foo' ) );
	}

	function test_it_can_reset_meta_keys_for_task() {
		$this->sentinel->reset();

		$this->assertFalse( get_post_meta( 'mqsm_checksum' ) );
		$this->assertFalse( get_post_meta( 'mqsm_mode' ) );
		$this->assertFalse( get_post_meta( 'mqsm_profile_progress' ) );
		$this->assertFalse( get_post_meta( 'mqsm_compile_results_progress' ) );
		$this->assertFalse( get_post_meta( 'mqsm_export_results_progress' ) );
	}
}
