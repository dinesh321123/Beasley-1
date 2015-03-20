<?php

namespace WordPress\Entities;

class Event extends Post {

	function get_post_type() {
		return 'tribe_events';
	}

	function add( &$fields ) {
		if ( ! empty( $fields['event_title'] ) ) {
			$fields['post_title'] = $fields['event_title'];
		}

		$event_title = $fields['post_title'];
		$existing_event = $this->get_event_by_title( $event_title );

		if ( ! is_null( $existing_event ) ) {
			//error_log( 'found existing event: ' . $event_title );
			return $existing_event;
		}

		if ( ! empty( $fields['event_content'] ) ) {
			$fields['post_content'] = $fields['event_content'];
		}

		if ( ! empty( $fields['event_meta'] ) ) {
			$fields['postmeta'] = $fields['event_meta'];
		}

		$fields   = parent::add( $fields );
		$event_id = $fields['ID'];

		if ( array_key_exists( 'event_categories', $fields ) ) {
			$this->set_event_categories( $event_id, $fields['event_categories'] );
		}

		if ( array_key_exists( 'event_venue', $fields ) ) {
			$this->set_event_venue( $event_id, $fields['event_venue'] );
		}

		return $fields;
	}

	function get_event_by_title( $event_title ) {
		$post_name = sanitize_title( $event_title );
		$table     = $this->get_table( 'posts' );

		return $table->get_row_with_field( 'post_name', $post_name );
	}

	function set_event_categories( $event_id, $categories ) {
		$entity = $this->get_entity( 'event_category' );

		foreach ( $categories as $category ) {
			$entity->add( $category, $event_id );
		}
	}

	function set_event_venue( $event_id, $venue ) {
		$entity = $this->get_entity( 'venue' );

		if ( is_string( $venue ) ) {
			$venue_name = $venue;
		} else if ( ! empty( $venue['venue_name'] ) ) {
			$venue_name = $venue['venue_name'];
		} else {
			$venue_name = $venue['post_title'];
		}

		$venue_entity = $entity->get_venue_by_name( $venue_name );

		if ( is_null( $venue_entity ) ) {
			if ( is_string( $venue ) ) {
				$venue_entity = array( 'post_title' => $venue_name );
			} else {
				$venue_entity = $venue;
			}
		}

		$venue_entity = $entity->add( $venue_entity );
		$venue_id = $venue_entity['ID'];
		$meta_fields = array(
			'_EventVenueID' => $venue_id,
		);

		$posts = $this->get_table( 'posts' );
		$posts->add_post_meta( $event_id, $meta_fields );
	}

}
