<?php

namespace GreaterMedia\Import;

class Showcase extends BaseImporter {

	function get_tool_name() {
		return 'showcase';
	}

	function import_source( $source ) {
		$showcases = $this->showcases_from_source( $source );
		$total     = count( $showcases );
		$notify = new \WordPress\Utils\ProgressBar( "Importing $total Showcases", $total );
		$entity = $this->get_entity( 'album' );
		$add_count = 0;
		$skip_count = 0;

		foreach ( $showcases as $showcase ) {
			$album = $this->album_from_showcase( $showcase );

			if ( ! $this->can_import_by_time( $album ) ) {
				$skip_count++;
				continue;
			}

			$entity->add( $album );
			$add_count++;
			$notify->tick();
		}

		\WP_CLI::log( "Added $add_count Showcases" );
		\WP_CLI::log( "Skipped $skip_count Showcases" );

		$notify->finish();
	}

	function showcases_from_source( $source ) {
		return $source->Showcase;
	}

	function album_from_showcase( $showcase ) {
		$album = array(
			'album_name' => $this->import_string( $showcase['ShowcaseName'] ),
			'album_galleries' => $this->galleries_from_album( $showcase ),
			'album_content' => $this->import_string( $showcase['ShowcaseDescription'] ),
			'created_on' => $this->import_string( $showcase['DateCreated'] ),
			'modified_on' => $this->import_string( $showcase['DateModified'] ),
		);

		return $album;
	}

	function galleries_from_album( $showcase ) {
		$entries = $showcase->Entry;
		$total   = count( $entries );
		$galleries = array();

		foreach ( $entries as $entry ) {
			$gallery = $this->gallery_from_entry( $entry );
			$galleries[] = $gallery;
		}

		return $galleries;
	}

	function gallery_from_entry( $entry ) {
		$photos = $entry->ShowcasePhoto;
		$images = array();
		$gallery = array(
			'gallery_name' => $this->import_string( $entry['ShowcaseEntryName'] ),
			'gallery_content' => $this->import_string( $entry['LongText01'] ),
			'marketron_id' => $this->import_string( $entry['ShowcaseEntryID'] ),
			'redirects' => array(
				array( 'url' => $this->import_string( $entry['ShowcaseEntryURL'] ) )
			)
		);

		foreach ( $photos as $photo ) {
			$image = array(
				'file' => $this->import_string( $photo['PhotoURL'] ),
				'caption' => $this->import_string( $photo['MediumText01'] ),
				'attribution' => $this->import_string( $photo['SubmitterName'] ),
			);

			$images[] = $image;
		}

		$gallery['gallery_images'] = $images;

		return $gallery;
	}

}
