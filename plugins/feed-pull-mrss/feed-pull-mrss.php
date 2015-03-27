<?php
/*
Plugin Name: Feed Pull MRSS
Description: Extends Feed Pull plugin by adding ability to extract media information from RSS feeds.
Version: 1.0.0
Author: 10up
Author URI: http://10up.com/
*/

/**
 * Catches SimpleXML element to use in the next steps.
 *
 * @global SimpleXMLElement $fpmrss_feed_item Current SimpleXML element.
 * @param array $pre_filter_post_value The array of post data.
 * @param SimpleXMLElement $xml_element The current xml element.
 * @return array The inital post data.
 */
function fpmrss_catch_feed_item_xml( $pre_filter_post_value, $xml_element ) {
	global $fpmrss_feed_item;
	$fpmrss_feed_item = $xml_element;

	return $pre_filter_post_value;
}
add_filter( 'fp_post_args', 'fpmrss_catch_feed_item_xml', 10, 2 );

/**
 * Extracts media group element.
 *
 * @param SimpleXMLElement $element Current RSS element.
 * @return SimpleXMLElement Media group element on success, otherwise FALSE.
 */
function fpmrss_get_media_group( SimpleXMLElement $element ) {
	$group = $element->xpath( 'media:group' );
	if ( ! empty( $group ) ) {
		$group = current( $group );
	} else {
		$group = $element->xpath( 'media:content' );
		if ( ! empty( $group ) ) {
			$group = current( $group );
		}
	}

	return $group;
}

/**
 * Extracts media thumbnail from an RSS element.
 *
 * @param SimpleXMLElement $element Current RSS element.
 * @return string Thumbnail URL on success, otherwise FALSE.
 */
function fpmrss_extract_media_thumbnail( SimpleXMLElement $element ) {
	$group = fpmrss_get_media_group( $element );
	if ( ! is_a( $group, 'SimpleXMLElement' ) ) {
		return false;
	}

	$thumbnail_url = false;
	$thumbnails = $group->xpath( 'media:thumbnail' );
	if ( ! empty( $thumbnails ) ) {
		$max_width = 0;
		foreach ( $thumbnails as $thumbnail ) {
			if ( empty( $thumbnail['url'] ) || ! filter_var( $thumbnail['url'], FILTER_VALIDATE_URL ) ) {
				continue;
			}

			$current_width = intval( $thumbnail['width'] );
			if ( ! empty( $current_width ) && $max_width >= $current_width ) {
				continue;
			}

			$thumbnail_url = strval( $thumbnail['url'] );
			$max_width = $current_width;
		}
	}

	return $thumbnail_url;
}

/**
 * Extracts media player from an RSS element.
 *
 * @param SimpleXMLElement $element Current RSS elemenet.
 * @return string Player value on success, otherwise FALSE.
 */
function fpmrss_extract_media_player( SimpleXMLElement $element ) {
	$group = fpmrss_get_media_group( $element );
	if ( ! is_a( $group, 'SimpleXMLElement' ) ) {
		return false;
	}

	$player_value = false;
	$player = $group->xpath( 'media:player' );
	if ( ! empty( $player ) ) {
		$player = current( $player );
		$player_value = trim( strval( $player ) );
		if ( empty( $player_value ) && ! empty( $player['url'] ) && filter_var( $player['url'], FILTER_VALIDATE_URL ) ) {
			$player_value = strval( $player['url'] );
		}
	}

	return $player_value;
}

/**
 * Fetches thumbnail image for a media item.
 *
 * @global SimpleXMLElement $fpmrss_feed_item The current SimpleXML element.
 * @global array $fpmrss_feed_thumbnails The array of feed thumbnails to import.
 * @param int $post_id Newly imported post id.
 */
function fpmrss_fetch_media_data( $post_id ) {
	global $fpmrss_feed_item, $fpmrss_feed_thumbnails;

	// do nothing if an xml element is not caught
	if ( ! $fpmrss_feed_item ) {
		return;
	}

	// init feed thumbnails array if it isn't
	if ( ! is_array( $fpmrss_feed_thumbnails ) ) {
		$fpmrss_feed_thumbnails = array();
	}

	// fetch thumbnail
	$thumbnail = fpmrss_extract_media_thumbnail( $fpmrss_feed_item );
	if ( $thumbnail ) {
		$fpmrss_feed_thumbnails[] = array( $thumbnail, $post_id );
	}

	// fetch player
	$player = fpmrss_extract_media_player( $fpmrss_feed_item );
	if ( $player ) {
		update_post_meta( $post_id, 'gmr-player', $player );
		set_post_format( $post_id, 'video' );
	}

	$fpmrss_feed_item = null;
}
add_action( 'fp_created_post', 'fpmrss_fetch_media_data' );
add_action( 'fp_updated_post', 'fpmrss_fetch_media_data' );

