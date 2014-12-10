<?php

namespace GreaterMedia\Gigya\Sync;

class Sentinel {

	public $member_query_id;
	public $meta_prefix = 'mqsm'; // member_query_sync_meta
	public $params;

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

	function is_task_type_complete( $task_type ) {
		$progress = $this->get_task_progress( $task_type );
		return $progress === 100;
	}

	function get_progress() {
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

		error_log( print_r( $parts, true ) );

		$total_parts    = count( $parts );
		$total_progress = array_sum( $parts );
		$total          = 100 * $total_parts;
		$progress       = $total_progress / $total * 100;

		return (int)$progress;
	}

	function can_compile_results() {
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
		return $this->get_progress() === 100;
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

}
