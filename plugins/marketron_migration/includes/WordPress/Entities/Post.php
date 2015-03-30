<?php

namespace WordPress\Entities;

class Post extends BaseEntity {

	function add( &$fields ) {
		if ( array_key_exists( 'post_authors', $fields ) ) {
			$post_authors = $this->add_post_authors( $fields['post_authors'] );
			if ( count( $post_authors ) > 0 ) {
				$fields['post_author'] = $post_authors[0];
			}
		}

		if ( ! empty( $fields['post_author'] ) && $fields['post_author'] === 'INACTIVE' ) {
			$fields['post_author'] = 0;
		}

		if ( empty( $fields['post_name'] ) && ! empty( $fields['post_title'] ) ) {
			$fields['post_name'] = sanitize_title( $fields['post_title'] );
		}

		$table  = $this->get_table( 'posts' );
		$this->load_defaults( $fields );

		if ( array_key_exists( 'created_on', $fields ) ) {
			$time_variants           = $this->to_time_variants( $fields['created_on'] );
			$fields['post_date']     = $time_variants['date'];
			$fields['post_date_gmt'] = $time_variants['date_gmt'];

			if ( empty( $fields['modified_on'] ) ) {
				$fields['modified_on'] = $fields['created_on'];
			}
		}

		if ( array_key_exists( 'modified_on', $fields ) ) {
			$time_variants               = $this->to_time_variants( $fields['modified_on'] );
			$fields['post_modified']     = $time_variants['date'];
			$fields['post_modified_gmt'] = $time_variants['date_gmt'];
		}

		$fields['post_content'] = $this->import_images_in_content(
			$fields['post_content']
		);

		$fields = $table->add( $fields );
		$post_id = $fields['ID'];

		if ( array_key_exists( 'post_format', $fields ) ) {
			$this->set_post_format( $post_id, $fields['post_format'] );
		}

		if ( array_key_exists( 'tags', $fields ) ) {
			$this->set_post_tags( $post_id, $fields['tags'] );
		}

		if ( array_key_exists( 'categories', $fields ) ) {
			$this->set_post_categories( $post_id, $fields['categories'] );
		}

		if ( array_key_exists( 'collections', $fields ) ) {
			$this->set_post_collections( $post_id, $fields['collections'] );
		}

		if ( array_key_exists( 'featured_image', $fields ) && ! empty( $fields['featured_image'] ) ) {
			$this->set_featured_image( $post_id, $fields );
		}

		if ( array_key_exists( 'featured_audio', $fields ) && ! empty( $fields['featured_audio'] ) ) {
			$this->set_featured_audio( $post_id, $fields );
		}

		if ( array_key_exists( 'redirects', $fields ) ) {
			$this->set_redirects( $post_id, $fields );
		}

		return $fields;
	}

	function set_redirects( $post_id, &$fields ) {
		$redirects = $fields['redirects'];
		$legacy_redirects = $this->get_entity( 'legacy_redirect' );

		foreach ( $redirects as $redirect ) {
			$redirect['post_id'] = $post_id;
			$redirect = $legacy_redirects->add( $redirect );
		}
	}

	function set_featured_image( $post_id, &$fields ) {
		$attachment = $fields['featured_image'];
		$post_author = ! empty( $fields['post_author'] ) ? $fields['post_author'] : 0;
		$entity = $this->get_entity( 'attachment' );

		if ( is_string( $attachment ) ) {
			$attachment = array( 'file' => $attachment );
		}

		$attachment['post_parent'] = $post_id;
		$attachment['post_author'] = $post_author;
		$attachment['created_on']  = $fields['created_on'];
		$attachment                = $entity->add( $attachment );

		if ( $attachment === false ) {
			return;
		}

		$attachment_id = $attachment['ID'];

		if ( $this->is_tiny_image( $attachment ) ) {
			return;
		}

		$posts       = $this->get_table( 'posts' );
		$meta_fields = array( '_thumbnail_id' => $attachment_id );

		$posts->add_post_meta( $post_id, $meta_fields );
	}

