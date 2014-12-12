<?php

// action hooks
add_action( 'gmr_live_link_copy_post', 'gmrs_copy_personalities_to_live_link', 10, 2 );

// filter hooks
add_filter( 'gmr_live_link_taxonomies', 'gmrs_add_live_links_taxonomy_support' );
add_filter( 'gmr_live_link_add_copy_action', 'gmrs_check_live_links_copy_action', 10, 2 );

/**
 * Adds support of shows taxonomy to live links post type.
 *
 * @filter gmr_live_link_taxonomies
 * @param array $taxonomies The array of already supported taxonomies.
 * @return array The extended array of supported taxonomies.
 */
function gmrs_add_live_links_taxonomy_support( $taxonomies ) {
	$taxonomies[] = ShowsCPT::SHOW_TAXONOMY;
	return $taxonomies;
}

/**
 * Copies show terms when a post is copied to live links.
 *
 * @action gmr_live_link_copy_post
 * @param int $ll_id The live link post id.
 * @param int $post_id The copied post id.
 */
function gmrs_copy_personalities_to_live_link( $ll_id, $post_id ) {
	$terms = wp_get_post_terms( $post_id, ShowsCPT::SHOW_TAXONOMY );
	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		$terms = array_filter( (array) wp_list_pluck( $terms, 'term_id' ) );
		wp_set_post_terms( $ll_id, $terms, ShowsCPT::SHOW_TAXONOMY );
	}
}

/**
 * Checks whether or not to add copy live link action.
 *
 * @filter gmr_live_link_add_copy_action
 * @param boolean $add_copy_link Initial value.
 * @param WP_Post $post The post object.
 * @return boolean TRUE if we need to add a copy link, otherwise FALSE.
 */
function gmrs_check_live_links_copy_action( $add_copy_link, WP_Post $post ) {
	return ! $add_copy_link ? $add_copy_link : ShowsCPT::SHOW_CPT != $post->post_type;
}