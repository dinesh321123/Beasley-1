<?php

namespace WordPress\Entities;

class Gallery extends Post {

	function get_post_type() {
		return 'gmr_gallery';
	}

	// gallery_images
	//   caption, attribution, file
	// update post content
	function add( &$fields ) {
		$gallery_images = $fields['gallery_images'];
		$gallery_name   = $fields['gallery_name'];
		$gallery_content = $fields['gallery_content'];

		$fields['post_title']   = htmlentities( $fields['gallery_name'] );
		$fields['post_content'] = $fields['gallery_content'];

		if ( ! isset( $fields['post_format'] ) ) {
			$fields['post_format'] = 'gallery';
		}

		$fields = parent::add( $fields );
		$gallery_id = $fields['ID'];

		$this->set_gallery_images( $gallery_id, $gallery_images, $fields['post_content'] );

		if ( ! empty( $fields['gallery_show'] ) ) {
			$gallery_show = $fields['gallery_show'];
			$this->set_gallery_show( $gallery_id, $gallery_show );
		}

		return $fields;
	}

	function set_gallery_images( $gallery_id, $gallery_images, $content ) {
		$attachments = array();
		$entity = $this->get_entity( 'attachment' );

		foreach ( $gallery_images as $gallery_image ) {
			$attachment = array(
				'file' => $gallery_image['file'],
				'caption' => $gallery_image['caption'],
				'attribution' => $gallery_image['attribution'],
			);

			$attachment = $entity->add( $attachment );
			if ( ! empty( $attachment ) ) {
				$attachments[] = $attachment['ID'];
			}
		}

		if ( count( $attachments ) > 0 ) {
			$content = '[gallery ids="' . implode( ',', $attachments ) . '"]';
		}

		$posts = $this->get_table( 'posts' );
		$posts->update( $gallery_id, 'post_content', $content );
	}

	function set_gallery_show( $gallery_id, $show ) {
		$entity = $this->get_entity( 'show_taxonomy' );
		$entity->add( $show, $gallery_id );
	}

}
