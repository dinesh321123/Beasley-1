<?php

namespace GreaterMedia\Import;

class EventManager extends BaseImporter {

	function get_tool_name() {
		return 'event_manager';
	}

	function import_source( $source ) {
		$calendars    = $this->calendars_from_source( $source );
		$total        = count( $calendars );
		$msg          = "Importing $total Calendars";
		$progress_bar = new \WordPress\Utils\ProgressBar( $msg, $total );
		$mappings     = $this->container->mappings;

		foreach ( $calendars as $calendar ) {
			$calendar_name = $this->import_string( $calendar['CalendarName'] );

			if ( $mappings->can_import_marketron_name( $calendar_name, 'event_manager' ) ) {
				$this->import_calendar( $calendar );
			}

			$progress_bar->tick();
		}

		$progress_bar->finish();
	}

	function import_calendar( $calendar ) {
		$calendar_id   = $this->import_string( $calendar['CalendarID'] );
		$calendar_name = $this->import_string( $calendar['CalendarName'] );
		//\WP_CLI::log( "Importing Calendar: $calendar_name" );

		$events       = $this->events_from_calendar( $calendar );
		//$total        = count( $events );
		//$msg          = "Importing $total Events from Calendar";
		//$progress_bar = new \WordPress\Utils\ProgressBar( $msg, $total );
		$entity       = $this->get_entity( 'event' );
		$categories   = $this->categories_from_calendar( $calendar );

		foreach ( $events as $calendar_event ) {
			$event = $this->entity_from_event( $calendar_event );
			$event['event_categories'] = $categories;

			$entity->add( $event );
			//$progress_bar->tick();
		}

		//$progress_bar->finish();
	}

	function calendars_from_source( $source ) {
		return $source->Calendar;
	}

	function events_from_calendar( $calendar ) {
		return $calendar->Events->Event;
	}

	function categories_from_calendar( $calendar ) {
		$category = $this->import_string( $calendar['CalendarName'] );
		return array( $category );
	}

	function entity_from_event( $calendar_event ) {
		$event                  = array();
		$event['event_venue']   = $this->venue_from_event( $calendar_event );
		$event['event_title']   = $this->title_from_event( $calendar_event );
		$event['event_content'] = $this->content_from_event( $calendar_event );
		$event['created_on']    = $this->import_string( $calendar_event['DateCreated'] );
		$event['modified_on']   = $this->import_string( $calendar_event['DateModified'] );

		$featured_image = $this->featured_image_from_event( $calendar_event );
		if ( ! empty( $featured_image ) ) {
			$event['featured_image'] = $featured_image;
		}

		$event['event_meta'] = $this->meta_from_event( $calendar_event );

		return $event;
	}

	function meta_from_event( $event ) {
		$meta                           = array();
		$meta['_EventShowMap']          = 1;
		$meta['_EventShowMapLink']      = 1;
		$meta['_EventCurrencySymbol']   = '$';
		$meta['_EventCurrencyPosition'] = 'prefix';
		$meta['_EventURL']              = $this->import_string( $event['TicketPurchaseURL'] );
		$meta['_EventCost']             = $this->price_from_event( $event );
		$meta['_EventOrigin']           = 'marketron';
		$meta['_EventOrganizerID']      = 0;
		$meta['marketron_id']           = $this->import_string( $event['ConcertID'] );

		$timespan = $this->timespan_from_event( $event );

		$meta['_EventStartDate'] = $timespan['start']->format( 'Y-m-d H:i:s' );
		$meta['_EventEndDate']   = $timespan['end']->format( 'Y-m-d H:i:s' );

		return $meta;
	}

	function price_from_event( $event ) {
		return $this->import_string( $event['TicketPrice'] );
	}

	function featured_image_from_event( $calendar_event ) {
		$image_url = $this->import_string( $calendar_event['ConcertImageURL'] );

		if ( ! empty( $image_url ) ) {
			$image_url_path = parse_url( $image_url, PHP_URL_PATH );
		} else {
			$image_url_path = null;
		}

		return $image_url_path;
	}

	function timespan_from_event( $event ) {
		$span  = array();
		$start = $this->import_string( $event['DateToRelease'] );

		if ( empty( $start ) ) {
			$concert_date = $this->import_string( $event['ConcertDate'] );
			$concert_date = $this->to_datetime( $concert_date );

			$start = $concert_date;
			$end   = $concert_date;
		} else {
			$start = $this->to_datetime( $start );

			$end = $this->import_string( $event['DateToExpire'] );
			$end = $this->to_datetime( $end );
		}

		return array(
			'start' => $start,
			'end' => $end,
		);
	}

	function content_from_event( $event ) {
		$content = $event['ConcertBlurb'];
		$content = $this->import_string( $content );
		$content = htmlentities( $content );

		return $content;
	}

	function title_from_event( $event ) {
		$title = $event['ConcertName'];
		$title = $this->import_string( $title );
		$title = htmlentities( $title );

		return $title;
	}

	function venue_from_event( $event ) {
		$venue    = $event->Venue;
		$importer = $this->container->importer_factory->build( 'venue' );

		return $importer->entity_from_venue( $venue );
	}

}
