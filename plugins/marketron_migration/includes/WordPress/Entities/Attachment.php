<?php

namespace WordPress\Entities;

class Attachment extends Post {

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

			$fields['post_mime_type'] = $file_meta['mime_type'];
			$fields['guid']           = $file_meta['url'];
			$fields['postmeta']       = array(
				'_wp_attached_file'       => $file_meta['upload_path'],
				'_wp_attachment_metadata' => serialize( $attachment_meta )
			);

			$fields['attachment_meta'] = $attachment_meta;
			$fields['file_meta']       = $file_meta;
			$filename                  = pathinfo( $file, PATHINFO_FILENAME );

			if ( ! array_key_exists( 'post_title', $fields ) ) {
				$fields['post_title'] = basename( $file );
			}

			if ( ! array_key_exists( 'post_name', $fields ) ) {
				$fields['post_name']  = basename( $file );
			}

			if ( ! array_key_exists( 'post_status', $fields ) ) {
				$fields['post_status'] = 'inherit';
			}

			$fields = parent::add( $fields );

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

}
