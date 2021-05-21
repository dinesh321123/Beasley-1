<?php

/**
 * Class ListicleCPT
 */
class ListicleCPT {

	const LISTICLE_POST_TYPE = 'listicle_cpt';

	/**
	 * Hook into the appropriate actions when the class is initiated.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'listicle_cpt' ), 0 );

		add_filter( 'gmr-homepage-curation-post-types', array( __CLASS__, 'extend_curration_post_types' ) );
		add_filter( 'gmr-show-curation-post-types', array( __CLASS__, 'extend_curration_post_types' ) );
	}
	/**
	 * Add the listicle Content Type
	 */
	public static function listicle_cpt() {
		load_plugin_textdomain( LISTICLE_CPT_TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		$labels = array(
			'name'                  => _x( 'Listicle', 'Post type general name', LISTICLE_CPT_TEXT_DOMAIN ),
			'singular_name'         => _x( 'Listicle', 'Post type singular name', LISTICLE_CPT_TEXT_DOMAIN ),
			'menu_name'             => _x( 'Listicle', 'Admin Menu text', LISTICLE_CPT_TEXT_DOMAIN ),
			'name_admin_bar'        => _x( 'Listicle', 'Add New on Toolbar', LISTICLE_CPT_TEXT_DOMAIN ),
			'add_new'               => __( 'Add New', LISTICLE_CPT_TEXT_DOMAIN ),
			'add_new_item'          => __( 'Add New listicle', LISTICLE_CPT_TEXT_DOMAIN ),
			'new_item'              => __( 'New listicle', LISTICLE_CPT_TEXT_DOMAIN ),
			'edit_item'             => __( 'Edit listicle', LISTICLE_CPT_TEXT_DOMAIN ),
			'view_item'             => __( 'View listicle', LISTICLE_CPT_TEXT_DOMAIN ),
			'all_items'             => __( 'All listicle', LISTICLE_CPT_TEXT_DOMAIN ),
			'search_items'          => __( 'Search listicle', LISTICLE_CPT_TEXT_DOMAIN ),
			'parent_item_colon'     => __( 'Parent listicle:', LISTICLE_CPT_TEXT_DOMAIN ),
			'not_found'             => __( 'No listicle found.', LISTICLE_CPT_TEXT_DOMAIN ),
			'not_found_in_trash'    => __( 'No listicle found in Trash.', LISTICLE_CPT_TEXT_DOMAIN ),
			'featured_image'        => _x( 'listicle Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', LISTICLE_CPT_TEXT_DOMAIN ),
			'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', LISTICLE_CPT_TEXT_DOMAIN ),
			'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', LISTICLE_CPT_TEXT_DOMAIN ),
			'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', LISTICLE_CPT_TEXT_DOMAIN ),
			'archives'              => _x( 'listicle archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', LISTICLE_CPT_TEXT_DOMAIN ),
			'insert_into_item'      => _x( 'Insert into listicle', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', LISTICLE_CPT_TEXT_DOMAIN ),
			'uploaded_to_this_item' => _x( 'Uploaded to this listicle', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', LISTICLE_CPT_TEXT_DOMAIN ),
			'filter_items_list'     => _x( 'Filter listicle list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', LISTICLE_CPT_TEXT_DOMAIN ),
			'items_list_navigation' => _x( 'listicle list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', LISTICLE_CPT_TEXT_DOMAIN ),
			'items_list'            => _x( 'listicle list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', LISTICLE_CPT_TEXT_DOMAIN ),
		);
		$rewrite = array(
			'slug'                => 'listicle',
			'with_front'          => true,
			'pages'               => true,
			'feeds'               => true,
		);
		$args = array(
			'labels'             => $labels,
			'description'        => 'listicle custom post type.',
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => $rewrite,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 29,
			'menu_icon'           => 'dashicons-list-view',
			'supports'           => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail' ),
			'taxonomies'         => array( 'category', 'post_tag' ),
			'show_in_rest'       => true
		);
		register_post_type( self::LISTICLE_POST_TYPE, $args );

		/*
		* Create custom metabox in right side
		*/
		acf_add_local_field_group( array(
			'key'                   => 'listicle_settings',
			'title'                 => 'Listicle Settings',
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
						'value'    => self::LISTICLE_POST_TYPE,
					),
				),
			),
			'fields'                => array(
				array(
					'key'           => 'field_hide_social_share_am',
					'label'         => 'Hide Social Share',
					'name'          => 'hide_social_share',
					'type'          => 'true_false',
					'instructions'  => 'Whether or not to display social share buttons on the listicle page.',
					'required'      => 0,
					'default_value' => 0,
					'ui'            => 1,
					'ui_on_text'    => '',
					'ui_off_text'   => '',
				),
				array(
					'key'           => 'field_share_photos_am',
					'label'         => 'Share Individual Photos',
					'name'          => 'share_photos',
					'type'          => 'true_false',
					'instructions'  => 'Detemines whether social share buttons should share the listicle page or just individual photos.',
					'required'      => 0,
					'default_value' => 0,
					'ui'            => 1,
					'ui_on_text'    => '',
					'ui_off_text'   => '',
				),
				array(
					'key'           => 'field_images_per_ad_am',
					'label'         => 'Ads Interval',
					'name'          => 'images_per_ad',
					'type'          => 'number',
					'instructions'  => 'Show central ad banner after every {X} images viewed in the listicle.',
					'required'      => 0,
					'default_value' => 3,
					'placeholder'   => '',
					'prepend'       => '',
					'append'        => '',
					'min'           => 1,
					'max'           => 99,
					'step'          => 1,
				),
			),
		) );
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
		$post_types[] = self::LISTICLE_POST_TYPE;
		return $post_types;
	}
}

ListicleCPT::init();
