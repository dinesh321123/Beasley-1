<?php

namespace GreaterMedia\Import;

class PhotoAlbumV2 extends BaseImporter {

	function get_tool_name() {
		return 'photo_album_v2';
	}

	function import_source( $source ) {
		$albums = $this->albums_from_source( $source );
		$total  = count( $albums );
		$notify = new \WordPress\Utils\ProgressBar( "Importing $total albums", $total );
		$entity = $this->get_entity( 'gallery' );

		foreach ( $albums as $album ) {
			$gallery = $this->gallery_from_album( $album );
			$entity->add( $gallery );
			$notify->tick();
		}

		$notify->finish();
	}

	function gallery_from_album( $album ) {
		$gallery_image_items = $this->gallery_images_from_album( $album );
		$gallery_images      = $gallery_image_items['images'];
		$gallery_tags        = array_unique( $gallery_image_items['tags'] );

		$gallery = array(
			'gallery_name'    => $this->import_string( $album['AlbumName'] ),
			'gallery_content' => $this->import_string( $album['Description'] ),
			'gallery_images'  => $gallery_images,
			'created_on'      => $this->import_string( $album['UTCDateCreated'] ),
			'modified_on'     => $this->import_string( $album['UTCDateModified'] ),
		);

		$meta         = $this->meta_from_album( $album );
		$gallery_show = $meta['gallery_show'];

		$gallery['categories']   = $meta['categories'];
		$gallery['tags']         = $gallery_tags;

		if ( empty( $gallery_show ) ) {
			$gallery_show = $this->show_from_album_name( $gallery['gallery_name'] );
		}

		if ( empty( $gallery_show  ) ) {
			$gallery_show = $this->show_from_tags( $gallery_tags );
		}

		$gallery['gallery_show'] = $gallery_show;

		return $gallery;
	}

	function meta_from_album( $album ) {
		$categories = $this->import_string( $album['Categories'] );

		if ( ! empty( $categories ) ) {
			$names    = explode( ',', $categories );
			$names    = array_map( 'trim', $names );
			$categories = $names;
		} else {
			$categories = array();
		}

		$gallery_show = $this->show_from_categories( $categories );

		return array(
			'categories' => $categories,
			'gallery_show' => $gallery_show
		);
	}

	function show_from_album_name( $name ) {
		$mappings = $this->container->mappings;
		$authors  = $mappings->get_matched_authors( $name );

		if ( ! empty( $authors ) ) {
			$show = $mappings->get_show_from_author_names( $authors );
		} else {
			$show = null;
		}

		return null;
	}

	function show_from_categories( &$categories ) {
		if ( ! empty( $categories ) ) {
			$show = $this->container->mappings->get_show_from_categories( $categories );
			return $show;
		} else {
			return null;
		}
	}

	function show_from_tags( &$tags ) {
		$show = $this->show_from_categories( $tags );
		if ( empty( $show ) ) {
			$show = $this->container->mappings->get_show_from_author_names( $tags );
		}
		return $show;
	}


	function gallery_images_from_album( $album ) {
		$images = array();
		$photos = $this->photos_from_album( $album );
		$count = count( $photos );
		$tags = array();

		foreach ( $photos as $photo ) {
			$attachment = $this->attachment_from_photo( $photo );
			$tags = array_merge( $tags, $attachment['photo_tags'] );
			$images[] = $attachment;
		}

		return array(
			'images' => $images,
			'tags' => $tags,
		);
	}

	function attachment_from_photo( $photo ) {
		$total_photo_files = count( $photo->PhotoFiles );
		$largest_photo     = $photo->PhotoFiles[ $total_photo_files - 1 ];

		$attachment = array(
			'file' => $this->import_string( $largest_photo['Filename'] ),
			'caption' => $this->import_string( $photo['PhotoCaption'] ),
			'attribution' => $this->import_string( $photo['Attribution'] ),
			'photo_tags' => $this->tags_from_photo( $photo )
		);

		return $attachment;
	}

	function tags_from_photo( $photo ) {
		$tags = $this->import_string( $photo['Tags'] );

		if ( ! empty( $tags ) ) {
			$tags = explode( ',', $tags );
			$tags = array_map( 'trim', $tags );

			return $tags;
		} else {
			return array();
		}
	}

	function photos_from_album( $album ) {
		return $album->Photo;
	}

	function albums_from_source( $source ) {
		return $source->Album;
	}

}
