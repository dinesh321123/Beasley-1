<?php
/**
 * Created by Eduard
 * Date: 28.12.2014 2:46
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}


class GreaterMediaClosuresCPT {

	const CLOSURE_CPT_SLUG = 'closure';
	const CLOSURE_TYPE_SLUG = 'closures_types';
	const CLOSURE_ENTITY_TYPE_SLUG = 'closures_entity_types';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'gmedia_closures_cpt' ) );
		add_action( 'init', array( __CLASS__, 'gmedia_closures_type' ) );
		add_action( 'init', array( __CLASS__, 'gmedia_closures_entity_type' ) );
		add_action( 'admin_menu', array( __CLASS__, 'gmedia_closures_remove_metaboxes' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'gmedia_enqueue_scripts' ) );

		add_filter( 'enter_title_here', array( __CLASS__, 'gmedia_closures_change_title_text' ) );
	}

	public static function gmedia_enqueue_scripts() {
		if( is_post_type_archive( self::CLOSURE_CPT_SLUG ) ) {
			$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
			wp_enqueue_style( 'gmedia_closures_styles', GMCLOSURES_URL . 'assets/css/greater_media_closures' . $postfix  . '.css' );
		}
	}

	/**
	* Registers Closures CPT
	*/
	public static function gmedia_closures_cpt() {

		$labels = array(
			'name'                => __( 'Closures', 'greatermedia' ),
			'singular_name'       => __( 'Closure', 'greatermedia' ),
			'add_new'             => _x( 'Add New Closure', 'greatermedia', 'greatermedia' ),
			'add_new_item'        => __( 'Add New Closure', 'greatermedia' ),
			'edit_item'           => __( 'Edit Closure', 'greatermedia' ),
			'new_item'            => __( 'New Closure', 'greatermedia' ),
			'view_item'           => __( 'View Closure', 'greatermedia' ),
			'search_items'        => __( 'Search Closures', 'greatermedia' ),
			'not_found'           => __( 'No Closures found', 'greatermedia' ),
			'not_found_in_trash'  => __( 'No Closures found in Trash', 'greatermedia' ),
			'parent_item_colon'   => __( 'Parent Closure:', 'greatermedia' ),
			'menu_name'           => __( 'Closures', 'greatermedia' ),
		);

		$args = array(
			'labels'                   => $labels,
			'hierarchical'        => false,
			'description'         => 'description',
			'taxonomies'          => array(),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-welcome-comments',
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'has_archive'         => true,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => true,
			'capability_type'     => 'post',
			'supports'            => array(
				'title'
				)
		);

		register_post_type( self::CLOSURE_CPT_SLUG, $args );
	}


	/**
	 * Create Closure type taxonomy
	 */
	public static function gmedia_closures_type() {

		$labels = array(
			'name'					=> _x( 'Closure Types', 'Taxonomy Closure Types', 'greatermedia' ),
			'singular_name'			=> _x( 'Closure Type', 'Taxonomy Closure Type', 'greatermedia' ),
			'search_items'			=> __( 'Search Closure Types', 'greatermedia' ),
			'popular_items'			=> __( 'Popular Closure Types', 'greatermedia' ),
			'all_items'				=> __( 'All Closure Types', 'greatermedia' ),
			'parent_item'			=> __( 'Parent Closure Type', 'greatermedia' ),
			'parent_item_colon'		=> __( 'Parent Closure Type', 'greatermedia' ),
			'edit_item'				=> __( 'Edit Closure Type', 'greatermedia' ),
			'update_item'			=> __( 'Update Closure Type', 'greatermedia' ),
			'add_new_item'			=> __( 'Add New Closure Type', 'greatermedia' ),
			'new_item_name'			=> __( 'New Closure Type Name', 'greatermedia' ),
			'add_or_remove_items'	=> __( 'Add or remove Closure Types', 'greatermedia' ),
			'choose_from_most_used'	=> __( 'Choose from most used greatermedia', 'greatermedia' ),
			'menu_name'				=> __( 'Closure Type', 'greatermedia' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => true,
			'hierarchical'      => false,
			'show_tagcloud'     => true,
			'show_ui'           => true,
			'query_var'         => true,
			'rewrite'           => true,
			'capabilities'      => array(),
		);

		register_taxonomy( self::CLOSURE_TYPE_SLUG, array( self::CLOSURE_CPT_SLUG ), $args );
	}
	
	/**
	 * Create a taxonomy for closure entity types
	 */
	public static function gmedia_closures_entity_type() {

		$labels = array(
			'name'					=> _x( 'Entity Types', 'Taxonomy Entity Types', 'greatermedia' ),
			'singular_name'			=> _x( 'Entity Type', 'Taxonomy Entity Type', 'greatermedia' ),
			'search_items'			=> __( 'Search Entity Types', 'greatermedia' ),
			'popular_items'			=> __( 'Popular Entity Types', 'greatermedia' ),
			'all_items'				=> __( 'All Entity Types', 'greatermedia' ),
			'parent_item'			=> __( 'Parent Entity Type', 'greatermedia' ),
			'parent_item_colon'		=> __( 'Parent Entity Type', 'greatermedia' ),
			'edit_item'				=> __( 'Edit Entity Type', 'greatermedia' ),
			'update_item'			=> __( 'Update Entity Type', 'greatermedia' ),
			'add_new_item'			=> __( 'Add New Entity Type', 'greatermedia' ),
			'new_item_name'			=> __( 'New Entity Type Name', 'greatermedia' ),
			'add_or_remove_items'	=> __( 'Add or remove Entity Types', 'greatermedia' ),
			'choose_from_most_used'	=> __( 'Choose from most used greatermedia', 'greatermedia' ),
			'menu_name'				=> __( 'Entity Type', 'greatermedia' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => true,
			'hierarchical'      => false,
			'show_tagcloud'     => true,
			'show_ui'           => true,
			'query_var'         => true,
			'rewrite'           => true,
			'capabilities'      => array(),
		);

		register_taxonomy( self::CLOSURE_ENTITY_TYPE_SLUG, array( self::CLOSURE_CPT_SLUG ), $args );
	}

	/**
	 * Hide tags from closures page
	 */
	public static function gmedia_closures_remove_metaboxes() {
		remove_meta_box( 'tagsdiv-' . self::CLOSURE_ENTITY_TYPE_SLUG, self::CLOSURE_CPT_SLUG, 'normal' );
		remove_meta_box( 'tagsdiv-' . self::CLOSURE_TYPE_SLUG, self::CLOSURE_CPT_SLUG, 'normal' );
	}

	/**
	 * Change the title placeholder text for closures
	 * @return string
	 */
	public static function gmedia_closures_change_title_text( $title ) {
		$screen = get_current_screen();

		if  ( self::CLOSURE_CPT_SLUG == $screen->post_type ) {
			$title = 'Business or School Name Here';
		}

		return $title;
	}
}


GreaterMediaClosuresCPT::init();