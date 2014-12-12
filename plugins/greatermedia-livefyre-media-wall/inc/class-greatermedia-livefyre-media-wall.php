<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

/**
 * Class GreaterMediaLiveFyreMediaWall
 * Registers a custom post type and handles front-end (content) rendering
 */
class GreaterMediaLiveFyreMediaWall {

	function __construct() {

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

		add_filter( 'gmr_live_link_suggestion_post_types', array( $this, 'extend_live_link_suggestion_post_types' ) );
		
	}

	/**
	 * Implements init action
	 * Registers the content type
	 */
	public function init() {

		// Generated using http://generatewp.com/post-type/
		$labels = array(
			'name'               => _x( 'LiveFyre Media Walls', 'Post Type General Name', 'greatermedia-livefyre-media-wall' ),
			'singular_name'      => _x( 'LiveFyre Media Wall', 'Post Type Singular Name', 'greatermedia-livefyre-media-wall' ),
			'menu_name'          => __( 'Media Wall', 'greatermedia-livefyre-media-wall' ),
			'parent_item_colon'  => __( 'Parent Wall:', 'greatermedia-livefyre-media-wall' ),
			'all_items'          => __( 'All Walls', 'greatermedia-livefyre-media-wall' ),
			'view_item'          => __( 'View Wall', 'greatermedia-livefyre-media-wall' ),
			'add_new_item'       => __( 'Add New Wall', 'greatermedia-livefyre-media-wall' ),
			'add_new'            => __( 'Add a Wall', 'greatermedia-livefyre-media-wall' ),
			'edit_item'          => __( 'Edit Wall', 'greatermedia-livefyre-media-wall' ),
			'update_item'        => __( 'Update Wall', 'greatermedia-livefyre-media-wall' ),
			'search_items'       => __( 'Search Wall', 'greatermedia-livefyre-media-wall' ),
			'not_found'          => __( 'Not found', 'greatermedia-livefyre-media-wall' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'greatermedia-livefyre-media-wall' ),
		);
		$args   = array(
			'label'                => __( 'livefyre_media_wall', 'greatermedia-livefyre-media-wall' ),
			'description'          => __( 'LiveFyre Media Wall', 'greatermedia-livefyre-media-wall' ),
			'labels'               => $labels,
			'supports'             => array( 'title' ),
			'hierarchical'         => true,
			'public'               => true,
			'show_ui'              => true,
			'show_in_menu'         => true,
			'show_in_nav_menus'    => false,
			'show_in_admin_bar'    => true,
			'menu_position'        => 5,
			'can_export'           => true,
			'has_archive'          => false,
			'exclude_from_search'  => true,
			'publicly_queryable'   => true,
			'capability_type'      => 'post',
			'register_meta_box_cb' => array( $GLOBALS['GreaterMediaLiveFyreMediaWallAdmin'], 'add_meta_boxes' ),
		);
		register_post_type( 'livefyre-media-wall', $args );

	}

	/**
	 * Implements wp_enqueue_scripts action
	 * Enqueues JavaScript and sets up a "localization" object with settings & translations
	 */
	public function wp_enqueue_scripts() {

		$post_id = get_the_ID();
		if ( empty( $post_id ) ) {
			return;
		}

		wp_enqueue_script( 'livefyre', '//cdn.livefyre.com/Livefyre.js', array(), false, false );
		wp_enqueue_script( 'livefyre-media-wall', trailingslashit( GREATER_MEDIA_LIVEFYRE_WALLS_URL ) . 'js/livefyre-media-wall.js', array( 'livefyre' ), false, false );

		$media_wall_id      = get_post_meta( $post_id, 'media_wall_id', true );
		$media_wall_modal   = get_post_meta( $post_id, 'media_wall_allow_modal', true );
		$media_wall_initial = get_post_meta( $post_id, 'media_wall_initial', true );
		$media_wall_responsive = get_post_meta($post_id, 'media_wall_responsive', true);
		$media_wall_columns = get_post_meta( $post_id, 'media_wall_columns', true );
		$media_wall_min_width = get_post_meta( $post_id, 'media_wall_min_width', true );

		$settings = array(
			// One wall per page now, but trying to build in flexibility just in case
			'walls' => array(
				array(
					'element_id' => 'wall',
					'network'    => get_option( 'livefyre_media_walls_network', '' ),
					'site'       => get_option( 'livefyre_media_walls_site', '' ),
					'id'         => esc_attr( $media_wall_id ),
					'initial'    => absint( $media_wall_initial ),
					'modal'      => $media_wall_modal,
					'style' => $media_wall_responsive,
					'min-width' => absint( $media_wall_min_width),
					'columns'    => absint( $media_wall_columns ),
				)
			)
		);
		wp_localize_script( 'livefyre-media-wall', 'LiveFyreMediaWall', $settings );

	}

	/**
	 * Extends live link suggestion post types.
	 *
	 * @static
	 * @access public
	 * @param array $post_types The array of already registered post types.
	 * @return array The array of extended post types.
	 */
	public function extend_live_link_suggestion_post_types( $post_types ) {
		$post_types[] = 'livefyre-media-wall';
		return $post_types;
	}
	
}

$GreaterMediaLiveFyreMediaWall = new GreaterMediaLiveFyreMediaWall();