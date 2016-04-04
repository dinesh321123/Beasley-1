<?php

namespace GreaterMedia\HomepageCountdownClock;

use \WP_Query;

add_action( 'init',                  __NAMESPACE__ . '\register_homepage_countdown_clock_cpt' );
add_action( 'add_meta_boxes',        __NAMESPACE__ . '\remove_yoast_metabox', PHP_INT_MAX );
add_action( 'wp_print_scripts',      __NAMESPACE__ . '\remove_yoast_metabox_js', PHP_INT_MAX );

/**
 * Homepage slug
 *
 * @return string
 */
function gmr_countdownclocks_slug() {
	return GMR_COUNTDOWN_CLOCK_CPT;
}

/**
* Registers Homepage Countdown Clock post type
*
* @param string  Post type key, must not exceed 20 characters
* @param array|string  See optional args description above.
* @return object|WP_Error the registered post type object, or an error object
*/
function register_homepage_countdown_clock_cpt() {

	$labels = array(
		'name'                => __( 'Countdown Clocks', 'greatermedia' ),
		'singular_name'       => __( 'Countdown Clock', 'greatermedia' ),
		'add_new'             => _x( 'Add New Countdown Clock', 'greatermedia', 'greatermedia' ),
		'add_new_item'        => __( 'Add New Countdown Clock', 'greatermedia' ),
		'edit_item'           => __( 'Edit Countdown Clock', 'greatermedia' ),
		'new_item'            => __( 'New Countdown Clock', 'greatermedia' ),
		'view_item'           => __( 'View Countdown Clock', 'greatermedia' ),
		'search_items'        => __( 'Search Countdown Clocks', 'greatermedia' ),
		'not_found'           => __( 'No Countdown Clocks found', 'greatermedia' ),
		'not_found_in_trash'  => __( 'No Countdown Clocks found in Trash', 'greatermedia' ),
		'parent_item_colon'   => __( 'Parent Countdown Clock:', 'greatermedia' ),
		'menu_name'           => __( 'Countdown Clocks', 'greatermedia' ),
	);

	$args = array(
		'labels'              => $labels,
		'hierarchical'        => false,
		'description'         => 'description',
		'taxonomies'          => array(),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 46,
		'menu_icon'           => 'dashicons-clock',
		'show_in_nav_menus'   => true,
		'publicly_queryable'  => true,
		'exclude_from_search' => true,
		'has_archive'         => false,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => true,
		'capability_type'     => 'post',
		'show_in_rest'       => true,
        'rest_base'          => 'countdownclocks',
		'supports'            => array( 'title', 'thumbnail' ),
	);

	$args = apply_filters( 'gmr_homepage_countdown_clock_cpt_args', $args, gmr_countdownclocks_slug() );

	register_post_type( gmr_countdownclocks_slug(), $args );
}

/**
 * Remove the Yoast SEO metabox from homepage CPT's.
 */
function remove_yoast_metabox() {
	remove_meta_box( 'wpseo_meta', gmr_countdownclocks_slug(), 'normal' );
}

/**
 * Remove the Yoast SEO metabox js from homepage CPT's.
 */
function remove_yoast_metabox_js() {
	global $post;

	if ( ! is_a( $post, '\WP_Post' ) ) {
		return;
	}

	if ( gmr_countdownclocks_slug() === $post->post_type ) {
		wp_dequeue_script( 'wp-seo-metabox' );
	}
}
