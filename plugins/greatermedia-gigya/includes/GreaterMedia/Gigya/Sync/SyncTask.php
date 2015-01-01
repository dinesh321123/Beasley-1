<?php

namespace GreaterMedia\Gigya\Sync;

class SyncTask extends Task {

	public $sentinel;
	public $task_factory;
	public $message_types = array(
		'enqueue',
		'execute',
		'retry',
		'abort',
		'error',
	);

	function get_task_name() {
		return 'sync_task';
	}

	function get_sentinel() {
		if ( is_null( $this->sentinel ) ) {
			$this->sentinel = new Sentinel(
				$this->get_member_query_id(),
				$this->params
			);
		}

		return $this->sentinel;
	}

	function get_site_id() {
		return $this->get_param( 'site_id' );
	}

	function get_member_query_id() {
		return $this->get_param( 'member_query_id' );
	}

	function get_mode() {
		return $this->get_param( 'mode' );
	}

	function get_checksum() {
		return $this->get_param( 'checksum' );
	}

	function verify_checksum() {
		return $this->get_sentinel()->verify_checksum(
			$this->get_checksum()
		);
	}

	function before() {
		if ( $this->verify_checksum() ) {
			$sentinel = $this->get_sentinel();
			if ( $sentinel->get_status_code() === 'running' && $sentinel->has_expired() ) {
				$sentinel->set_status_code( 'completed' );
				$sentinel->add_error( 'Error: The query timed out' );

				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	function get_task_factory() {
		if ( is_null( $this->task_factory ) ) {
			$this->task_factory = new TaskFactory();
		}

		return $this->task_factory;
	}

	function fail( $error ) {
		$sentinel = $this->get_sentinel();
		$sentinel->add_error( $error->getMessage() );
	}

}
