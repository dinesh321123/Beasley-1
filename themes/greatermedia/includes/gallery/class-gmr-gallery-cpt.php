<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

/**
 * Class GreaterMediaGalleryCPT
 */
class GreaterMediaGalleryCPT {

	const GALLERY_POST_TYPE = 'gmr_gallery';

	/**
	 * Hook into the appropriate actions when the class is initiated.
	 */
	public static function init() {

		add_action( 'init', array( __CLASS__, 'gallery_cpt' ), 0 );

	}

	/**
	 * Add the Gallery Content Type
	 */
	public static function gallery_cpt() {

		$labels = array(
			'name'                => _x( 'Galleries', 'Post Type General Name', 'greatermedia' ),
			'singular_name'       => _x( 'Gallery', 'Post Type Singular Name', 'greatermedia' ),
			'menu_name'           => __( 'Galleries', 'greatermedia' ),
			'parent_item_colon'   => __( 'Parent Item:', 'greatermedia' ),
			'all_items'           => __( 'All Galleries', 'greatermedia' ),
			'view_item'           => __( 'View Gallery', 'greatermedia' ),
			'add_new_item'        => __( 'Add New Gallery', 'greatermedia' ),
			'add_new'             => __( 'Add New', 'greatermedia' ),
			'edit_item'           => __( 'Edit Gallery', 'greatermedia' ),
			'update_item'         => __( 'Update Gallery', 'greatermedia' ),
			'search_items'        => __( 'Search Galleries', 'greatermedia' ),
			'not_found'           => __( 'Not found', 'greatermedia' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'greatermedia' ),
		);
		$rewrite = array(
			'slug'                => self::GALLERY_POST_TYPE,
			'with_front'          => true,
			'pages'               => true,
			'feeds'               => true,
		);
		$args = array(
			'label'               => __( 'gallery', 'greatermedia' ),
			'description'         => __( 'A post type for Galleries', 'greatermedia' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', ),
			'taxonomies'          => array( 'post_tag' ),
			'hierarchical'        => true,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-format-gallery',
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'capability_type'     => 'post',
		);
		register_post_type( self::GALLERY_POST_TYPE, $args );

	}

	/**
	 * Extends live link suggestion post types.
	 *
	 * @static
	 * @access public
	 * @param array $post_types The array of already registered post types.
	 * @return array The array of extended post types.
	 */
	public static function extend_live_link_suggestion_post_types( $post_types ) {
		$post_types[] = self::GALLERY_POST_TYPE;
		return $post_types;
	}

}

GreaterMediaGalleryCPT::init();