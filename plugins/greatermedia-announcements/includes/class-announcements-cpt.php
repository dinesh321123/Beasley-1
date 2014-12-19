<?php
/**
 * Created by Eduard
 * Date: 09.12.2014 21:26
 */

class AnnouncementsCPT {

	public function __construct() {
		add_action( 'init', array( $this, 'register_announcements_cpt' ) );
	}


	/**
	* Registers announcements CPT
	* @uses $wp_post_types Inserts new post type object into the list
	*
	* @param string  Post type key, must not exceed 20 characters
	* @param array|string  See optional args description above.
	* @return object|WP_Error the registered post type object, or an error object
	*/
	function register_announcements_cpt() {

		$labels = array(
			'name'                => __( 'Announcements', 'greatermedia' ),
			'singular_name'       => __( 'Announcement', 'greatermedia' ),
			'add_new'             => _x( 'Add New Announcement', 'greatermedia', 'greatermedia' ),
			'add_new_item'        => __( 'Add New Announcement', 'greatermedia' ),
			'edit_item'           => __( 'Edit Announcement', 'greatermedia' ),
			'new_item'            => __( 'New Announcement', 'greatermedia' ),
			'view_item'           => __( 'View Announcement', 'greatermedia' ),
			'search_items'        => __( 'Search Announcements', 'greatermedia' ),
			'not_found'           => __( 'No Announcements found', 'greatermedia' ),
			'not_found_in_trash'  => __( 'No Announcements found in Trash', 'greatermedia' ),
			'parent_item_colon'   => __( 'Parent Announcement:', 'greatermedia' ),
			'menu_name'           => __( 'Announcements', 'greatermedia' ),
		);

		$args = array(
			'labels'                => $labels,
			'hierarchical'          => false,
			'description'           => 'description',
			'taxonomies'            => array( 'post_tag', 'category' ),
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'show_in_admin_bar'     => true,
			'menu_position'         => null,
			'menu_icon'             => null,
			'show_in_nav_menus'     => true,
			'publicly_queryable'    => true,
			'exclude_from_search'   => false,
			'has_archive'           => true,
			'query_var'             => true,
			'can_export'            => true,
			'rewrite'               => true,
			'capability_type'       => 'post',
			'supports'              => array(
				'title', 'editor', 'author', 'thumbnail',
				'excerpt','custom-fields', 'trackbacks', 'comments',
				'revisions', 'page-attributes', 'post-formats'
				)
		);

		if( get_current_blog_id() == BlogData::$content_site_id ) {
			register_post_type( 'announcement', $args );
		}
	}
}


$AnnouncementsCPT = new AnnouncementsCPT();