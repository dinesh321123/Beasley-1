<?php

namespace WordPress\Utils;

class MediaSideLoader {

	public $container;
	public $pending_sideloads = array();
	public $directory_map     = array();
	public $conflict_count    = 0;

	function register() {
		add_action( 'init', array( $this, 'do_register' ) );
	}

	function do_register() {
		add_action(
			'sideload_file_async_job',
			array( $this, 'do_sideload_file' )
		);
	}

	function do_sideload_file( $params ) {
		$source = $params[0];
		$dest   = $params[1];
		$opts   = $params[2];

		$this->do_copy( $source, $dest, $opts );
	}

	// copy the site's uploads directory to backups/uploads
	function backup() {
		$source_dir = $this->get_sync_target_dir();
		$target_dir = $this->get_backup_dir();

		$this->rsync( $source_dir, $target_dir );
	}

	// copy the backup contents to the site's uploads directory
	function restore() {
		$source_dir = $this->get_backup_dir();
		$target_dir = $this->get_sync_target_dir();

		$this->rsync( $source_dir, $target_dir );
	}

	// copy sideloaded uploads to site's uploads directory
	function sync() {
		\WP_CLI::log( "Total Conflicts: $this->conflict_count" );
		$this->remove_duplicates();

		$source_dir = $this->get_sync_source_dir();
		$target_dir = $this->get_sync_target_dir();
		$total      = count( $this->pending_sideloads );
		$notify     = new \WordPress\Utils\ProgressBar( "Copying $total Media items", $total );

		foreach ( $this->pending_sideloads as $index => $pending_sideload ) {
			$source   = $pending_sideload['source'];
			$dest     = $pending_sideload['dest'];
			$opts     = array( 'index' => $index + 1, 'total' => $total );

			$this->copy( $source, $dest, $opts );

			$notify->tick();
		}

		$notify->finish();
		//$this->rsync( $source_dir, $target_dir );
	}

	// copy the media file to output/uploads and return it's attributes
	function sideload( $filepath, $timestamp = null ) {
		if ( ! file_exists( $filepath ) ) {
			return false;
		}

		if ( is_null( $timestamp ) ) {
			$timestamp = strtotime( 'now' );
		}

		$filename        = basename( $filepath );
		$new_filename    = sanitize_file_name( $filename );
		$target_dir      = $this->get_upload_dir_for( $timestamp );
		$target_filepath = $target_dir . '/' . $new_filename;

		$wordpress_upload_dir = $this->get_wordpress_upload_path_for( $filename, $timestamp );

		if ( $this->directory_has_file( dirname( $wordpress_upload_dir ), $new_filename ) ) {
			$wordpress_upload_dir = $this->directory_next_file( dirname( $wordpress_upload_dir ), $new_filename );
			$new_filename = basename( $wordpress_upload_dir );
		}

		if ( ! $this->directory_has_file( dirname( $wordpress_upload_dir ), $new_filename ) ) {
			$this->directory_add_file( dirname( $wordpress_upload_dir ), $new_filename );
		} else {
			//\WP_CLI::log( "Conflict in path: $filepath - $wordpress_upload_dir" );
			$this->conflict_count++;
		}

		$this->pending_sideloads[] = array(
			'source' => $filepath,
			'dest' => $wordpress_upload_dir,
		);

		return $this->get_file_meta( $new_filename, $filepath, $timestamp );
	}

	function rsync( $source_dir, $target_dir ) {
		\WP_CLI::log( "Syncing $source_dir to $target_dir ..." );
		$source_dir = trailingslashit( $source_dir );

		$cmd  = 'rsync -arh --delete --delete-delay --copy-links ';
		//$cmd .= ' --verbose';
		//$cmd .= ' --progress';
		$cmd .= escapeshellarg( $source_dir );
		$cmd .= ' ';
		$cmd .= escapeshellarg( $target_dir );

		//error_log( $cmd );

		if ( ! is_dir( $target_dir ) ) {
			system( 'mkdir -p ' . escapeshellarg( $target_dir ) );
		}

		system( $cmd );
	}

	function get_file_meta( $filename, $filepath, $timestamp ) {
		$result     = wp_check_filetype( $filepath );
		$extension  = $result['ext'];
		$mime_type  = $result['type'];

		if ( strpos( $mime_type, 'image/' ) === 0 ) {
			$meta = $this->get_image_meta( $filepath );
		} else if ( strpos( $mime_type, 'audio/' ) === 0 ) {
			$meta = $this->get_audio_meta( $filepath );
		} else if ( strpos( $mime_type, 'video/' ) === 0 ) {
			$meta = $this->get_video_meta( $filepath );
		} else {
			$meta = array();
		}

		$meta['file']        = $filepath;
		$meta['upload_path'] = $this->get_upload_path_for( $filename, $timestamp );
		$meta['mime_type']   = $mime_type;
		$meta['url']         = $this->get_upload_url_for( $filename, $timestamp );

		return $meta;
	}

	function get_image_meta( $filepath ) {
		// getimagesize is slow, do this computation as part of
		// thumbnail regeneration
		return array(
			'width' => 640,
			'height' => 480,
		);

		$result = getimagesize( $filepath );
		$meta = array();

		$meta['width'] = $result[0];
		$meta['height'] = $result[1];

		return $meta;
	}

