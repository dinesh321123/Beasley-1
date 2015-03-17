<?php

namespace WordPress\Entities;

class Event extends Post {

	function get_post_type() {
		return 'tribe_events';
	}

	function add( &$fields ) {
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

	function set_event_categories( $event_id, $categories ) {
		$entity = $this->get_entity( 'event_category' );

		foreach ( $categories as $category ) {
			$entity->add( $category, $event_id );
		}
	}

	function set_event_venue( $event_id, $venue ) {
		$entity       = $this->get_entity( 'venue' );
		$venue_entity = $entity->get_venue_by_name( $venue );

		if ( is_null( $venue_entity ) ) {
			$venue_entity = array(
				'post_title' => $venue,
			);

			$venue_entity = $entity->add( $venue_entity );
		}

		$venue_id = $venue_entity['ID'];
		$meta_fields = array(
			'_EventVenueID' => $venue_id,
		);

		$posts = $this->get_table( 'posts' );
		$posts->add_post_meta( $event_id, $meta_fields );
	}

}
