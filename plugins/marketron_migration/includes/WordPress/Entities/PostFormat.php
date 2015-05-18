<?php

namespace WordPress\Entities;

class PostFormat extends Taxonomy {

	function get_taxonomy() {
		return 'post_format';
	}

	function add( $post_format, $post_id = null, $exclude_from_csv = false ) {
		$term_name = $this->get_post_format( $post_format );
		return parent::add( $term_name, $post_id );
	}

	function get_post_format( $format ) {
		$format = sanitize_key( $format );

		if ( 'standard' === $format || ! in_array( $format, get_post_format_slugs() ) ) {
			$format = '';
		} else {
			$format = 'post-format-' . $format;
		}

		return $format;
	}

}