	function get_audio_meta( $filepath ) {
		return array();
		return wp_read_audio_metadata( $filepath );
	}

	function get_video_meta( $filepath ) {
		return array();
		return wp_read_video_metadata( $filepath );
	}

	function get_site_dir() {
		return $this->container->config->get_site_dir();
	}

	function get_sync_source_dir() {
		return $this->get_site_dir() . '/output/uploads';
	}

	function get_sync_target_dir() {
		$upload_dir = wp_upload_dir();
		return $upload_dir['basedir'];
	}

	function get_upload_dir_for( $timestamp ) {
		$suffix     = $this->get_timestamp_suffix( $timestamp );
		$upload_dir = $this->get_sync_source_dir();

		return $upload_dir . '/' . $suffix;
	}

	function get_upload_path_for( $filename, $timestamp ) {
		$suffix     = $this->get_timestamp_suffix( $timestamp );
		$upload_dir = wp_upload_dir();

		return $suffix . '/' . $filename;
	}

	function get_upload_url_for( $filename, $timestamp ) {
		$suffix     = $this->get_timestamp_suffix( $timestamp );
		$upload_dir = wp_upload_dir();

		return $upload_dir['baseurl'] . '/' . $suffix . '/' . $filename;
	}

	function get_wordpress_upload_path_for( $filename, $timestamp ) {
		$suffix     = $this->get_timestamp_suffix( $timestamp );
		$upload_dir = wp_upload_dir();

		return $upload_dir['basedir'] . '/' . $suffix . '/' . sanitize_file_name( $filename );
	}

	function get_timestamp_suffix( $timestamp ) {
		if ( $timestamp instanceof \DateTime ) {
			$date = $timestamp;
		} else if ( is_int( $timestamp ) ) {
			$date = new \DateTime();
			$date->setTimestamp( $timestamp );
		} else {
			$date = new \DateTime( $timestamp );
		}

		return date_format( $date, 'Y/m' );
	}

	function get_backup_dir() {
		return $this->get_site_dir() . '/backups/uploads';
	}

	function symlink( $source, $dest ) {
		$cwd       = getcwd();
		$dest_dir  = dirname( $dest );
		$link_name = basename( $dest );

		try {
			chdir( $dest_dir );
		} catch ( \Exception $e ) {

		} finally {
			chdir( $cwd );
		}
	}

	function update_ownership( $path ) {
		/* TODO: configurable via WP_CLI opts */
		chown( $path, 'nginx' );
		chgrp( $path, 'nginx' );
	}

	function remove_duplicates() {
		$map = array();

		foreach ( $this->pending_sideloads as $index => $pending_sideload ) {
			$source = $pending_sideload['source'];
			$dest   = $pending_sideload['dest'];
			$hash   = md5( $source . $dest );

			if ( ! array_key_exists( $hash, $map ) ) {
				$map[ $hash ] = true;
			} else {
				$this->pending_sideloads[ $index ] = null;
			}
		}

		$this->pending_sideloads = array_filter(
			$this->pending_sideloads,
			array( $this, 'is_pending_sideload' )
		);

		$this->pending_sideloads = array_values( $this->pending_sideloads );
	}

	function is_pending_sideload( $item ) {
		return ! empty( $item );
	}

	function copy( $source, $dest, $opts  ) {
		if ( $this->container->opts['async'] === true ) {
			$this->enqueue_copy( $source, $dest, $opts );
		} else {
			$this->do_copy( $source, $dest, $opts );
		}
	}

	function do_copy( $source, $dest, $opts ) {
		$dest_dir = dirname( $dest );

		if ( ! file_exists( $dest_dir ) ) {
			mkdir( $dest_dir, 0700, true );
			$this->update_ownership( $dest_dir );
			//system( 'mkdir -p ' . escapeshellarg( $dest_dir ) );
		}

		copy( $source, $dest );
		$this->update_ownership( $dest );

		$name    = basename( $dest );
		$percent = round( $opts['index'] / $opts['total'] * 100, 2 );

		error_log( $opts['index'] . ' / ' . $opts['total'] );
		error_log( "Copied: $name - $percent%" );
	}

	function enqueue_copy( $source, $dest, $opts ) {
		wp_async_task_add(
			'sideload_file_async_job',
			array( $source, $dest, $opts ),
			'normal'
		);
	}

	/* directory conflict detection */
	function directory_has_file( $dir, $filename ) {
		return array_key_exists( $dir, $this->directory_map ) &&
			array_key_exists( $filename, $this->directory_map[ $dir ] );
	}

	function directory_add_file( $dir, $filename ) {
		if ( ! array_key_exists( $dir, $this->directory_map ) ) {
			$this->directory_map[ $dir ] = array();
		}

		$this->directory_map[ $dir ][ $filename ] = true;
	}

	function directory_next_file( $dir, $filename ) {
		if ( ! array_key_exists( $dir, $this->directory_map ) ) {
			$this->directory_map[ $dir ] = array();
		}

		$next_id   = count( $this->directory_map[ $dir ] ) + 1;
		$next_path = $dir . '/' . $next_id . '-' . $filename;

		return $next_path;
	}

}
