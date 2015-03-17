<?php

namespace WordPress\Entities;

class Album extends Post {

	function get_post_type() {
		return 'gmr_album';
	}

	/*
	 * album_gallery
	 * album_name
	 *
	 */
	function add( &$fields ) {
		$album_name    = $fields['album_name'];
		$album_galleries = $fields['album_galleries'];
		$album_content = $fields['album_content'];

		$fields['post_title'] = $album_name;
		$fields['post_content'] = $album_content;

		$fields   = parent::add( $fields );
		$album_id = $fields['ID'];

		$this->set_album_galleries( $album_id, $album_galleries );

		return $fields;
	}

	function set_album_galleries( $album_id, $album_galleries ) {
		$galleries = array();
		$entity = $this->get_entity( 'gallery' );

		foreach ( $album_galleries as $album_gallery ) {
			$album_gallery['post_parent'] = $album_id;
			$album_gallery    = $entity->add( $album_gallery );

			$album_gallery_id = $album_gallery['ID'];
			$galleries[]      = $album_gallery_id;
		}

		if ( ! empty( $galleries ) ) {
			$table       = $this->get_table( 'posts' );
			$meta_fields = array( '_gmedia_related_galleries' => serialize( $galleries ) );
			$table->add_post_meta( $album_id, $meta_fields );
		}
	}

}
