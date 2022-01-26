<?php

namespace Cmmarslender\Timer;

class TimerTests extends TestCase {

	/**
	 * Tests that make sure the timer is properly reset
	 *
	 * @param $timer
	 */
	protected function _reset_tests( $timer ) {
		$this->assertFalse( $timer->is_running() );
		$this->assertSame( Timer::STATE_RESET, $timer->state );
		$this->assertSame( 0, $timer->start_time );
		$this->assertSame( 0, $timer->stop_time );
		$this->assertSame( 0, $timer->total_items );
		$this->assertSame( 0, $timer->current_item );
	}

	public function test_new_object_is_reset() {
		$timer = new Timer();

		$this->_reset_tests( $timer );
	}

	public function test_reset_works() {
		$timer = new Timer();

		// Set some non-default values on everything
		$timer->start();
		$timer->stop_time = time() + 100;
		$timer->set_total_items( 100 );
		$timer->tick();

		// Now reset the timer
		$timer->reset();

		$this->_reset_tests( $timer );
	}

	public function test_unstarted_timer_returns_zero_time_elapsed() {
		 $timer = new Timer();

		 $this->assertEquals( 0, $timer->elapsed_time() );
	}

	public function test_timer_reports_running() {
		$timer = new Timer();
		$timer->start();

		$this->assertTrue( $timer->is_running() );
		$this->assertSame( Timer::STATE_RUNNING, $timer->state );
	}

	public function test_timer_reports_stopped() {
		$timer = new Timer();

		// Should not report running when reset (never started)
		$this->assertFalse( $timer->is_running() );

		$timer->start();
		$timer->stop();

		$this->assertFalse( $timer->is_running() );
		$this->assertSame( Timer::STATE_STOPPED, $timer->state );
	}

	public function test_set_total_items_works() {
		$timer = new Timer();

		$timer->set_total_items( 100 );

		$this->assertEquals( 100, $timer->total_items );
	}

	public function test_tick_increments_items() {
		$timer = new Timer();

		// Start with a known value, just to be certain
		$timer->current_item = 0;
		$timer->tick();

		$this->assertSame( 1, $timer->current_item );
	}

	public function test_average_calculations() {
		$timer = new Timer();

		// This should false if the timer has not been started
		$this->assertFalse( $timer->average() );

		$timer->start();
		$timer->stop();
		$timer->reset();

		// Should also return false if reset
		$this->assertFalse( $timer->average() );

		// Set some known values
		$timer->start();
		$timer->stop();
		$timer->stop_time = ( $timer->start_time + 100 );
		$timer->set_total_items( 100 );
		$timer->current_item = 4;

		$this->assertEquals( 25, $timer->average() );
	}

	public function test_remaining_time_calculations() {
		$timer = new Timer();

		// Set up an average of 5 seconds per item
		$timer->set_total_items( 20 );
		$timer->start();
		$timer->stop_time = $timer->start_time + 20;
		$timer->current_item = 4;

		$this->assertEquals( 80, $timer->remaining_time() );
	}

	public function test_percent_calcuations() {
		$timer = new Timer();

		$timer->set_total_items( 100 );
		$timer->tick();
		$timer->tick();
		$timer->tick();
		$timer->tick();
		$timer->tick();

		$this->assertEquals( 5 , $timer->percent_complete() );
	}

	public function test_elapsed_time_calculations_while_stopped() {
		$timer = new Timer();

		$timer->start();
		$timer->stop();
		$timer->stop_time = $timer->start_time + 50;

		$this->assertEquals( 50, $timer->elapsed_time() );
		sleep(2);
		$this->assertEquals( 50, $timer->elapsed_time() );
	}

	public function test_elapsed_time_calculations_while_running() {
		$timer = new Timer();

		$timer->start();
		$this->assertEquals( 0, $timer->elapsed_time() );
		sleep(1);
		$this->assertEquals( 1, $timer->elapsed_time() );
	}

	public function test_friendly_times() {
		$timer = new Timer();

		$t = $timer->friendly_times( 1 );
		$this->assertEquals( 0, $t['hours'] );
		$this->assertEquals( 0, $t['minutes'] );
		$this->assertEquals( 1, $t['seconds'] );

		$t = $timer->friendly_times( 60 );
		$this->assertEquals( 0, $t['hours'] );
		$this->assertEquals( 1, $t['minutes'] );
		$this->assertEquals( 0, $t['seconds'] );

		$t = $timer->friendly_times( 61 );
		$this->assertEquals( 0, $t['hours'] );
		$this->assertEquals( 1, $t['minutes'] );
		$this->assertEquals( 1, $t['seconds'] );

		$t = $timer->friendly_times( 119 );
		$this->assertEquals( 0, $t['hours'] );
		$this->assertEquals( 1, $t['minutes'] );
		$this->assertEquals( 59, $t['seconds'] );

		$t = $timer->friendly_times( 120 );
		$this->assertEquals( 0, $t['hours'] );
		$this->assertEquals( 2, $t['minutes'] );
		$this->assertEquals( 0, $t['seconds'] );

		$t = $timer->friendly_times( 3600 );
		$this->assertEquals( 1, $t['hours'] );
		$this->assertEquals( 0, $t['minutes'] );
		$this->assertEquals( 0, $t['seconds'] );

		$t = $timer->friendly_times( 3601 );
		$this->assertEquals( 1, $t['hours'] );
		$this->assertEquals( 0, $t['minutes'] );
		$this->assertEquals( 1, $t['seconds'] );

		$t = $timer->friendly_times( 3660 );
		$this->assertEquals( 1, $t['hours'] );
		$this->assertEquals( 1, $t['minutes'] );
		$this->assertEquals( 0, $t['seconds'] );

		$t = $timer->friendly_times( 3661 );
		$this->assertEquals( 1, $t['hours'] );
		$this->assertEquals( 1, $t['minutes'] );
		$this->assertEquals( 1, $t['seconds'] );
	}

	public function test_format_time() {
		$timer = new Timer();

		$this->assertEquals( "0:00:01", $timer->format_time( 1 ) );
		$this->assertEquals( "0:01:01", $timer->format_time( 61 ) );
		$this->assertEquals( "1:01:01", $timer->format_time( 3661 ) );
		$this->assertEquals( "26:01:01", $timer->format_time( 93661 ) );
	}

}
