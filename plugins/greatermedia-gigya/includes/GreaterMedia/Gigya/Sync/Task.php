<?php

namespace GreaterMedia\Gigya\Sync;

class Task {

	public $params           = array();
	public $max_retries      = 3;
	public $aborted          = false;
	public $failure          = null;
	public $did_system_error = false;

	// TODO: will default to disabled in production
	public $log_disabled = false;

	public $message_types = array(
		'error'
	);
	/* for testing */
	/*
	public $message_types = array(
		'register',
		'enqueue',
		'abort',
		'retry',
	);
	*/

	function get_task_name() {
		return 'task';
	}

	function get_async_action() {
		return $this->get_task_name() . '_async_job';
	}

	function get_task_priority() {
		return 'normal';
	}

	function register() {
		$this->log( 'register' );

		add_action(
			$this->get_async_action(), array( $this, 'execute' )
		);
	}

	function enqueue( $params = array() ) {
		$this->params = $params;
		$this->log( 'enqueue' );

		return wp_async_task_add(
			$this->get_async_action(),
			$params,
			$this->get_task_priority()
		);
	}

	function execute( $params ) {
		$this->params = $params;
		$this->log( 'execute' );

		try {
			$this->set_error_trap();
			$start_time = microtime( true );
			$proceed    = $this->before();

			if ( $proceed ) {
				$this->log_attempt();

				$result = $this->run();
				$this->log( 'after', $result );

				$stop_time = microtime( true );
				$run_time  = $stop_time - $start_time;

				$this->log( 'after', $run_time );
				$this->after( $result );
			} else {
				$this->aborted = true;
				$this->log( 'abort' );
			}
		} catch (\Exception $err) {
			$this->log( 'error', $err->getMessage() );
			$this->recover( $err );
		} finally {
			$this->clear_error_trap();
		}
	}

	function before() {
		return true;
	}

	function run() {

	}

	function after( $result ) {

	}

	function recover( $error ) {
		if ( $this->can_retry() ) {
			$this->retry();
		} else {
			$this->fail( $error );
		}
	}

	function fail( $error ) {
		$this->failure = $error;
	}

	function did_fail() {
		return ! is_null( $this->failure );
	}

	function retry() {
		$this->log( 'retry' );

		// WARNING: Should NOT export params here
		// else internal retries will be lost
		// resulting in infinite retries => stack overflow
		$this->enqueue( $this->params );
	}

	function can_retry() {
		if ( $this->max_retries <= 0 ) {
			return false;
		} else {
			return $this->get_retries() < $this->max_retries;
		}
	}

	function log_attempt() {
		if ( array_key_exists( 'retries', $this->params ) ) {
			$retries = $this->params['retries'];
		} else {
			$retries = 0;
		}

		$this->params['retries'] = ++$retries;
	}

	function get_param( $key ) {
		return $this->params[ $key ];
	}

	function set_param( $key, $value ) {
		$this->params[ $key ] = $value;
	}

	function has_param( $key ) {
		return array_key_exists( $key, $this->params );
	}

	function get_retries() {
		if ( $this->has_param( 'retries' ) ) {
			return $this->get_param( 'retries' );
		} else {
			return 0;
		}
	}

	function can_log( $type ) {
		return
			defined( 'DOING_ASYNC' ) && DOING_ASYNC &&
			in_array( $type, $this->message_types );
	}

	function log() {
		if ( defined( 'PHPUNIT_RUNNER' ) || $this->log_disabled ) {
			return;
		}

		$count = func_num_args();

		if ( $count >= 1 ) {
			$args = func_get_args();
			$type = $args[0];

			array_shift( $args );

			if ( $this->can_log( $type ) ) {
				$task_name     = $this->get_task_name();
				$task_priority = $this->get_task_priority();
				$params        = json_encode( $this->params );
				$pid           = getmypid();

				error_log(
					"Task[{$pid}] ($task_name.$type@$task_priority) - ($params)"
				);

				if ( count( $args ) > 0 ) {
					error_log( "\t" . json_encode( $args ) );
				}
			}
		}
	}

	function export_params() {
		$params = $this->params;
		unset( $params['retries'] );

		return $params;
	}

	function set_error_trap() {
		if ( ! defined( 'PHPUNIT_RUNNER' ) ) {
			set_error_handler( array( $this, 'on_system_error' ) );
		}
	}

	function clear_error_trap() {
		restore_error_handler();
	}

	function on_system_error() {
		$args = func_get_args();
		error_log( 'System Error: ' . implode( "\n", $args ) );
		$this->did_system_error = true;
	}

}
