<?php

/**
 * Class GreaterMediaGalleryCPT
 */
class GreaterMediaGalleryCPT {

	const ALBUM_POST_TYPE = 'gmr_album';
	const GALLERY_POST_TYPE = 'gmr_gallery';

	/**
	 * Hook into the appropriate actions when the class is initiated.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'gallery_cpt' ), 0 );
		add_action( 'init', array( __CLASS__, 'album_cpt' ), 0 );

		add_filter( 'gmr_live_link_suggestion_post_types', array( __CLASS__, 'extend_live_link_suggestion_post_types' ) );
		add_filter( 'gmr-homepage-curation-post-types', array( __CLASS__, 'extend_curration_post_types' ) );
		add_filter( 'gmr-show-curation-post-types', array( __CLASS__, 'extend_curration_post_types' ) );
		add_filter( 'gmr-homepage-exclude-post-types', array( __CLASS__, 'add_keep_off_homepage_widget' ) );

		self::add_save_actions();
	}

	public static function add_save_actions() {
		add_action( 'save_post_' . self::GALLERY_POST_TYPE, array( __CLASS__, 'save_gallery' ), 10, 2 );
	}

	public static function remove_save_actions() {
		remove_action( 'save_post_' . self::GALLERY_POST_TYPE, array( __CLASS__, 'save_gallery' ), 10 );
	}

	/**
	 * Add the Gallery Content Type
	 */
	public static function gallery_cpt() {
		$labels = array(
			'name'                => 'Galleries',
			'singular_name'       => 'Gallery',
			'menu_name'           => 'Galleries',
			'parent_item_colon'   => 'Parent Item:',
			'all_items'           => 'All Galleries',
			'view_item'           => 'View Gallery',
			'add_new_item'        => 'Add New Gallery',
			'add_new'             => 'Add New',
			'edit_item'           => 'Edit Gallery',
			'update_item'         => 'Update Gallery',
			'search_items'        => 'Search Galleries',
			'not_found'           => 'Not found',
			'not_found_in_trash'  => 'Not found in Trash',
		);

		$rewrite = array(
			'slug'                => 'galleries',
			'with_front'          => true,
			'pages'               => true,
			'feeds'               => true,
		);

		$args = array(
			'label'               => 'gallery',
			'description'         => 'A post type for Galleries',
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', ),
			'taxonomies'          => array( 'post_tag', 'category' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 31,
			'menu_icon'           => 'dashicons-format-gallery',
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'capability_type'     => array( 'gallery', 'galleries' ),
			'map_meta_cap'        => true,
		);

		register_post_type( self::GALLERY_POST_TYPE, $args );

		acf_add_local_field_group( array(
			'key'                   => 'gallery_settings',
			'title'                 => 'Gallery Settings',
			'menu_order'            => 0,
			'position'              => 'side',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => 1,
			'description'           => '',
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => self::GALLERY_POST_TYPE,
					),
				),
			),
			'fields'                => array(
				array(
					'key'           => 'field_is_featured',
					'label'         => 'Is Featured',
					'name'          => 'is_featured',
					'type'          => 'true_false',
					'instructions'  => 'Determines if this gallery is featured.',
					'required'      => 0,
					'default_value' => 0,
					'ui'            => 1,
					'ui_on_text'    => '',
					'ui_off_text'   => '',
				),
				array(
					'key'           => 'field_hide_download_link',
					'label'         => 'Hide Download Link',
					'name'          => 'hide_download_link',
					'type'          => 'true_false',
					'instructions'  => 'Whether or not to display download links on the gallery page.',
					'required'      => 0,
					'default_value' => 1,
					'ui'            => 1,
					'ui_on_text'    => '',
					'ui_off_text'   => '',
				),
				array(
					'key'           => 'field_hide_social_share',
					'label'         => 'Hide Social Share',
					'name'          => 'hide_social_share',
					'type'          => 'true_false',
					'instructions'  => 'Whether or not to display social share buttons on the gallery page.',
					'required'      => 0,
					'default_value' => 0,
					'ui'            => 1,
					'ui_on_text'    => '',
					'ui_off_text'   => '',
				),
				array(
					'key'           => 'field_share_photos',
					'label'         => 'Share Individual Photos',
					'name'          => 'share_photos',
					'type'          => 'true_false',
					'instructions'  => 'Detemines whether social share buttons should share the gallery page or just individual photos.',
					'required'      => 0,
					'default_value' => 0,
					'ui'            => 1,
					'ui_on_text'    => '',
					'ui_off_text'   => '',
				),
				array(
					'key'           => 'field_images_per_ad',
					'label'         => 'Ads Interval',
					'name'          => 'images_per_ad',
					'type'          => 'number',
					'instructions'  => 'Show central ad banner after every {X} images viewed in the gallery.',
					'required'      => 0,
					'default_value' => 3,
					'placeholder'   => '',
					'prepend'       => '',
					'append'        => '',
					'min'           => 1,
					'max'           => 99,
					'step'          => 1,
				),
				array(
					'key'               => 'field_sponsored_image',
					'label'             => 'Sponsored Image',
					'name'              => 'sponsored_image',
					'type'              => 'image',
					'instructions'      => 'Select an image for the sponsored slide.',
					'required'          => 0,
					'conditional_logic' => 0,
					'return_format'     => 'id',
					'preview_size'      => 'medium',
					'library'           => 'all',
					'min_width'         => '',
					'min_height'        => '',
					'min_size'          => '',
					'max_width'         => '',
					'max_height'        => '',
					'max_size'          => '',
					'mime_types'        => '',
				),
			),
		) );
	}

	public static function album_cpt() {

		$labels = array(
			'name'                => _x( 'Albums', 'Post Type General Name', 'greatermedia' ),
			'singular_name'       => _x( 'Album', 'Post Type Singular Name', 'greatermedia' ),
			'menu_name'           => __( 'Albums', 'greatermedia' ),
			'parent_item_colon'   => __( 'Parent Album:', 'greatermedia' ),
			'all_items'           => __( 'All Albums', 'greatermedia' ),
			'view_item'           => __( 'View Album', 'greatermedia' ),
			'add_new_item'        => __( 'Add New Album', 'greatermedia' ),
			'add_new'             => __( 'Add New', 'greatermedia' ),
			'edit_item'           => __( 'Edit Album', 'greatermedia' ),
			'update_item'         => __( 'Update Album', 'greatermedia' ),
			'search_items'        => __( 'Search Albums', 'greatermedia' ),
			'not_found'           => __( 'Not found', 'greatermedia' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'greatermedia' ),
		);
		$rewrite = array(
			'slug'                => 'albums',
			'with_front'          => true,
			'pages'               => true,
			'feeds'               => true,
		);
		$args = array(
			'label'               => __( 'album', 'greatermedia' ),
			'description'         => __( 'A post type for Albums', 'greatermedia' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'author', 'thumbnail', ),
			'taxonomies'          => array( 'post_tag', 'category' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_icon'           => 'dashicons-format-gallery',
			'menu_position'       => 30,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'capability_type'     => array( 'album', 'albums' ),
			'map_meta_cap'        => true,
		);
		register_post_type( self::ALBUM_POST_TYPE, $args );

	}

	public static function save_gallery( $post_id, $post ) {

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

	/**
	 * Extends homepage curration post types.
	 *
	 * @static
	 * @access public
	 * @param array $post_types The array of already registered post types.
	 * @return array The array of extended post types.
	 */
	public static function extend_curration_post_types( $post_types ) {
		$post_types[] = self::ALBUM_POST_TYPE;
		$post_types[] = self::GALLERY_POST_TYPE;
		return $post_types;
	}

	public static function add_keep_off_homepage_widget( $post_types ) {
		$post_types[] = self::GALLERY_POST_TYPE;
		return $post_types;
	}

}

GreaterMediaGalleryCPT::init();