/**
 * Lauches async task to import thumbnails.
 *
 * @global array $fpmrss_feed_thumbnails The array of feed thumbnails to import.
 */
function fpmrss_launch_async_thumbnails_import() {
	global $fpmrss_feed_thumbnails;

	// do nothing if feed thumbnails array if empty
	if ( empty( $fpmrss_feed_thumbnails ) ) {
		return;
	}

	// try to launch async task if available, otherwise schedule single event
	if ( function_exists( 'wp_async_task_add' ) ) {
		wp_async_task_add( 'fpmrss_import_thumbnails', $fpmrss_feed_thumbnails );
	} else {
		wp_schedule_single_event( current_time( 'timestamp', 1 ), 'fpmrss_import_thumbnails', array( $fpmrss_feed_thumbnails ) );
	}
}
add_action( 'fp_post_feed_pull', 'fpmrss_launch_async_thumbnails_import' );

/**
 * Performs thumbnails import.
 *
 * @param array $thumbnails Array of arrays of thumbnail urls and post ids.
 */
function fpmrss_import_thumbnails( $thumbnails ) {
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';

	foreach ( $thumbnails as $thumbnail ) {
		$thumbnail_id = fpmrss_download_image( $thumbnail[0], $thumbnail[1] );
		if ( $thumbnail_id && ! is_wp_error( $thumbnail_id ) ) {
			set_post_thumbnail( $thumbnail[1], $thumbnail_id );
		}
	}
}
add_action( 'fpmrss_import_thumbnails', 'fpmrss_import_thumbnails' );

/**
 * Downloads remote image or just returns attachment ID if it has been already imported.
 *
 * @param string $image The remote image URL.
 * @param int $post_id The post id to which the image would be assigned.
 * @return int The attachment ID on success, otherwise FALSE.
 */
function fpmrss_download_image( $image, $post_id ) {
	$query = new WP_Query( array(
		'post_type'   => 'attachment',
		'post_status' => 'inherit',
		'meta_query'  => array(
			array(
				'key'     => 'fp_orig_image',
				'value'   => $image,
				'compare' => '='
			),
		),
	) );

	if ( $query->have_posts() ) {
		return $query->next_post()->ID;
	}

	// Download file to temp location
	$tmp = download_url( $image );
	if ( is_wp_error( $tmp ) ) {
		return false;
	}

	// Set variables for storage, fix file filename for query strings
	$matches = array();
	preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $image, $matches );
	$file_array['name'] = fpmrss_generate_image_name( $image );
	$file_array['tmp_name'] = $tmp;
	$file_array['error'] = 0;

	$post_data = array();
	$post = get_post( $post_id );
	if ( $post ) {
		$post_data['post_author'] = $post->post_author;
	}

	$image_id = media_handle_sideload( $file_array, $post_id, null, $post_data );
	if ( is_wp_error( $image_id ) ) {
		@unlink( $file_array['tmp_name'] );
	} else {
		add_post_meta( $image_id, 'fp_orig_image', $image );
	}

	return $image_id;
}

/**
 * Generates image name based on its file path.
 *
 * @param string $image The image path.
 * @return string The image name.
 */
function fpmrss_generate_image_name( $image ) {
	$file_name = '';
	$matches = array();

	preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $image, $matches );
	if ( is_array( $matches ) && count( $matches ) ) {
		$file_name = str_replace( '%20', '-', basename( $matches[0] ) );
	} else {
		$sizes = getimagesize( $image );
		if ( is_array( $sizes ) && isset( $sizes['mime'] ) && ! empty( $sizes['mime'] ) ) {
			$ext = image_type_to_extension( $sizes[2] );
			$file_name = substr( sanitize_title( pathinfo( $image, PATHINFO_FILENAME ) ), 0, 254 ) . $ext;
		}
	}
	
	return $file_name;
}

/**
 * Appends player content if it is available for the current post.
 *
 * @param string $content The initial content of the post.
 * @return string Updated post content if player code available, otherwise initial value.
 */
function fpmrss_update_content( $content ) {
	$player = get_post_meta( get_the_ID(), 'gmr-player', true );
	if ( ! empty( $player ) ) {
		$content = $player . PHP_EOL . PHP_EOL . $content;
	}
	
	return $content;
}
add_action( 'the_content', 'fpmrss_update_content', 1 );