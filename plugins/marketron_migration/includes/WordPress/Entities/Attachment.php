<?php

namespace WordPress\Entities;

class Attachment extends Post {

	public $attachments_log;

	function get_post_type() {
		return 'attachment';
	}

	function add( &$fields ) {
		if ( array_key_exists( 'created_on', $fields ) ) {
			$timestamp = $this->to_datetime( $fields['created_on'] );
		} else {
			$timestamp = new \DateTime();
		}

		$file      = $fields['file'];
		$file_meta = $this->sideload( $file, $timestamp );

		if ( $file_meta !== false ) {
			$attachment_meta = $this->get_attachment_meta( $file_meta );
			$attachment_meta = $attachment_meta;
			$dest_file = $file_meta['file'];

			$fields['post_mime_type'] = $file_meta['mime_type'];
			$fields['guid']           = $file_meta['url'];
			$fields['postmeta']       = array(
				'_wp_attached_file'       => $file_meta['upload_path'],
				'_wp_attachment_metadata' => serialize( $attachment_meta )
			);

			$fields['attachment_meta'] = $attachment_meta;
			$fields['file_meta']       = $file_meta;
			$filename                  = pathinfo( $dest_file, PATHINFO_FILENAME );

			if ( ! array_key_exists( 'post_title', $fields ) ) {
				$fields['post_title'] = $filename;
			}

			if ( ! array_key_exists( 'post_name', $fields ) ) {
				$fields['post_name']  = basename( $dest_file );
			}

			if ( ! array_key_exists( 'post_status', $fields ) ) {
				$fields['post_status'] = 'inherit';
			}

			$fields = parent::add( $fields );
			$attachment_id = $fields['ID'];

			if ( strpos( $file_meta['mime_type'], 'image/' ) === 0 ) {
				$this->log_attachment( $attachment_id );
			}

			return $fields;
		} else {
			// don't insert attachment if sideloading failed
			// TODO: error logging
			return false;
		}
	}

	function get_attachment_meta( $file_meta ) {
		$meta         = array();
		$mime_type    = $file_meta['mime_type'];
		$meta['file'] = $file_meta['upload_path'];

		if ( strpos( $mime_type, 'image/' ) === 0 ) {
			$meta['width']      = $file_meta['width'];
			$meta['height']     = $file_meta['height'];
			$meta['sizes']      = array();
			$meta['image_meta'] = array();
		} else if ( strpos( $mime_type, 'audio/' ) === 0 ) {

		} else if ( strpos( $mime_type, 'video/' ) === 0 ) {

		} else {

		}

		return $meta;
	}

	function get_attachments_log() {
		if ( is_null( $this->attachments_log ) ) {
			$path = $this->container->config->get_attachments_log_file();
			$this->attachments_log = fopen( $path, 'w' );
		}

		return $this->attachments_log;
	}

	function log_attachment( $attachment_id ) {
		$log  = $this->get_attachments_log();
		$line = $attachment_id . "\n";

		fwrite( $log, $line );
	}

}
