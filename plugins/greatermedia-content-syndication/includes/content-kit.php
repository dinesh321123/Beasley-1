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
			'labels'              => $labels,
			'public'              => false,
			'show_ui'             => true,
			'query_var'           => false,
			'menu_position'       => 45,
			'can_export'          => true,
			'rewrite'             => false,
			'menu_icon'           => 'dashicons-media-document',
			'map_meta_cap'        => true,
			// Capabilities aren't working correctly right now for this CPT, so disabling and
			// allowing content kits to inherit post capabilities.
			// 'capability_type'     => array( 'content_kit', 'content_kits' ),
			'taxonomies'          => array( 'post_tag', 'category' ),
			'supports'            => array(
				'title', 'editor', 'author', 'thumbnail',
				'excerpt','custom-fields', 'trackbacks', 'comments',
				'revisions', 'page-attributes', 'post-formats',
			)
		);

		register_post_type( 'content-kit', $args );

	}

}

$ContentKit = new ContentKit();
