<?php

namespace WordPress\Utils;

class AssetLocator {

	public $container;

	function get_asset_dirs() {
		return $this->container->config->get_asset_dirs();
	}

	function find( $path ) {
		$path       = $this->repair_path( $path );
		$asset_dirs = $this->get_asset_dirs();

		foreach ( $asset_dirs as $asset_dir ) {
			$full_path = $this->find_in_dir( $asset_dir, $path );

			if ( $full_path !== false ) {
				return $full_path;
			}
		}

		$this->container->error_reporter->log_not_found( $path );

		return false;
	}

	function find_in_dir( $asset_dir, $path ) {
		$path = str_replace( '\\', '/', $path );

		if ( strpos( $path, '/' ) === 0 ) {
			$full_path = $asset_dir . $path;
		} else {
			$full_path = $asset_dir . '/' . $path;
		}

		if ( file_exists( $full_path ) ) {
			return $full_path;
		} else {
			return $this->find_substitute( $full_path );
		}
	}

	function repair_path( $path ) {
		$path = preg_replace( '/^\\\\Pics/', '', $path, 1 );
		$path = preg_replace( '/\s+.mp3$/', '.mp3', $path, 1 );
		$path = preg_replace( '/\s+.jpeg$/', '.jpeg', $path, 1 );
		$path = preg_replace( '/\s+.jpg$/', '.jpg', $path, 1 );

		return $path;
	}

	function find_substitute( $path ) {
		// disabling for now
		return false;

		if ( strpos( $path, '/EventCalendars/' ) !== false ) {
			return $this->find_event_calendar_substitute( $path );
		} else {
			return false;
		}
	}

	function find_event_calendar_substitute( $path ) {
		$pattern = preg_replace( '#EventCalendars/.*/#', 'EventCalendars/*/', $path, 1 );
		$files   = glob( $pattern );

		if ( count( $files ) > 0 ) {
			$file = $files[0];

			if ( file_exists( $file ) ) {
				return $file;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}


}
