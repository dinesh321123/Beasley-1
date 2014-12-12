<?php

namespace GreaterMedia\Gigya\Sync;

class TempSchemaMigratorTest extends \WP_UnitTestCase {

	public $migrator;

	function setUp() {
		parent::setUp();

		$this->migrator = new TempSchemaMigrator();
	}

	function tearDown() {
		parent::tearDown();
	}

	function test_it_has_a_schema_version_key() {
		$this->assertNotEmpty( $this->migrator->schema_version_key );
	}

	function test_it_defaults_to_starting_version_if_absent() {
		$actual = $this->migrator->get_current_schema_version();
		$this->assertEquals( '0.0.0', $actual );
	}

	function test_it_uses_stored_version_if_present() {
		update_option( 'member_query_temp_schema_version', '2.3.4', true );
		$actual = $this->migrator->get_current_schema_version();
		$this->assertEquals( '2.3.4', $actual );
	}

	function test_it_can_change_schema_version() {
		$this->migrator->change_schema_version( '3.4.5' );
		$actual = $this->migrator->get_current_schema_version();
		$this->assertEquals( '3.4.5', $actual );
	}

	function test_it_knows_can_migrate_from_initial_version() {
		$actual = $this->migrator->can_migrate();
		$this->assertTrue( $actual );
	}

	function test_it_knows_dont_need_to_migrate_if_equal_to_required_version() {
		$this->migrator->change_schema_version( $this->migrator->required_schema_version );
		$actual = $this->migrator->can_migrate();
		$this->assertFalse( $actual );
	}

	function test_it_knows_dont_need_to_migrate_if_greater_than_required_version() {
		$this->migrator->change_schema_version( '100.10.10' );
		$actual = $this->migrator->can_migrate();
		$this->assertFalse( $actual );
	}

	function test_it_knows_path_to_schema_for_version() {
		$actual = $this->migrator->get_path_to_schema_for( '0.0.0' );
		$expected = GMR_GIGYA_PATH . 'scripts/sync/0.0.0.sql';
		$this->assertEquals( $expected, $actual );
	}

	function test_it_will_not_run_script_if_start_version_greater_than_min_version() {
		$actual = $this->migrator->run_if( '1.1.1', '0.0.1' );
		$this->assertFalse( $actual );
	}

	function test_it_will_not_run_script_if_start_version_equals_min_version() {
		$actual = $this->migrator->run_if( '1.1.1', '1.1.1' );
		$this->assertFalse( $actual );
	}

	function test_it_will_run_script_if_start_version_less_than_min_version() {
		$actual = $this->migrator->run_if( '0.0.0', '0.0.1' );
		$this->assertTrue( $actual );
	}

	function test_it_will_not_run_script_if_missing() {
		$actual = $this->migrator->run_if( '0.0.0', '1000.0.1' );
		$this->assertFalse( $actual );
	}

	function test_it_can_execute_script() {
		$schema = 'show databases;';
		$this->migrator->execute( $schema );
	}

	function test_it_can_run_migration_scripts_upto_end_version() {
		$this->migrator->run( '0.0.0', '2.0.0' );

		$query   = 'select * from member_query_users';
		$db      = TempDatabase::get_instance();
		$results = $db->execute( $query );

		$this->assertNotNull( $results );
	}

	function test_it_can_run_migration_if_required() {
		$this->migrator->change_schema_version( '0.0.0' );
		$actual = $this->migrator->migrate();

		$this->assertTrue( $actual );
	}

	function test_it_will_update_schema_version_after_migration() {
		$this->migrator->change_schema_version( '0.0.0' );
		$actual = $this->migrator->migrate();

		$this->assertEquals( $this->migrator->required_schema_version, '0.1.0' );
	}

	function test_it_will_not_migrate_after_successfull_migration() {
		$this->migrator->change_schema_version( '0.0.0' );
		$this->migrator->migrate();
		$actual = $this->migrator->migrate();

		$this->assertFalse( $actual );
	}

}
