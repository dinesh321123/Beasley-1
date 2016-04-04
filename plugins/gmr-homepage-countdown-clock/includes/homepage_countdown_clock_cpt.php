<?php

namespace GreaterMedia\HomepageCountdownClock;

use \WP_Query;

add_action( 'init',                  __NAMESPACE__ . '\register_homepage_countdown_clock_cpt' );
add_action( 'save_post',             __NAMESPACE__ . '\save_meta_data' );
add_action( 'post_submitbox_start',  __NAMESPACE__ . '\create_homepages_nonce' );
add_action( 'add_meta_boxes',        __NAMESPACE__ . '\remove_yoast_metabox', PHP_INT_MAX );
add_action( 'wp_print_scripts',      __NAMESPACE__ . '\remove_yoast_metabox_js', PHP_INT_MAX );

add_filter( 'preview_post_link',     __NAMESPACE__ . '\preview_post_setup', PHP_INT_MAX, 2 );

/**
 * Homepage save nonce
 *
 * @return string
 */
function get_homepage_save_nonce() {
	return '_save_hompage_nonce';
}

/**
 * Preview meta key suffix
 *
 * @return string
 */
function preview_meta_key_suffix() {
	return '_preview';
}

/**
 * Homepage slug
 *
 * @return string
 */
function gmr_countdownclocks_slug() {
	return GMR_COUNTDOWN_CLOCK_CPT;
}

/**
 * Array with metabox data with name => slug
 *
 * @return array
 */
function metabox_data() {
	return array(
		'Featured'    => 'featured_meta_box',
		'Don\'t miss' => 'dont_miss_meta_box',
		'Events'      => 'events_meta_box'
	);
}

/**
 * Get supported post types which will be quiried
 *
 * @return array
 */
function get_supported_post_types() {
	return (array) apply_filters( 'gmr-homepage-curation-post-types', array( 'post', 'page', 'tribe_events' )  );
}

/**
 * A wrapper for get_post_meta that returns 'preview-aware' post meta.
 *
 * @param $homepage_id       Homepage post ID
 * @param $key               The post meta key to get
 * @param bool|false $single Whether to return a single meta value or not
 * @return mixed             Will be an array if $single is false. Will be value of meta data field if $single is true.
 */
function get_preview_aware_post_meta( $homepage_id, $key, $single = false ) {
	// If we're requesting a preview, return the preview meta data.
	if ( is_preview() ) {
		$key .= preview_meta_key_suffix();
	}

	return get_post_meta( absint( $homepage_id ), sanitize_text_field( $key ), (bool) $single );
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
		//'register_meta_box_cb' => __NAMESPACE__ . '\register_meta_boxes',
	);

	$args = apply_filters( 'gmr_homepage_countdown_clock_cpt_args', $args, gmr_countdownclocks_slug() );

	register_post_type( gmr_countdownclocks_slug(), $args );
}

/**
 * Registers meta boxes for the spotlight post type.
 *
 * @param \WP_Post $homepage Homepage WP_Post object
 */
function register_meta_boxes( $homepage ) {
	$metaboxes = metabox_data();

	foreach ( $metaboxes as $name => $slug ) {
		add_meta_box(
			$slug,
			$name,
			__NAMESPACE__ . '\render_source_meta_box',
			$homepage->post_type,
			'normal',
			'high',
			array( 'slug' => $slug )
		);
	}
}

function render_source_meta_box( $homepage, $metabox ) {
	$post_ids = get_preview_aware_post_meta( $homepage->ID, $metabox['args']['slug'], true );

	// Can hook into these to change the limit for each curated area
	$homepage_curation_featured_limit = apply_filters( 'gmr-homepage-featured-limit', 4 );
	$homepage_curation_community_limit = apply_filters( 'gmr-homepage-community-limit', 3 );
	$homepage_curation_events_limit = apply_filters( 'gmr-homepage-events-limit', 2 );

	$post_picker_args = array (
		'show_numbers'            => true,
		'show_icons'              => true,
		'show_recent_select_list' => true,
		'args'                    => array (
			'post_type'   => get_supported_post_types(),
			'post_status' => array( 'publish', 'future' )
		),
	);

	if ( 'events_meta_box' !== $metabox['args']['slug'] ) {
		// Query restricted posts.
		$query            = new \WP_Query();
		$restricted_posts = $query->query( array (
			'post_type'           => get_supported_post_types(),
			'post_status'         => 'any',
			'posts_per_page'      => 50,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'fields'              => 'ids',
			'meta_query'          => array (
				'relation' => 'OR',
				array (
					'key'     => 'post_age_restriction',
					'compare' => 'EXISTS',
				),
				array (
					'key'     => 'post_login_restriction',
					'compare' => 'EXISTS',
				),
			),
		) );

		if ( 'featured_meta_box' === $metabox['args']['slug'] ) {
			$post_picker_args['limit'] = $homepage_curation_featured_limit;
		} else {
			$post_picker_args['limit'] = $homepage_curation_community_limit;
		}

		$post_picker_args['args']['exclude'] = $restricted_posts;
	} else {
		// Fetch future events post ids
		$query = new \WP_Query();
		$future_events = $query->query( array(
			'post_type'           => 'tribe_events',
			'post_status'         => array( 'publish', 'future', 'private' ),
			'posts_per_page'      => 2,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'fields'              => 'ids',
			'suppress_filters'    => true, // have to suppress filters otherwise it won't work
			'meta_key'            => '_EventStartDate',
			'meta_type'           => 'DATETIME',
			'orderby'             => 'meta_value',
			'order'               => 'ASC',
			'meta_query'          => array(
				array(
					'key'     => '_EventStartDate',
					'value'   => current_time( 'mysql' ),
					'type'    => 'DATETIME',
					'compare' => '>',
				),
			),
		) );

		$post_picker_args['limit'] = $homepage_curation_events_limit;
		$post_picker_args['args']['post_type'] = 'tribe_events';
		$post_picker_args['args']['include']   = $future_events;
	}

	render_post_picker( $metabox['args']['slug'], $post_ids, $post_picker_args );
}