	function set_featured_audio( $post_id, &$fields ) {
		$attachment  = $fields['featured_audio'];
		$post_author = $fields['post_author'];
		$entity      = $this->get_entity( 'attachment' );

		if ( is_string( $attachment ) ) {
			$attachment = array( 'file' => $attachment );
		}

		$attachment['post_parent'] = $post_id;
		$attachment['post_author'] = $post_author;
		$attachment['created_on']  = $fields['created_on'];
		$attachment                = $entity->add( $attachment );

		if ( isset( $attachment['file_meta'] ) ) {
			$file_meta = $attachment['file_meta'];
			$mp3       = $file_meta['url'];

			$content  = $fields['post_content'];
			$content .= "<br/>[audio mp3='$mp3'][/audio]";

			$posts = $this->get_table( 'posts' );
			$posts->update( $post_id, 'post_content', $content );
		}
	}

	function is_tiny_image( $attachment ) {
		if ( isset( $attachment['attachment_meta'] ) ) {
			$meta = $attachment['attachment_meta'];

			if ( array_key_exists( 'width', $meta ) ) {
				return $meta['width'] <= 300; // TODO: make constant
			} else {
				// if width is missing assume to be a tiny image
				return true;
			}
		} else {
			// if no meta found, don't use as featured image
			return true;
		}
	}

	function import_images_in_content( $content ) {
		$inline_image_replacer = $this->container->inline_image_replacer;
		return $inline_image_replacer->find_and_replace( $content );
	}

	function set_post_format( $post_id, $post_format ) {
		$entity = $this->get_entity( 'post_format' );
		$entity->add( $post_format, $post_id );
	}

	function set_post_tags( $post_id, $tags ) {
		$entity = $this->get_entity( 'tag' );

		foreach ( $tags as $tag ) {
			$entity->add( $tag, $post_id );
		}
	}

	function set_post_categories( $post_id, $categories ) {
		$entity = $this->get_entity( 'category' );

		foreach ( $categories as $category ) {
			$entity->add( $category, $post_id );
		}
	}

	function set_post_collections( $post_id, $collections ) {
		$entity = $this->get_entity( 'collection_taxonomy' );

		foreach ( $collections as $collection ) {
			$entity->add( $collection, $post_id );
		}
	}

	function add_post_authors( $post_authors ) {
		$author_ids = array();
		$authors    = $this->get_entity( 'author' );

		foreach ( $post_authors as $author_name ) {
			$author = array( 'display_name' => $author_name );
			$author = $authors->add( $author );
			$author_ids[] = $author['ID'];
		}

		return $author_ids;
	}

	function load_defaults( &$fields ) {
		foreach ( $this->get_defaults() as $field_name => $field_value ) {
			if ( ! array_key_exists( $field_name, $fields ) ) {
				//error_log( 'loading default field: ' . $field_name . ' - ' . $field_value );
				$fields[ $field_name ] = $field_value;
			}
		}
	}

	function get_post_type() {
		return 'post';
	}

	function get_defaults() {
		$now = gmdate( 'Y-m-d H:i:s' );

		return array(
			'post_type' => $this->get_post_type(),
			'post_mime_type' => null,

			'post_author' => 0,
			'post_parent' => 0,
			'menu_order'  => 0,

			'post_title' => '',
			'post_name' => '',

			'post_content' => '',
			'post_content_filtered' => null,
			'post_excerpt' => '',
			'post_status' => 'publish',

			'post_date'         => $now,
			'post_date_gmt'     => $now,
			'post_modified'     => $now,
			'post_modified_gmt' => $now,

			'comment_count' => 0,
			'comment_status' => 'open',

			'to_ping' => null,
			'pinged' => null,
			'ping_status' => 'open',
		);
	}

}
