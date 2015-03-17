<?php

namespace GreaterMedia\Import;

class Showcase extends BaseImporter {

	function get_tool_name() {
		return 'showcase';
	}

	function import_source( $source ) {
		$showcases = $this->showcases_from_source( $source );
		$total     = count( $showcases );
		$notify = new \cli\progress\Bar( "Importing $total Showcases", $total );
		$entity = $this->get_entity( 'album' );

		foreach ( $showcases as $showcase ) {
			$album = $this->album_from_showcase( $showcase );
			$entity->add( $album );
			$notify->tick();
		}

		$notify->finish();
	}

	function showcases_from_source( $source ) {
		return $source->Showcase;
	}

	function album_from_showcase( $showcase ) {
		$album = array(
			'album_name' => $this->import_string( $showcase['ShowcaseName'] ),
			'album_galleries' => $this->galleries_from_album( $showcase ),
			'album_content' => $this->import_string( $showcase['ShowcaseDescription'] )
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
