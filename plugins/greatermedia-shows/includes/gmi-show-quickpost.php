<?php

// action hooks
add_action( 'gmr_quickpost_add_metaboxes', 'qmi_shows_add_quickpost_meta_box' );
// filter hooks
add_filter( 'gmr_quickpost_post_data', 'gmi_shows_filter_quickpost_data' );

/**
 * Adds the meta box container for shows info.
 *
 * @action gmr_quickpost_add_metaboxes
 * @param string $screen_id The quickpost screen id.
 */
function qmi_shows_add_quickpost_meta_box( $screen_id ) {
	add_meta_box( 'shows_meta_box', __( 'Shows', 'greatermedia' ), 'gmi_shows_render_quickpost_meta_box', $screen_id, 'side', 'high' );
}

/**
 * Renders shows meta box for quickpost popup.
 *
 * @param array $args The meta box arguments.
 */
function gmi_shows_render_quickpost_meta_box( $args ) {
	require_once ABSPATH . 'wp-admin/includes/meta-boxes.php';

	// set global post object
	$GLOBALS['post'] = get_post( $args['post_id'] );

	// activate default shows filter
	add_filter( 'wp_get_object_terms', 'gmi_shows_set_quickpost_defaults' );
	// render shows metabox
	post_categories_meta_box( get_post( $args['post_id'] ), array( 'args' => array( 'taxonomy' => ShowsCPT::SHOW_TAXONOMY ) ) );
	// deactivate default shows filter
	remove_filter( 'wp_get_object_terms', 'gmi_shows_set_quickpost_defaults' );
}

/**
 * Filters quickpost post data before saving into database. Casts show term ids to int.
 *
 * @filter gmr_quickpost_post_data
 * @param array $post The post data.
 * @return array The post data.
 */
function gmi_shows_filter_quickpost_data( $post ) {
	if ( ! empty( $post['tax_input'][ShowsCPT::SHOW_TAXONOMY] ) ) {
		$post['tax_input'][ShowsCPT::SHOW_TAXONOMY] = array_map( 'intval', $post['tax_input'][ShowsCPT::SHOW_TAXONOMY] );
	}

	return $post;
}

/**
 * Sets default shows for the quickpost form.
 *
 * @filter wp_get_object_terms
 * @param array $terms The initial array of shows.
 * @return array The extended array of shows.
 */
function gmi_shows_set_quickpost_defaults( $terms ) {
	$user_show_tt_id = get_user_option( 'show_tt_id' );
	if ( $user_show_tt_id ) {
		$term = get_term_by( 'term_taxonomy_id', $user_show_tt_id, ShowsCPT::SHOW_TAXONOMY );
		if ( $term ) {
			$terms[] = $term->term_id;
		}
	}
	
	return $terms;
}