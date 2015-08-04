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
