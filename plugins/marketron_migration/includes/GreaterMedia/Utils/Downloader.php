<?php

namespace GreaterMedia\Utils;

class Downloader {

	public $cache_dir;
	public $cache_errors = true;
	public $media_files_dir;
	public $errors = array();

	function __construct( $cache_dir, $media_files_dir ) {
		$this->cache_dir       = $cache_dir;
		$this->errors_dir      = $cache_dir . '/errors';
		$this->media_files_dir = $media_files_dir;
	}

	function has_cached_error( $url ) {
		$key  = $this->cache_key_for( $url );
		$path = $this->errors_dir . '/' . $key;

		return file_exists( $path );
	}

	function cache_key_for( $url ) {
		return md5( $url );
	}

	function cache_file_path_for( $url ) {
		$key  = $this->cache_key_for( $url );
		$path = $this->cache_dir . '/' . $key;

		return $path;
	}

	function is_cached( $url ) {
		return file_exists( $this->cache_file_path_for( $url ) );
	}

	function cache( $url ) {
		$tmp_file = download_url( $url );

		if ( ! is_wp_error( $tmp_file ) ) {
			$cache_file_path = $this->cache_file_path_for( $url );
			rename( $tmp_file, $cache_file_path );
		} else {
			$key             = $this->cache_key_for( $url );
			$error_file_path = $this->errors_dir . '/' . $key;

			touch( $error_file_path );
		}
	}

	function is_cached_media_file( $url, $url_path ) {
		$media_file_path = $this->media_files_dir . $url_path;
		$has_cached_file = file_exists( $media_file_path );

		return $has_cached_file;
	}

	function cache_media_file( $url, $url_path ) {
		$media_file_path = $this->media_files_dir . $url_path;
		$cache_file_path = $this->cache_file_path_for( $url );

		//\WP_CLI::success( 'Found cached media file: ' . $url_path );
		copy( $media_file_path, $cache_file_path );
	}

	function download( $url ) {
		$url      = trim( $url );
		$parts    = parse_url( $url );
		$url_path = urldecode( $parts['path'] );

		/* if url is 404 and cached return early */
		if ( $this->has_cached_error( $url ) ) {
			$this->errors[] = $url;
			return false;
		}

		if ( $this->is_cached_media_file( $url, $url_path ) ) {
			/* if media file, we copy the media file to our cache */
			if ( ! $this->is_cached( $url ) ) {
				$this->cache_media_file( $url, $url_path );
			}
		} else if ( ! $this->is_cached( $url ) ) {
			$this->cache( $url );
		}

		if ( $this->is_cached( $url ) ) {
			$cache_file_path = $this->cache_file_path_for( $url );
			$tmp_file_path   = $cache_file_path . '_tmp';

			copy( $cache_file_path, $tmp_file_path );

			return $tmp_file_path;
		} else {
			$this->errors[] = $url;
			return false;
		}
	}

}
