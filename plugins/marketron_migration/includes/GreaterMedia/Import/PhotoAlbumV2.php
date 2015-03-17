<?php

namespace GreaterMedia\Import;

class PhotoAlbumV2 extends BaseImporter {

	function get_tool_name() {
		return 'photo_album_v2';
	}

	function import_source( $source ) {
		$albums = $this->albums_from_source( $source );
		$total  = count( $albums );
		$notify = new \cli\progress\Bar( "Importing $total albums", $total );
		$entity = $this->get_entity( 'gallery' );
		$max = 1;
		$i = 1;

		foreach ( $albums as $album ) {
			$gallery = $this->gallery_from_album( $album );
			$entity->add( $gallery );
			$notify->tick();

			//if ( $i++ > $max ) {
				//return;
			//}
		}

		$notify->finish();
	}

	function gallery_from_album( $album ) {
		$gallery = array(
			'gallery_name' => $this->import_string( $album['AlbumName'] ),
			'gallery_content' => $this->import_string( $album['Description'] ),
			'gallery_images' => $this->gallery_images_from_album( $album ),
		);

		$meta                    = $this->meta_from_album( $album );
		$gallery['categories']   = $meta['categories'];
		$gallery['gallery_show'] = $meta['gallery_show'];

		return $gallery;
	}

	function meta_from_album( $album ) {
		$categories = $album['Categories'];
		$gallery_show = null;

		if ( ! empty( $categories ) ) {
			$names = explode( ',', $categories );
			$names = array_map( 'trim', $names );
			$mappings = $this->container->mappings;

			foreach ( $names as $cat_name ) {
				if ( $mappings->has_author( $cat_name ) ) {
					$gallery_show = $mappings->get_show_for_author( $cat_name );
				}
			}
		} else {
			$names = array();
		}

		return array(
			'categories' => $names,
			'gallery_show' => $gallery_show
		);
	}

	function gallery_images_from_album( $album ) {
		$images = array();
		$photos = $this->photos_from_album( $album );
		$count = count( $photos );

		foreach ( $photos as $photo ) {
			$attachment = $this->attachment_from_photo( $photo );
			$images[] = $attachment;
		}

		return $images;
	}

	function attachment_from_photo( $photo ) {
		$total_photo_files = count( $photo->PhotoFiles );
		$largest_photo     = $photo->PhotoFiles[ $total_photo_files - 1 ];

		$attachment = array(
			'file' => $this->import_string( $largest_photo['Filename'] ),
			'caption' => $this->import_string( $photo['PhotoCaption'] ),
			'attribution' => $this->import_string( $photo['Attribution'] ),
		);

		return $attachment;
	}

	function photos_from_album( $album ) {
		return $album->Photo;
	}

	function albums_from_source( $source ) {
		return $source->Album;
	}

}
