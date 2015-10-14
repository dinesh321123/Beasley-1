<?php

namespace GreaterMedia\Utils;

class ErrorReporter {

	public $errors = array();
	public $container;

	function log() {
		$args = func_get_args();
		$this->errors[] = $args;
	}

	function log_not_found( $path, $parent = null ) {
		/*
		if ( is_null( $parent ) ) {
			$this->log( "File not found: $path" );
		} else {
			$this->log( "File attached to ($parent) not found: $path" );
		}
*/
		$this->log( $path );
	}

	function save_report() {
		$report_file = $this->get_error_log_file();
		$file        = fopen( $report_file, 'w' );
		$total       = count( $this->errors );

		if ( $total > 0 ) {
			\WP_CLI::warning( "Migration Successful, with $total errors" );
			$notify = new \WordPress\Utils\ProgressBar( str_pad( "Writing $total Errors", 40, ' ' ), $total );

			foreach ( $this->errors as $error ) {
				$line  = implode( ' ', $error );
				$line .= "\n";

				fwrite( $file, $line );
				$notify->tick();
			}

			$notify->finish();
		} else {
			\WP_CLI::success( 'Migration Successful, 0 Errors occurred!' );
		}
	}

	function get_error_log_file() {
		$site_dir = $this->container->config->get_site_dir();
		$log_file = $this->container->config->get_error_option( 'log_file' );

		return $site_dir . '/output/' . $log_file;
	}

}
