<?php

namespace GreaterMedia\Import;

class Venue extends BaseImporter {

	function get_tool_name() {
		return 'venue';
	}

	function import_source( $source ) {
		$tool           = $this->get_tool();
		$tool_name      = $tool->get_name();
		$venues         = $this->venues_from_source( $source );
		$total          = count( $venues );
		$notify         = new \WordPress\Utils\ProgressBar( "Importing $total items from $tool_name", $total );
		$venue_entities = $this->get_entity( 'venue' );

		foreach ( $venues as $venue ) {
			$venue_entity = $this->entity_from_venue( $venue );
			$venue_entities->add( $venue_entity );
			$notify->tick();
		}

		$notify->finish();
	}

	function venues_from_source( $source ) {
		return $source->Venue;
	}

	function entity_from_venue( $venue ) {
		$meta_fields = $this->get_meta_fields();
		$postmeta    = array();
		$post       = array(
			'post_title'   => $this->title_from_venue( $venue ),
			'post_content' => $this->content_from_venue( $venue ),
			'marketron_id' => $this->import_string( $venue['VenueID'] ),
		);

		foreach ( $meta_fields as $marketron_field => $new_field ) {
			$value = $venue[ $marketron_field ];
			if ( ! empty( $value ) ) {
				$value = $this->import_string( $value );
				$postmeta[ $new_field ] = $value;
			}
		}

		if ( empty( $postmeta['_VenueCountry'] ) ) {
			$postmeta['_VenueCountry'] = 'United States';
		}

		$post['postmeta']    = $postmeta;

		if ( ! empty( $venue['DateCreated'] ) ) {
			$post['created_on'] = $this->import_string( $venue['DateCreated'] );
		}

		if ( ! empty( $venue['DateModified'] ) ) {
			$post['modified_on'] = $this->import_string( $venue['DateModified'] );
		}

		return $post;
	}

	function title_from_venue( $venue ) {
		$title = $venue['VenueName'];
		$title = $this->import_string( $title );
		$title = strip_tags( $title );
		$title = htmlentities( $title );

		return $title;
	}

	function content_from_venue( $venue ) {
		$content = $venue['Directions'];
		$content = $this->import_string( $content );

		return $content;
	}

	function get_meta_fields() {
		return array(
			'PhoneNumber'        => '_VenuePhone',
			'WebsiteURL'         => '_VenueURL',
			'Address1'           => '_VenueAddress',
			'City'               => '_VenueCity',
			'State'              => '_VenueStateProvince',
			'ZipCode'            => '_VenueZip',
			'Country'            => '_VenueCountry',
			'VenueID'            => '_legacy_VenueID',
			'ParkingInformation' => '_legacy_ParkingInformation',
		);
	}

	function meta_from_venue( $name, $venue ) {
		if ( isset( $venue[ $name ] ) ) {
			return $this->import_string( $venue[ $name ] );
		} else {
			return null;
		}
	}

}
