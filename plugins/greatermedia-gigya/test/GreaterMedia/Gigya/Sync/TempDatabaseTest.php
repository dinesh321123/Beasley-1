<?php

namespace GreaterMedia\Gigya\Sync;

class TempDatabaseTest extends \WP_UnitTestCase {

	public $db;

	function setUp() {
		parent::setUp();

		$this->db = new TempDatabase();
	}

	function init_constants() {
		if ( ! defined( 'GMR_JOB_DB' ) ) {
			define( 'GMR_JOB_DB', true );
			define( 'GMR_JOB_DB_USER', 'gmr_job_db_user' );
			define( 'GMR_JOB_DB_PASSWORD', '1234' );
			define( 'GMR_JOB_DB_NAME', 'gmr_job_db_test' );
			define( 'GMR_JOB_DB_HOST', 'localhost' );
		}
	}

	function tearDown() {
		parent::tearDown();
	}

	// KLUDGE: exploiting order of tests run
	function test_it_knows_if_job_db_is_absent() {
		$this->assertFalse( $this->db->has_job_db() );
	}

	function test_it_uses_wpdb_if_job_db_is_not_available() {
		$db = $this->db->get_db();
		global $wpdb;

		$this->assertSame( $db, $wpdb );
	}

	function test_it_can_run_query_on_wpdb() {
		$script = 'show databases';
		$actual = $this->db->execute( $script );
		$this->assertNotEmpty( $actual );
	}

	// END KLUDGE

	function test_it_knows_if_job_db_is_available() {
		$this->init_constants();
		$this->assertTrue( $this->db->has_job_db() );
	}

	function test_it_can_create_instance_of_job_db() {
		$this->init_constants();
		$job_db = $this->db->get_job_db();

		$this->assertInstanceOf( 'wpdb', $job_db );
	}

	function test_it_uses_job_db_if_available() {
		$this->init_constants();
		$db = $this->db->get_job_db();
		global $wpdb;

		$this->assertNotSame( $db, $wpdb );
	}

	function test_it_can_run_query_on_job_db() {
		$script = 'show databases';
		$actual = $this->db->execute( $script );
		$this->assertNotEmpty( $actual );
	}

}
