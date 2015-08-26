<?php
/**
 * Extends the API a bit to add some easier to use helpers for Greater Media
 */

/**
 * Adds easy support for post thumbnails to JSON API
 */
function greatermedia_json_api_post_thumbnails($post, $data, $update) {
	if ( ! empty( $data['x-post-thumbnail'] ) ) {
		$thumb_id = intval( $data['x-post-thumbnail'] );

		$image = get_post( $thumb_id );
		if ( 'attachment' !== $image->post_type ) {
			return;
		}

		set_post_thumbnail( $post['ID'], $image->ID );

	}
}
add_filter('json_insert_post', 'greatermedia_json_api_post_thumbnails', 20, 3 );

function greatermedia_json_api_shows( $post, $data, $update ) {
	if ( ! empty( $data['x-shows'] ) ) {
		$shows = (array) $data['x-shows'];
		$shows = array_map( 'sanitize_text_field', $shows );

		wp_set_object_terms( $post['ID'], $shows, '_shows' );
	}
}
add_filter('json_insert_post', 'greatermedia_json_api_shows', 20, 3 );

function greatermedia_json_api_breaking( $post, $data, $update ) {
	if ( ! empty( $data['x-breaking'] ) ) {
		$breaking = filter_var( $data['x-breaking'], FILTER_VALIDATE_BOOLEAN );

		update_post_meta( $post['ID'], '_is_breaking_news', $breaking );
	}
}
add_filter('json_insert_post', 'greatermedia_json_api_breaking', 20, 3 );

function greatermedia_json_api_attribution( $post, $data, $update ) {
	if ( ! empty( $data['x-attribution'] ) ) {
		$attribution = sanitize_text_field( $data['x-attribution'] );

		update_post_meta( $post['ID'], 'gmr_image_attribution', $attribution );
	}
}
add_filter('json_insert_post', 'greatermedia_json_api_attribution', 20, 3 );

function greatermedia_json_post_format( $post, $data, $update ) {
	if ( ! empty( $data['x-post-format'] ) ) {
		$format = sanitize_text_field( $data['x-post-format'] );

		set_post_format( $post['ID'], $format );
	}
}
add_filter('json_insert_post', 'greatermedia_json_post_format', 20, 3 );
