<?php

namespace GreaterMedia\Gigya\Sync;

class TaskFactoryTest extends \WP_UnitTestCase {

	public $factory;

	function setUp() {
		parent::setUp();

		$this->factory = new TaskFactory();
	}

	function tearDown() {
		parent::tearDown();
	}

	function test_it_has_profile_task_type() {
		$this->assertNotNull( $this->factory->get_task( 'profile' ) );
	}

	function test_it_raises_an_exception_for_unknown_task_lookups() {
		$this->setExpectedException( 'Exception' );
		$this->factory->get_task( 'unknown_task' );
	}

	function test_it_allows_replacing_task_types() {
		$this->factory->set_task( 'profile', 'GreaterMedia\Gigya\Sync\DummyProfileTask' );
		$actual = $this->factory->build( 'profile' );
		$this->assertInstanceOf( 'GreaterMedia\Gigya\Sync\DummyProfileTask', $actual );
	}

}

class DummyProfileTask extends Task {

}
