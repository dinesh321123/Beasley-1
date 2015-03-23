<?php

namespace WordPress\Utils;

class MediaSideLoader {

	public $container;
	public $pending_sideloads = array();

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
		$source_dir = $this->get_sync_source_dir();
		$target_dir = $this->get_sync_target_dir();
		$total      = count( $this->pending_sideloads );
		$notify     = new \WordPress\Utils\ProgressBar( "Copying $total Media items", $total );

		foreach ( $this->pending_sideloads as $pending_sideload ) {
			$source   = $pending_sideload['source'];
			$dest     = $pending_sideload['dest'];
			$dest_dir = dirname( $dest );

			if ( ! file_exists( $dest_dir ) ) {
				mkdir( $dest_dir, 0700, true );
				//system( 'mkdir -p ' . escapeshellarg( $dest_dir ) );
			}

			//error_log( "copy: $source - $dest" );
			copy( $source, $dest );

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

		if ( ! is_dir( $target_dir ) ) {
			//system( 'mkdir -p ' . escapeshellarg( $target_dir ) );
		}

		// slow copy
		//error_log( "copy: $filepath $target_filepath" );
		//copy( $filepath, $target_filepath );

		// copy with system
		//$filepath_arg        = escapeshellarg( $filepath );
		//$target_filepath_arg = escapeshellarg( $target_filepath );

		//system( "cp $filepath_arg $target_filepath_arg" );

		// we symlink the media file into place, and use rsync to
		// resolve symlinks
		//$cwd         = getcwd();
		//$cd_dir      = dirname( $target_filepath );
		//$link_target = realpath( $filepath );
		//$link_name   = basename( $target_filepath );

		//$link_target_arg = escapeshellarg( $link_target );
		//$link_name_arg   = escapeshellarg( $link_name );

		/*
		try {
			chdir( $cd_dir );

			//if ( is_link( $link_name ) ) {
				//unlink( $link_name );
			//}

			if ( ! is_link( $link_name ) ) {
				symlink( $link_target, $link_name );
			}

		} catch ( \Exception $e ) {
			error_log( 'Symlink Error: ' . $e->getMessage() );
		} finally {
			chdir( $cwd );
		}
		 */

		$wordpress_upload_dir = $this->get_wordpress_upload_path_for( $filename, $timestamp );

		$this->pending_sideloads[] = array(
			'source' => $filepath,
			'dest' => $wordpress_upload_dir,
		);

		//return $this->get_file_meta( $new_filename, $target_filepath, $timestamp );
		// we are getting meta info from the source file itself
		// the file will be copied into place at the end
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

}
