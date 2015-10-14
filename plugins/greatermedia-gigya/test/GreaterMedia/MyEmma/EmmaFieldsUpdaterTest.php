<?php

namespace GreaterMedia\MyEmma;

class EmmaFieldsUpdaterTest extends \WP_UnitTestCase {

	public $updater;

	function setUp() {
		parent::setUp();

		$settings    = array(
			'emma_account_id'  => '1746533',
			'emma_public_key'  => '3e89a3b76be875952b48',
			'emma_private_key' => '519231e76466c2f0bfc0'
		);

		update_option( 'member_query_settings', json_encode( $settings ) );

		$this->updater = new EmmaFieldsUpdater();
	}

	function tearDown() {
		parent::tearDown();
	}

	function test_it_knows_all_fields_to_add_if_no_active_fields() {
		$actual = $this->updater->calc_fields_to_add( array() );
		$actual = array_column( $actual, 'shortcut_name' );

		$this->assertContains( 'first_name', $actual );
		$this->assertContains( 'last_name', $actual );
		$this->assertContains( 'birthday', $actual );
	}

	function test_it_knows_no_fields_to_add_if_all_active_fields() {
		$active_fields = array(
			array( 'shortcut_name' => 'first_name' ),
			array( 'shortcut_name' => 'last_name' ),
			array( 'shortcut_name' => 'birthday' ),
		);

		$actual = $this->updater->calc_fields_to_add( $active_fields );
		$this->assertEmpty( $actual );
	}

	function test_it_knows_some_fields_to_add_if_all_active_fields() {
		$active_fields = array(
			array( 'shortcut_name' => 'first_name' ),
			array( 'shortcut_name' => 'last_name' ),
			array( 'shortcut_name' => 'birthday' ),
		);

		$this->updater->fields['foo'] = array( 'shortcut_name' => 'foo' );
		$this->updater->fields['bar'] = array( 'shortcut_name' => 'bar' );

		$actual = $this->updater->calc_fields_to_add( $active_fields );
		$actual = array_column( $actual, 'shortcut_name' );

		$this->assertContains( 'foo', $actual );
		$this->assertContains( 'bar', $actual );
	}

	function test_it_can_fetch_active_fields() {
		$actual = $this->updater->fetch();
		$this->assertTrue( is_array( $actual ) );
	}

	function _test_it_can_add_new_emma_fields() {
		$fields = array(
			array(
				'shortcut_name' => 'lorem',
				'display_name' => 'lorem',
				'field_type' => 'text',
				'column_order' => 10,
			),
			array(
				'shortcut_name' => 'ipsum',
				'display_name' => 'ipsum',
				'field_type' => 'date',
				'column_order' => 11,
			),
		);

		try {
			$actual = $this->updater->add_fields( $fields );
			$this->assertNotEmpty( $actual );
		} catch ( \Emma_Invalid_Response_Exception $e ) {
			var_dump( $e->getHttpBody() );
		}

	}

	function test_it_can_update_fields() {
		try {
			$this->updater->update();
		} catch ( \Emma_Invalid_Response_Exception $e ) {
			var_dump( $e->getHttpBody() );
		}
	}

}
