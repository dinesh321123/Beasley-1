<?php

namespace GreaterMedia;

class MigrationConfig {

	function load( $config_file ) {
		if ( file_exists( $config_file ) ) {
			$json = file_get_contents( $config_file, 'r' );
			return $this->parse( $json );
		} else {
			\WP_CLI::error( "Config File not found - $config_file" );
			return false;
		}
	}

	function parse( $json ) {
		$data = json_decode( $json, true );

		if ( json_last_error() === JSON_ERROR_NONE ) {
			return $data;
		} else {
			\WP_CLI::error( 'Failed to parse config JSON' );
			return false;
		}
	}

}
