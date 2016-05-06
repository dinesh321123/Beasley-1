<?php
/**
 * Allow posts to be excluded from home page searches.
 *
 * @package GreaterMedia\HomepageCuration
 */

namespace GreaterMedia\HomepageCuration\KeepOffHomepage;

const NONCE_NAME = '_keep-off-homepage-nonce';
const NONCE_STRING = 'keep-off-homepage';
const META_KEY = 'keep-off-homepage';

/**
 * Add keep off home page metabox.
 */
function add_meta_boxes() {
	/**
	 * Filter post types that are allowed to be excluded from the home page.
	 *
	 * @param array $post_types Allowed post types.
	 */
	$screens = apply_filters( 'gmr-homepage-exclude-post-types', [ 'post', 'episode' ] );
	add_meta_box( 'keep-off-homepage', 'Keep Off Homepage', __NAMESPACE__ . '\render_meta_box', $screens, 'side' );
}
add_action( 'add_meta_boxes', __NAMESPACE__ . '\add_meta_boxes' );

/**
 * Include and render keep off home page metabox.
 */
function render_meta_box() {
	\GreaterMedia\HomepageCuration\load_template( 'metabox-keep-off-homepage.php' );
}

function save_meta( $post_id ) {
	/**
	 * See includes/homepage-exclude.php:23
	 */
	$allowed_types = apply_filters( 'gmr-homepage-exclude-post-types', [ 'post', 'episode' ] );
	$post          = get_post( $post_id );
	if (
		! in_array( $post->post_type, $allowed_types ) ||
	  ! isset( $_POST[ NONCE_NAME ] ) || // PHPCS: input var ok.
	  ! wp_verify_nonce( $_POST[ NONCE_NAME ], NONCE_STRING ) || // PHPCS: input var ok | sanitization ok.
		wp_is_post_autosave( $post ) ||
	  wp_is_post_revision( $post )
	) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return false;
	}

	if ( isset( $_POST[ META_KEY ] ) ) { // PHPCS: input var ok.
		add_post_meta( $post_id, META_KEY, true );
	} else {
		delete_post_meta( $post_id, META_KEY );
	}
}
add_action( 'save_post', __NAMESPACE__ . '\save_meta' );

/**
 * Add meta query that excludes posts that have the keep off homepage meta key.
 *
 * @param \WP_Query $query Query object being modified.
 */
function ignore_posts( $query ) {

	if ( ! $query->is_home() || ! $query->is_main_query() ) {
		return;
	}

	$meta_query   = $query->get( 'meta_query' );
	$meta_query[] = [
		'key'     => META_KEY,
		'compare' => 'NOT EXISTS',
	];
	$query->set( 'meta_query', $meta_query );
}
add_action( 'pre_get_posts', __NAMESPACE__ . '\ignore_posts' );
