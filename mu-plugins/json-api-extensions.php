<?php
/**
 * Extends the API a bit to add some easier to use helpers for Greater Media
 */

/**
 * Adds easy support for post thumbnails to JSON API
 */
function greatermedia_json_api_post_thumbnails($post, $data, $update) {
	if ( ! empty( $data['x-post-thumbnail'] ) ) {
		$post = (array) $post;
		$thumb_id = intval( $data['x-post-thumbnail'] );

		$image = get_post( $thumb_id );
		if ( 'attachment' !== $image->post_type ) {
			return;
		}

		set_post_thumbnail( $post['ID'], $image->ID );

	}
}
// 1.x
add_filter('json_insert_post', 'greatermedia_json_api_post_thumbnails', 20, 3 );
// 2.x
add_filter('rest_insert_post', 'greatermedia_json_api_post_thumbnails', 20, 3 );

function greatermedia_json_api_shows( $post, $data, $update ) {
	if ( ! empty( $data['x-shows'] ) ) {
		$post = (array) $post;
		$shows = (array) $data['x-shows'];
		$shows = array_map( 'sanitize_text_field', $shows );

		wp_set_object_terms( $post['ID'], $shows, '_shows' );
	}
}
// 1.x
add_filter('json_insert_post', 'greatermedia_json_api_shows', 20, 3 );
// 2.x
add_filter('rest_insert_post', 'greatermedia_json_api_shows', 20, 3 );

function greatermedia_json_api_breaking( $post, $data, $update ) {
	if ( ! empty( $data['x-breaking'] ) ) {
		$post = (array) $post;
		$breaking = filter_var( $data['x-breaking'], FILTER_VALIDATE_BOOLEAN );

		update_post_meta( $post['ID'], '_is_breaking_news', $breaking );
	}
}
// 1.x
add_filter('json_insert_post', 'greatermedia_json_api_breaking', 20, 3 );
// 2.x
add_filter('rest_insert_post', 'greatermedia_json_api_breaking', 20, 3 );

function greatermedia_json_api_attribution( $post, $data, $update ) {
	if ( ! empty( $data['x-attribution'] ) ) {
		$post = (array) $post;
		$attribution = sanitize_text_field( $data['x-attribution'] );

		update_post_meta( $post['ID'], 'gmr_image_attribution', $attribution );
	}
}
// 1.x
add_filter('json_insert_post', 'greatermedia_json_api_attribution', 20, 3 );
// 2.x
add_filter('rest_insert_post', 'greatermedia_json_api_attribution', 20, 3 );

function greatermedia_json_post_format( $post, $data, $update ) {
	if ( ! empty( $data['x-post-format'] ) ) {
		$post = (array) $post;
		$format = sanitize_text_field( $data['x-post-format'] );

		set_post_format( $post['ID'], $format );
	}
}
// 1.x only - Using post_format field in 2.x
add_filter('json_insert_post', 'greatermedia_json_post_format', 20, 3 );

function greatermedia_json_wpseo_redirect( $post, $data, $update ) {
	if ( ! empty( $data['x-redirect'] ) ) {
		$post = (array) $post;
		$redirect_url = trim( esc_url_raw( $data['x-redirect'] ) );

		if ( ! empty( $redirect_url ) ) {
			update_post_meta( $post['ID'], '_yoast_wpseo_redirect', $redirect_url );
		}
	}
}
// 1.x
add_filter( 'json_insert_post', 'greatermedia_json_wpseo_redirect', 20, 3 );
// 2.x
add_filter( 'rest_insert_post', 'greatermedia_json_wpseo_redirect', 20, 3 );

function greatermedia_json_categories_tags($post, $data, $update) {
	// Since 1.x IS array, and 2.x is object
	$post = (array) $post;
	$append = false;

	if ( ! empty( $data['x-tags'] ) ) {
		if ( is_array( $data['x-tags'] ) ) {
			wp_set_post_tags( $post['ID'], $data['x-tags'], $append );
		}
	}

	if ( ! empty( $data['x-categories'] ) ) {
		if ( is_array( $data['x-categories'] ) ) {
			wp_set_object_terms( $post['ID'], $data['x-categories'], 'category', $append );
		}
	}
}
// 1.x
add_filter('json_insert_post', 'greatermedia_json_categories_tags', 20, 3 );
// 2.x
add_filter('rest_insert_post', 'greatermedia_json_categories_tags', 20, 3 );

add_action( 'rest_api_init', 'greatermedia_json_register_breaking' );
function greatermedia_json_register_breaking() {
	register_rest_field( 'post',
		'_is_breaking_news',
		array(
			'get_callback'    => 'greatermedia_json_get_breaking',
		)
	);
}

function greatermedia_json_get_breaking( $object, $field_name, $request ) {
	return get_post_meta( $object[ 'id' ], $field_name, true );
}

add_action( 'rest_api_init', 'greatermedia_json_register_homepage_features' );
function greatermedia_json_register_homepage_features() {
	register_rest_field( 'gmr_homepage',
		'featured_meta_box',
		array(
			'get_callback'    => 'greatermedia_json_get_featured',
		)
	);
}

function greatermedia_json_get_featured( $object, $field_name, $request ) {
	return get_post_meta( $object[ 'id' ], $field_name, true );
}

function greatermedia_json_filter_meta_query( $valid_vars ) {
	
	$valid_vars = array_merge( $valid_vars, array( 'meta_key', 'meta_value' ) );
	return $valid_vars;
}
add_filter( 'rest_query_vars', 'greatermedia_json_filter_meta_query' );