<?php

class MockAsyncTaskRunner {

	public $tasks      = array();
	public $counter    = 0;
	public $run_on_add = false;

	static public $instance;
	static public function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new MockAsyncTaskRunner();
		}

		return self::$instance;
	}

	function add( $action, $params = array() ) {
		$task_id = $this->counter++;
		$this->tasks[ $task_id ] = array(
			'task_id' => $task_id,
			'action'  => $action,
			'params'  => $params,
		);

		if ( $this->run_on_add ) {
			$this->execute( $task_id );
		}

		return $task_id;
	}

	function get_task( $task_id ) {
		return $this->tasks[ $task_id ];
	}

	function execute( $task_id ) {
		$task   = $this->get_task( $task_id );
		$action = $task['action'];
		$params = $task['params'];

		do_action( $action, $params );
	}

	function clear() {
		$this->tasks      = array();
		$this->run_on_add = false;
	}

	function count() {
		return count( $this->tasks );
	}

	function last() {
		return $this->get_task( $this->counter - 1 );
	}

	function autorun( $run_on_add ) {
		$this->run_on_add = $run_on_add;
	}

}

function wp_async_task_runner() {
	return \MockAsyncTaskRunner::instance();
}

function wp_async_task_add( $action, $params = array() ) {
	return wp_async_task_runner()->add( $action, $params );
}

function wp_async_task_run( $task_id ) {
	return wp_async_task_runner()->execute( $task_id );
}

function wp_async_task_autorun( $autorun = true ) {
	wp_async_task_runner()->autorun( $autorun );
}

function wp_async_task_last_added() {
	return wp_async_task_runner()->last();
}

function wp_async_task_run_last() {
	$task    = wp_async_task_last_added();
	$task_id = $task['task_id'];

	return wp_async_task_run( $task_id );
}

function wp_async_task_clear() {
	wp_async_task_runner()->clear();
}

function wp_async_task_count() {
	return wp_async_task_runner()->count();
}
