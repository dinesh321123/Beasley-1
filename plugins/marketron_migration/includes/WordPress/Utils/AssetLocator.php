<?php

namespace WordPress\Utils;

class AssetLocator {

	public $container;
	public $library_images = array();
	public $library_mp3s = array();
	public $loaded_library = false;

	function get_asset_dirs() {
		return $this->container->config->get_asset_dirs();
	}

	function find( $path ) {
		if ( $this->use_media_library() ) {
			if ( strpos( $path, 'mp3' ) !== false ) {
				$path = $this->get_random_mp3();
			} else {
				$path = $this->get_random_image();
			}

			return $path;
		}

		$path = $this->repair_path( $path );

		if ( is_dir( $path ) ) {
			$this->container->error_reporter->log(
				"Expected File Path but was: $path"
			);
			return false;
		}

		$asset_dirs = $this->get_asset_dirs();

		foreach ( $asset_dirs as $asset_dir ) {
			$full_path = $this->find_in_dir( $asset_dir, $path );

			if ( $full_path !== false ) {
				if ( ! is_dir( $full_path ) ) {
					return $full_path;
				}
			}
		}

		//$this->container->error_reporter->log_not_found( $path );

		return false;
	}

	function find_in_dir( $asset_dir, $path ) {
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
		$path = str_replace( '\\', '/', $path );
		$path = preg_replace( '/^\/Pics/', '', $path, 1 );
		$path = preg_replace( '/^\/pics/', '', $path, 1 );
		$path = preg_replace( '/^\/media/', '', $path, 1 );
		$path = preg_replace( '/^\/Media/', '', $path, 1 );

		//error_log( 'repaired_path: ' . $path );
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

	function get_random_image() {
		$this->load_library();

		$total = count( $this->library_images );
		$index = rand( 0, $total - 1 );

		$image = $this->library_images[ $index ];
		//error_log( $image );
		return $image;
	}

	function get_random_mp3() {
		$this->load_library();

		$total = count( $this->library_mp3s );
		$index = rand( 0, $total - 1 );

		$mp3 = $this->library_mp3s[ $index ];
		return $mp3;
	}

	function load_library() {
		if ( ! $this->loaded_library ) {
			$this->library_images = $this->load_image_library();
			$this->library_mp3s   = $this->load_audio_library();
			$this->loaded_library = true;
		}
	}

	function load_image_library() {
		$image_dir = $this->get_media_library() . '/images';
		$pattern   = "$image_dir/*.jpg";
		$files     = glob( $pattern );

		return $files;
	}

	function load_audio_library() {
		$mp3_dir = $this->get_media_library() . '/mp3s';
		$pattern = "$mp3_dir/*.mp3";
		$files   = glob( $pattern );

		return $files;
	}

	function use_media_library() {
		return $this->container->opts['fake_media'];
	}

	function get_media_library() {
		if ( defined( 'FAKE_MEDIA_LIBRARY' ) ) {
			return FAKE_MEDIA_LIBRARY;
		} else {
			\WP_CLI::error( 'Fatal Error: FAKE_MEDIA_LIBRARY is not defined' );
		}
	}

}
