<?php

namespace WordPress\Entities;

class Venue extends Post {

	function get_post_type() {
		return 'tribe_venue';
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
