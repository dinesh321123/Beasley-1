<?php

namespace GreaterMedia\Gigya\Sync;

class TempDatabase {

	static public $instance = null;
	static public function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new TempDatabase();
		}

		return self::$instance;
	}

	public $job_db = null;

	function execute( $script ) {
		$wpdb = $this->get_db();
		return $wpdb->query( $script );
	}

	function get_db() {
		if ( $this->has_job_db() ) {
			return $this->get_job_db();
		} else {
			global $wpdb;
			return $wpdb;
		}
	}

	function get_job_db() {
		if ( is_null( $this->job_db ) ) {
			$this->job_db = new \wpdb(
				GMR_JOB_DB_USER,
				GMR_JOB_DB_PASSWORD,
				GMR_JOB_DB_NAME,
				GMR_JOB_DB_HOST
			);
		}

		return $this->job_db;
	}

	function has_job_db() {
		return defined( 'GMR_JOB_DB' ) && GMR_JOB_DB;
	}

}
