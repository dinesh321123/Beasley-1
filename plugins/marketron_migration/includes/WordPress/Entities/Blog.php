<?php

namespace WordPress\Entities;

class Blog extends Post {

	function get_post_type() {
		return 'post';
	}

	function add( &$fields ) {
		if ( ! empty( $fields['shows'] ) ) {
			$shows = $fields['shows'];
		} else {
			$shows = null;
		}

		$fields = parent::add( $fields );
		$post_id = $fields['ID'];

		if ( ! empty( $shows ) ) {
			$shows = array_unique( $shows );
			foreach ( $shows as $show ) {
				$entity = $this->get_entity( 'show_taxonomy' );
				if ( is_array( $show ) ) {
					\WP_CLI::error( 'Fatal Error in Mapping for Show: ' . print_r( $show, true ) );
				}
				if ( trim( $show ) !== '' ) {
					$entity->add( $show, $post_id );
				}
			}
		}

		if ( ! empty( $fields['post_format'] ) ) {
			$post_formats = $this->get_entity( 'post_format' );
			$post_formats->add( $fields['post_format'], $post_id );
		}

		return $fields;
	}

}
