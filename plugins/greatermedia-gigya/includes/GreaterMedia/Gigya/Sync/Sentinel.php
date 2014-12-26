<?php

namespace GreaterMedia\Gigya\Sync;

class Sentinel {

	public $member_query_id;
	public $meta_prefix = 'mqsm'; // member_query_sync_meta
	public $params;
	public $timeout = 600; // 10 minutes

	function __construct( $member_query_id, $params = array() ) {
		$this->member_query_id = $member_query_id;
		$this->params          = $params;
	}

	function get_task_meta( $key ) {
		$key   = "{$this->meta_prefix}_$key";
		$value = get_post_meta( $this->member_query_id, $key, true );

		return $value;
	}

	function set_task_meta( $key, $value ) {
		$key = "{$this->meta_prefix}_$key";
		update_post_meta( $this->member_query_id, $key, $value );
	}

	function clear_task_meta( $key ) {
		$key = "{$this->meta_prefix}_$key";
		delete_post_meta( $this->member_query_id, $key );
	}

	function set_checksum( $checksum ) {
		$this->set_task_meta( 'checksum', $checksum );
	}

	function get_checksum() {
		return $this->get_task_meta( 'checksum' );
	}

	function get_task_progress( $task_type ) {
		$progress = $this->get_task_meta( $task_type . '_progress' );
		return intval( $progress );
	}

	function set_task_progress( $task_type, $progress ) {
		$this->set_task_meta( $task_type . '_progress', $progress );
	}

	function get_status_code() {
		$status_code = $this->get_task_meta( 'status_code' );
		if ( $status_code === '' ) {
			$status_code = 'pending';
		}

		return $status_code;
	}

	function set_status_code( $status_code ) {
		$this->set_task_meta( 'status_code', $status_code );
		if ( $status_code === 'completed' ) {
			$this->set_end_time( time() );
		}
	}

	function get_start_time() {
		$start_time = $this->get_task_meta( 'start_time' );
		if ( $start_time === '' ) {
			$start_time = 0;
		} else {
			$start_time = intval( $start_time );
		}

		return $start_time;
	}

	function set_start_time( $start_time ) {
		$this->set_task_meta( 'start_time', strval( $start_time ) );
	}

	function get_end_time() {
		if ( $this->is_running() ) {
			return time();
		} else {
			$end_time = $this->get_task_meta( 'end_time' );
			if ( $end_time === '' ) {
				$end_time = 0;
			} else {
				$end_time = intval( $end_time );
			}
		}

		return $end_time;
	}

	function set_end_time( $end_time ) {
		$this->set_task_meta( 'end_time', strval( $end_time ) );
	}

	function get_duration() {
		$start_time = $this->get_start_time();
		$end_time   = $this->get_end_time();
		$duration   = $end_time - $start_time;

		return $duration;
	}

	function get_last_export() {
		$end_time = $this->get_task_meta( 'end_time' );
		if ( $end_time !== '' ) {
			return $this->get_end_time();
		} else {
			return 0;
		}
	}

	function is_running() {
		$status_code = $this->get_status_code();
		return $status_code === 'pending' || $status_code === 'running';
	}

	function has_expired() {
		return $this->get_duration() > $this->timeout;
	}

	function is_task_type_complete( $task_type ) {
		$progress = $this->get_task_progress( $task_type );
		return $progress === 100;
	}

	function has_errors() {
		$errors = $this->get_task_meta( 'errors' );
		return $errors !== '';
	}

	function add_error( $message ) {
		$errors = $this->get_errors();
		$errors[] = $message;

		$this->set_errors( $errors );
	}

	function set_errors( $messages ) {
		$json = json_encode( $messages );
		$this->set_task_meta( 'errors', $json );
		$this->set_status_code( 'completed' );
	}

	function get_errors() {
		if ( $this->has_errors() ) {
			$json = $this->get_task_meta( 'errors' );
			return json_decode( $json, true );
		} else {
			return [];
		}
	}

	function get_progress() {
		if ( $this->has_errors() ) {
			return 100;
		}

		$status_code = $this->get_status_code();
		if ( $status_code === 'completed' ) {
			return 100;
		}

		// TODO: If conjunction were known this could be dynamic
		$parts = array(
			$this->get_task_progress( 'profile' ),
			$this->get_task_progress( 'data_store' ),
			$this->get_task_progress( 'compile_results' ),
		);

		$mode = $this->get_mode();
		if ( $mode === 'export' ) {
			$parts[] = $this->get_task_progress( 'export_results' );
		} else {
			$parts[] = $this->get_task_progress( 'preview_results' );
		}

		$total_parts    = count( $parts );
		$total_progress = array_sum( $parts );
		$total          = 100 * $total_parts;
		$progress       = $total_progress / $total * 100;
		$progress       = (int)$progress;

		return $progress;
	}

	function can_compile_results() {
		if ( $this->has_errors() ) {
			return false;
		}

		$conjunction = $this->get_conjunction();

		if ( $conjunction === 'any' ) {
			return $this->get_task_progress( 'profile' ) === 100 ||
				$this->get_task_progress( 'data_store' ) === 100;
		} else {
			return $this->get_task_progress( 'profile' ) === 100 &&
				$this->get_task_progress( 'data_store' ) === 100;
		}
	}

	function can_export_results() {
		return $this->can_compile_results() &&
			$this->get_task_progress( 'compile_results' ) === 100;
	}

	function has_completed() {
		if ( ! $this->has_errors() ) {
			return $this->get_progress() === 100;
		} else {
			return true;
		}
	}

	function get_preview_results() {
		if ( $this->has_completed() ) {
			$results = get_post_meta(
				$this->member_query_id,
				'member_query_preview_results',
				true
			);

			$json = json_decode( $results, true );
			return $json;
		} else {
			throw new \Exception(
				"Sentinel: Query has not completed - {$this->member_query_id}"
			);
		}
	}

	function verify_checksum( $checksum ) {
		return $checksum !== '' && $checksum === $this->get_checksum();
	}

	function reset() {
		$this->clear_task_meta( 'profile_progress' );
		$this->clear_task_meta( 'data_store_progress' );
		$this->clear_task_meta( 'compile_results_progress' );
		$this->clear_task_meta( 'export_results_progress' );
		$this->clear_task_meta( 'errors' );
	}

	function get_param( $key ) {
		if ( array_key_exists( $key, $this->params ) ) {
			return $this->params[ $key ];
		} else {
			return '';
		}
	}

	function get_mode() {
		return $this->get_param( 'mode' );
	}

	function get_conjunction() {
		return $this->get_param( 'conjunction' );
	}

	function set_email_segment_id( $email_segment_id ) {
		$this->set_task_meta( 'email_segment_id', $email_segment_id );
	}

	function get_email_segment_id() {
		return $this->get_task_meta( 'email_segment_id' );
	}

	function get_status_meta() {
		$meta                   = array();
		$meta['statusCode']     = $this->get_status_code();
		$meta['emailSegmentID'] = $this->get_email_segment_id();
		$meta['memberQueryID']  = $this->member_query_id;
		$meta['progress']       = $this->get_progress();
		$meta['lastExport']     = $this->get_last_export();

		if ( $this->has_errors() ) {
			$meta['errors'] = $this->get_errors();
		}

		if ( $meta['statusCode'] === 'completed' ) {
			$meta['duration'] = $this->get_duration();
		}

		return $meta;
	}

}
