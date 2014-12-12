<?php

namespace GreaterMedia\Gigya\Sync;

class UserCollectorTest extends \WP_UnitTestCase {

	public $collector;

	function setUp() {
		parent::setUp();

		$this->collector = new UserCollector( 10, 11 );
		$this->collector->clear();
	}

	function tearDown() {
		$this->collector->clear();
		parent::tearDown();
	}

	function test_it_can_prepare_values_for_insert() {
		$db     = $this->collector->get_job_db();
		$format = '( %d, %d, %s )';
		$inputs = array( 20, 30, 'foo' );
		$actual = $this->collector->prepare_values( $db, $inputs, $format );

		$this->assertEquals( "( 20, 30, 'foo' )", $actual );
	}

	function count_rows() {
		$db  = $this->collector->get_job_db();
		$row = $db->get_row( 'select count(*) as total from member_query_users' );

		return $row->total;
	}

	function test_it_can_remove_all_users() {
		$this->collector->clear();
		$this->assertEquals( 0, $this->count_rows() );
	}

	function test_it_can_collect_empty_list_of_user_ids() {
		$this->collector->clear();
		$this->collector->collect( array(), 'profile' );

		$this->assertEquals( 0, $this->count_rows() );
	}

	function test_it_can_collect_list_of_user_ids() {
		$users = array(
			'lorem',
			'ipsum',
			'dolor',
			'sit',
			'amet',
		);

		$this->collector->collect( $users, 'profile' );
		$actual = $this->count_rows();

		$this->assertEquals( 5, $actual );
	}

	function test_it_can_clear_list_after_collecting_users() {
		$users = array(
			'lorem',
			'ipsum',
			'dolor',
			'sit',
			'amet',
		);

		$this->collector->collect( $users, 'profile' );
		$this->collector->clear();

		$this->assertEquals( 0, $this->count_rows() );
	}

}
