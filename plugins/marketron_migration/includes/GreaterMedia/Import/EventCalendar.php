<?php

namespace GreaterMedia\Import;

class EventCalendar extends BaseImporter {

	function get_tool_name() {
		return 'event_calendar';
	}

	function import_source( $source ) {
		$tool       = $this->get_tool();
		$tool_name  = $tool->get_name();
		$calendars  = $this->calendars_from_source( $source );

		foreach ( $calendars as $calendar ) {
			$this->import_calendar( $calendar );
		}
	}

	function import_calendar( $calendar ) {
		$entities   = $this->get_entity( 'event' );
		$events     = $this->events_from_calendar( $calendar );
		$total      = count( $events );
		$notify     = new \cli\progress\Bar( "Importing $total items from Calendar", $total );
		$max_items  = $this->get_site_option( 'limit' );
		$item_index = 1;
		$category   = $this->category_from_calendar( $calendar );

		foreach ( $events as $event ) {
			$entity = $this->entity_from_event( $event );
			$entity['event_categories'] = array( $category );
			$entities->add( $entity );
			$notify->tick();

			if ( $item_index++ > $max_items ) {
				break;
			}
		}

		$notify->finish();
	}

	function calendars_from_source( $source ) {
		return $source->EventCalendar;
	}

	function events_from_calendar( $calendar ) {
		return $calendar->Event;
	}

	function category_from_calendar( $calendar ) {
		return $this->import_string( $calendar['EventCalendarName'] );
	}

	function entity_from_event( $event ) {
		$post     = array();
		$postmeta = array();

		$post['post_title']   = $this->title_from_event( $event );
		$post['post_content'] = $this->content_from_event( $event );

		$featured_image = $this->featured_image_from_event( $event );
		if ( ! is_null( $featured_image ) ) {
			$post['featured_image'] = $featured_image;
		}

		$venue = $this->venue_from_event( $event );
		if ( ! is_null( $venue ) ) {
			$post['event_venue'] = $venue;
		}

		$timespan                    = $this->timespan_from_event( $event );
		$postmeta['_EventStartDate'] = $timespan['start'];
		$postmeta['_EventEndDate']   = $timespan['end'];

		$post['postmeta'] = $postmeta;

		return $post;
	}

	function content_from_event( $event ) {
		$content = $event['EventDescription'];
		$content = $this->import_string( $content );

		return $content;
	}

	function title_from_event( $event ) {
		$title = $event['EventName'];
		$title = $this->import_string( $title );
		$title = htmlentities( $title );

		return $title;
	}

	function featured_image_from_event( $event ) {
		if ( ! empty( $event['EventImageFilePath'] ) ) {
			$featured_image = $event['EventImageFilePath'];
			$featured_image = $this->import_string( $featured_image );
		} else {
			$featured_image = null;
		}

		return $featured_image;
	}

	function timespan_from_event( $event ) {
		$span  = array();
		$start = $this->import_string( $event['EventDate'] );
		$start = $this->to_datetime( $start );

		$end = $this->import_string( $event['EventEndDate'] );
		$end = $this->to_datetime( $start );

		return array(
			'start' => $start,
			'end' => $end,
		);
	}

	function venue_from_event( $event ) {
		if ( ! empty( $event['EventLocation'] ) ) {
			$venue = $event['EventLocation'];
			$venue = $this->import_string( $venue );
		} else {
			$venue = null;
		}

		return $venue;
	}


}
