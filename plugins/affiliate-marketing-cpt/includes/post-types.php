<?php

/**
 * Class AffiliateMarketingCPT
 */
class AffiliateMarketingCPT {

	const AFFILIATE_MARKETING_POST_TYPE = 'affiliate_marketing';

	/**
	 * Hook into the appropriate actions when the class is initiated.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'affiliate_cpt' ), 0 );

		add_filter( 'gmr-show-curation-post-types', array( __CLASS__, 'extend_curration_post_types' ) );
	}
	/**
	 * Add the Affiliate marketing Content Type
	 */
	public static function affiliate_cpt() {
		load_plugin_textdomain( AFFILIATE_MARKETING_CPT_TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		$labels = array(
			'name'                  => _x( 'Affiliate Marketing', 'Post type general name', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'singular_name'         => _x( 'Affiliate Marketing', 'Post type singular name', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'menu_name'             => _x( 'Affiliate Marketing', 'Admin Menu text', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'name_admin_bar'        => _x( 'Affiliate Marketing', 'Add New on Toolbar', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'add_new'               => __( 'Add New', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'add_new_item'          => __( 'Add New affiliate marketing', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'new_item'              => __( 'New affiliate marketing', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'edit_item'             => __( 'Edit affiliate marketing', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'view_item'             => __( 'View affiliate marketing', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'all_items'             => __( 'All affiliate marketing', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'search_items'          => __( 'Search affiliate marketing', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'parent_item_colon'     => __( 'Parent affiliate marketing:', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'not_found'             => __( 'No affiliate marketing found.', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'not_found_in_trash'    => __( 'No affiliate marketing found in Trash.', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'featured_image'        => _x( 'Affiliate Marketing Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'archives'              => _x( 'Affiliate Marketing archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'insert_into_item'      => _x( 'Insert into affiliate marketing', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'uploaded_to_this_item' => _x( 'Uploaded to this affiliate marketing', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'filter_items_list'     => _x( 'Filter affiliate marketing list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'items_list_navigation' => _x( 'Affiliate marketing list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'items_list'            => _x( 'Affiliate marketing list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
		);     
		$args = array(
			'labels'             => $labels,
			'description'        => 'Affiliate marketing custom post type.',
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'affiliate_marketing' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 29,
			'menu_icon'           => 'dashicons-networking',
			'supports'           => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail' ),
			'taxonomies'         => array( 'am_category', 'am_tag' ),
			'show_in_rest'       => true
		);
		register_post_type( self::AFFILIATE_MARKETING_POST_TYPE, $args );

		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => _x( 'Categories', 'taxonomy general name', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'singular_name'     => _x( 'Category', 'taxonomy singular name', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'search_items'      => __( 'Search Categories', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'all_items'         => __( 'All Categories', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'parent_item'       => __( 'Parent Category', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'parent_item_colon' => __( 'Parent Category:', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'edit_item'         => __( 'Edit Category', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'update_item'       => __( 'Update Category', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'add_new_item'      => __( 'Add New Category', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'new_item_name'     => __( 'New Category Name', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'menu_name'         => __( 'Category', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
		);
	
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'am_category' ),
		);
	
		register_taxonomy( 'am_category', array( self::AFFILIATE_MARKETING_POST_TYPE ), $args );
	
		unset( $args );
		unset( $labels );
	
		// Add new taxonomy, NOT hierarchical (like tags)
		$labels = array(
			'name'                       => _x( 'Tags', 'taxonomy general name', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'singular_name'              => _x( 'Tag', 'taxonomy singular name', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'search_items'               => __( 'Search Tags', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'popular_items'              => __( 'Popular Tags', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'all_items'                  => __( 'All Tags', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Tag', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'update_item'                => __( 'Update Tag', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'add_new_item'               => __( 'Add New Tag', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'new_item_name'              => __( 'New Tag Name', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'separate_items_with_commas' => __( 'Separate tags with commas', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'add_or_remove_items'        => __( 'Add or remove tags', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'choose_from_most_used'      => __( 'Choose from the most used tags', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'not_found'                  => __( 'No tags found.', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
			'menu_name'                  => __( 'Tags', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ),
		);
	
		$args = array(
			'hierarchical'          => false,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'am_tag' ),
		);
	
		register_taxonomy( 'am_tag', self::AFFILIATE_MARKETING_POST_TYPE, $args );

		/*
		* Create custom metabox in right side
		*/
		acf_add_local_field_group( array(
			'key'                   => 'affiliate_marketing_settings',
			'title'                 => 'Affiliate Marketing Settings',
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
						'value'    => self::AFFILIATE_MARKETING_POST_TYPE,
					),
				),
			),
			'fields'                => array(
				array(
					'key'           => 'field_hide_social_share_am',
					'label'         => 'Hide Social Share',
					'name'          => 'hide_social_share',
					'type'          => 'true_false',
					'instructions'  => 'Whether or not to display social share buttons on the affiliate marketing page.',
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
					'instructions'  => 'Detemines whether social share buttons should share the affiliate marketing page or just individual photos.',
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
					'instructions'  => 'Show central ad banner after every {X} images viewed in the affiliate marketing.',
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
		$post_types[] = self::AFFILIATE_MARKETING_POST_TYPE;
		return $post_types;
	}
}

AffiliateMarketingCPT::init();
