<?php

namespace WordPress\Entities;

class Venue extends Post {

	function get_post_type() {
		return 'tribe_venue';
	}

	function add( &$fields ) {
		if ( ! empty( $fields['venue_name'] ) ) {
			$venue_name = strip_tags( $fields['venue_name'] );
		} else {
			$venue_name = strip_tags( $fields['post_title'] );
		}

		$venue = $this->get_venue_by_name( $venue_name );

		if ( is_null( $venue ) ) {
			return parent::add( $fields );
		} else {
			return $venue;
		}
	}

	function get_venue_by_name( $name ) {
		$name  = trim( $name );
		$table = $this->get_table( 'posts' );

		if ( $table->has_row_with_field( 'post_title', $name ) ) {
			return $table->get_row_with_field( 'post_title', $name );
		} else {
			return null;
		}
	}

}
