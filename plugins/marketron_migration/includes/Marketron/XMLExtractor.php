<?php

namespace Marketron;

class XMLExtractor {

	public $container;

	function extract() {
		$this->unzip();
	}

	function unzip() {
		$config     = $this->container->config;
		$data_files = $config->get_data_files();
		$site_dir   = $config->get_site_dir();

		foreach ( $data_files as $data_file ) {
			$data_file_path = $site_dir . '/marketron_files/' . $data_file;
			$this->unzip_file( $data_file_path );
		}

		$marketron_export = $config->get_marketron_export_file();
	}

	function unzip_file( $data_file ) {
		$data_file_name = basename( $data_file, '.zip' );
		$data_file_dir  = dirname( $data_file );
		$dest           = $data_file_dir . '/' . $data_file_name;
		$update_flag    = $this->container->fresh ? '-u' : '-f';

		$dest_arg      = escapeshellarg( $dest );
		$data_file_arg = escapeshellarg( $data_file );

		system( "mkdir -p $dest_arg" );
		system( "unzip $update_flag -d $dest_arg $data_file_arg" );

		$this->format_files_in_dir( $dest );
	}

	function format_files_in_dir( $dir ) {
		$pattern = "$dir/*.{xml,XML}";
		$files   = glob( $pattern, GLOB_BRACE );
		$files   = preg_grep( '/._formatted.xml$/', $files, PREG_GREP_INVERT );

		foreach ( $files as $file ) {
			$outfile = preg_replace( '/.(XML|xml)$/', '_formatted.xml', $file );
			if ( ! file_exists( $outfile ) ) {
				\WP_CLI::log( 'Cleaning up: ' . basename( $file ) );

				$outfile_arg = escapeshellarg( $outfile );
				$file_arg    = escapeshellarg( $file );

				system( "xmllint --huge --format --output $outfile_arg $file_arg" );

				// Warning: This disables errors after the first run
				touch( $outfile );
			}
		}
	}

}