/**
 * Render a post picker field.
 *
 * @param string $name Name of input
 * @param string $value Expecting comma separated post ids
 * @param array $options Field options
 */
function render_post_picker( $name, $value, $options = array() ) {
	if ( class_exists( 'NS_Post_Finder' ) ) {
		\NS_Post_Finder::render( $name, $value, $options );
	} else {
		?><p>The Post Finder plugin was not found.</p><?php
	}
}

/**
 * Saves meta box data.
 *
 * @param int $post_id The post id.
 * @return boolean TRUE if meta data have been saved, otherwise FALSE.
 */
function save_meta_data( $post_id ) {
	// do nothing if nonce is invalid
	$nonce = filter_input( INPUT_POST, get_homepage_save_nonce() );
	if ( ! $nonce || ! wp_verify_nonce( $nonce, gmr_countdownclocks_slug() . $post_id ) ) {
		return false;
	}

	// do nothing if it is autosave request
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return false;
	}

	// do nothing if current user can't edit homepage posts
	$post_type = get_post_type_object( gmr_countdownclocks_slug() );
	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
		return false;
	}

	// Use a different meta key if this is a preview and the post was already published
	// No need to check for the 'original_post_status' because we always want to save
	// the preview meta data when 'wp-preview' = 'dopreview'.
	// See WP#20299
	$meta_key_suffix = '';
	if ( isset( $_POST['wp-preview'] ) && 'dopreview' === $_POST['wp-preview'] ) {
		$meta_key_suffix = preview_meta_key_suffix();
	}

	// Save posts
	$metaboxes = metabox_data();
	foreach ( $metaboxes as $name => $slug ) {
		$value = filter_input( INPUT_POST, $slug );
		save_meta_data_field( $post_id, $slug, $meta_key_suffix, $value );
	}

	return true;
}

/**
 * Create post field nonce field
 */
function create_homepages_nonce() {
	global $post;
	if ( isset( $post->ID ) ) {
		wp_nonce_field( gmr_countdownclocks_slug() . $post->ID, get_homepage_save_nonce(), false );
	}
}


function save_meta_data_field( $post_id, $meta_key, $meta_key_suffix, $meta_value ) {
	// Always delete the preview meta data so it doesn't hang around forever.
	delete_post_meta( absint( $post_id ), sanitize_text_field( $meta_key . preview_meta_key_suffix() ) );

	// If we aren't saving a preview, remove the 'live' meta data.
	if ( empty( $meta_key_suffix ) ) {
		delete_post_meta( absint( $post_id ), sanitize_text_field( $meta_key ) );
	}

	// Save the preview or live meta data, depending on the situation.
	if ( ! empty( $meta_value ) ) {
		add_post_meta(
			absint( $post_id ),
			sanitize_text_field( $meta_key ) . sanitize_text_field( $meta_key_suffix ),
			sanitize_text_field( $meta_value )
		);
	}
}

/**
 * Customize homepage preview URL.
 *
 * @param  string $url The post preview URL
 * @param  WP_Post $post The post being previewd
 * @return string The post preview URL
 */
function preview_post_setup( $url, $post ) {
	global $typenow;

	if ( ! is_a( $post, '\WP_Post' ) ) {
		return;
	}

	/**
	 * Save the preview post meta data if we're on the post edit screen.
	 * This is needed because the 'save_post' action isn't called when clicking the preview button.
	 * This is the only reliable place I could find to do this, even though it would be better to find an action.
	 * I tried using the '_wp_put_post_revision' action but it didn't fire every time the previe button was clicked.
	 */
	if ( gmr_countdownclocks_slug() === $typenow ) {
		save_meta_data( $post->ID );
	} else {
		return $url;
	}

	// Build a new preview URL. We can't just include the front page template here
	// because the body classes added affect the layout, i.e., WP thinks you're on a single post page.
	$url_parts = parse_url( $url );
	$query_args = array();

	// Get the current URL query args.
	parse_str( $url_parts['query'], $query_args );

	// Unset query vars that cause the single page template to be shown instead of the the front page template.
	unset( $query_args[$post->post_type] );
	unset( $query_args['post_type'] );
	unset( $query_args['p'] );

	// Make sure the preview post ID is included.
	if ( ! isset( $query_args['preview_id'] ) ) {
		$query_args['preview_id'] = $post->ID;
	}

	// Build the new preview URL
	$url = esc_url_raw( add_query_arg( $query_args, trailingslashit( home_url() ) ) );

	return $url;
}


/**
 * If previewing a homepage, return that instead of the live homepage.
 *
 * @return \WP_Post|bool Wether the post being previewed or false.
 */
function get_preview_countdown_clock() {
	if ( is_preview() && isset( $_GET['preview_id'] ) ) {
		$post_id = absint( $_GET['preview_id'] );

		$homepages = new WP_Query(
			array(
				'post_type'              => gmr_countdownclocks_slug(),
				'post_status'            => 'any',
				'posts_per_page'         => 1,
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'fields'                 => 'ids',
				'update_post_term_cache' => false,
				'post__in'               => array( $post_id )
			)
		);

		return $homepages;
	}

	return false;
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
