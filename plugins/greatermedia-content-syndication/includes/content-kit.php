<?php
/**
 * Created by Eduard
 * Date: 09.12.2014 20:45
 */

class ContentKit {

	public function __construct() {
		add_action( 'init', array( $this, 'register_content_kit_cpt' ) );
	}


	/**
	* Registers Content Kit post type
	* @uses $wp_post_types Inserts new post type object into the list
	*
	* @param string  Post type key, must not exceed 20 characters
	* @param array|string  See optional args description above.
	* @return object|WP_Error the registered post type object, or an error object
	*/
	function register_content_kit_cpt() {

		$labels = array(
			'name'                => __( 'Content Kits', 'gretermedia' ),
			'singular_name'       => __( 'Content Kit', 'gretermedia' ),
			'add_new'             => _x( 'Add New Content Kit', 'gretermedia', 'gretermedia' ),
			'add_new_item'        => __( 'Add New Content Kit', 'gretermedia' ),
			'edit_item'           => __( 'Edit Content Kit', 'gretermedia' ),
			'new_item'            => __( 'New Content Kit', 'gretermedia' ),
			'view_item'           => __( 'View Content Kit', 'gretermedia' ),
			'search_items'        => __( 'Search Content Kits', 'gretermedia' ),
			'not_found'           => __( 'No Content Kits found', 'gretermedia' ),
			'not_found_in_trash'  => __( 'No Content Kits found in Trash', 'gretermedia' ),
			'parent_item_colon'   => __( 'Parent Content Kit:', 'gretermedia' ),
			'menu_name'           => __( 'Content Kits', 'gretermedia' ),
		);

		$args = array(
			'labels'                   => $labels,
			'hierarchical'        => false,
			'description'         => 'description',
			'taxonomies'          => array( 'post_tag', 'category' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => null,
			'menu_icon'           => null,
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'has_archive'         => true,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => true,
			'capability_type'     => 'post',
			'supports'            => array(
				'title', 'editor', 'author', 'thumbnail',
				'excerpt','custom-fields', 'trackbacks', 'comments',
				'revisions', 'page-attributes', 'post-formats'
				)
		);

		register_post_type( 'content-kit', $args );

	}

}

$ContentKit = new ContentKit();