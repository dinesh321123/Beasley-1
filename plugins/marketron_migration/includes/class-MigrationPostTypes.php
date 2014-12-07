<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class MigrationPostTypes {
	public function __construct() {
		add_action( 'init', array( $this, 'check_and_add_cpt' ) );
	}

	public function check_and_add_cpt() {
		if( !post_type_exists( 'albums' )) {
			$labels = array(
				'name'                => __( 'Albums', 'gmiproto' ),
				'singular_name'       => __( 'Album', 'gmiproto' ),
				'add_new'             => _x( 'Add New Album', 'gmiproto', 'gmiproto' ),
				'add_new_item'        => __( 'Add New Album', 'gmiproto' ),
				'edit_item'           => __( 'Edit Album', 'gmiproto' ),
				'new_item'            => __( 'New Album', 'gmiproto' ),
				'view_item'           => __( 'View Album', 'gmiproto' ),
				'search_items'        => __( 'Search Albums', 'gmiproto' ),
				'not_found'           => __( 'No Albums found', 'gmiproto' ),
				'not_found_in_trash'  => __( 'No Albums found in Trash', 'gmiproto' ),
				'parent_item_colon'   => __( 'Parent Album:', 'gmiproto' ),
				'menu_name'           => __( 'Albums', 'gmiproto' ),
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
					'excerpt','custom-fields', 'comments',
					'revisions', 'page-attributes', 'post-formats'
				)
			);

			register_post_type( 'albums', $args );
		}

		if( !post_type_exists( 'tribe_events' )) {
			$labels = array(
				'name'                => __( 'Events', 'gmiproto' ),
				'singular_name'       => __( 'Event', 'gmiproto' ),
				'add_new'             => _x( 'Add New Event', 'gmiproto', 'gmiproto' ),
				'add_new_item'        => __( 'Add New Event', 'gmiproto' ),
				'edit_item'           => __( 'Edit Event', 'gmiproto' ),
				'new_item'            => __( 'New Event', 'gmiproto' ),
				'view_item'           => __( 'View Event', 'gmiproto' ),
				'search_items'        => __( 'Search Events', 'gmiproto' ),
				'not_found'           => __( 'No Events found', 'gmiproto' ),
				'not_found_in_trash'  => __( 'No Events found in Trash', 'gmiproto' ),
				'parent_item_colon'   => __( 'Parent Event:', 'gmiproto' ),
				'menu_name'           => __( 'Events', 'gmiproto' ),
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
					'excerpt','custom-fields', 'comments',
					'revisions', 'page-attributes', 'post-formats'
				)
			);

			register_post_type( 'tribe_events', $args );
		}

		if( !post_type_exists( 'contest' )) {
			$labels = array(
				'name'                => __( 'Contests', 'gmiproto' ),
				'singular_name'       => __( 'Contest', 'gmiproto' ),
				'add_new'             => _x( 'Add New Contest', 'gmiproto', 'gmiproto' ),
				'add_new_item'        => __( 'Add New Contest', 'gmiproto' ),
				'edit_item'           => __( 'Edit Contest', 'gmiproto' ),
				'new_item'            => __( 'New Contest', 'gmiproto' ),
				'view_item'           => __( 'View Contest', 'gmiproto' ),
				'search_items'        => __( 'Search Contests', 'gmiproto' ),
				'not_found'           => __( 'No Contests found', 'gmiproto' ),
				'not_found_in_trash'  => __( 'No Contests found in Trash', 'gmiproto' ),
				'parent_item_colon'   => __( 'Parent Contest:', 'gmiproto' ),
				'menu_name'           => __( 'Contests', 'gmiproto' ),
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
					'excerpt','custom-fields', 'comments',
					'revisions', 'page-attributes', 'post-formats'
				)
			);

			register_post_type( 'contest', $args );
		}

		if( !post_type_exists( 'contest_entry' )) {
			$labels = array(
				'name'                => __( 'Contest Entries', 'gmiproto' ),
				'singular_name'       => __( 'Contest Entry', 'gmiproto' ),
				'add_new'             => _x( 'Add New Contest Entry', 'gmiproto', 'gmiproto' ),
				'add_new_item'        => __( 'Add New Contest Entry', 'gmiproto' ),
				'edit_item'           => __( 'Edit Contest Entry', 'gmiproto' ),
				'new_item'            => __( 'New Contest Entry', 'gmiproto' ),
				'view_item'           => __( 'View Contest Entry', 'gmiproto' ),
				'search_items'        => __( 'Search Contest Entries', 'gmiproto' ),
				'not_found'           => __( 'No Contest Entries found', 'gmiproto' ),
				'not_found_in_trash'  => __( 'No Contest Entries found in Trash', 'gmiproto' ),
				'parent_item_colon'   => __( 'Parent Entry:', 'gmiproto' ),
				'menu_name'           => __( 'Contest Entries', 'gmiproto' ),
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
					'excerpt','custom-fields', 'comments',
					'revisions', 'page-attributes', 'post-formats'
				)
			);

			register_post_type( 'contest_entry', $args );
		}

		if( !post_type_exists( 'show' )) {
			$labels = array(
				'name'                => __( 'Shows', 'gmiproto' ),
				'singular_name'       => __( 'Show', 'gmiproto' ),
				'add_new'             => _x( 'Add New Show', 'gmiproto', 'gmiproto' ),
				'add_new_item'        => __( 'Add New Show', 'gmiproto' ),
				'edit_item'           => __( 'Edit Show', 'gmiproto' ),
				'new_item'            => __( 'New Show', 'gmiproto' ),
				'view_item'           => __( 'View Show', 'gmiproto' ),
				'search_items'        => __( 'Search Shows', 'gmiproto' ),
				'not_found'           => __( 'No Shows found', 'gmiproto' ),
				'not_found_in_trash'  => __( 'No Shows found in Trash', 'gmiproto' ),
				'parent_item_colon'   => __( 'Parent Show:', 'gmiproto' ),
				'menu_name'           => __( 'Shows', 'gmiproto' ),
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
					'excerpt','custom-fields', 'comments',
					'revisions', 'page-attributes', 'post-formats'
				)
			);

			register_post_type( 'show', $args );
		}

		if( !post_type_exists( 'survey' )) {
			$labels = array(
				'name'                => __( 'Surveys', 'gmiproto' ),
				'singular_name'       => __( 'Survey', 'gmiproto' ),
				'add_new'             => _x( 'Add New Survey', 'gmiproto', 'gmiproto' ),
				'add_new_item'        => __( 'Add New Survey', 'gmiproto' ),
				'edit_item'           => __( 'Edit Survey', 'gmiproto' ),
				'new_item'            => __( 'New Survey', 'gmiproto' ),
				'view_item'           => __( 'View Survey', 'gmiproto' ),
				'search_items'        => __( 'Search Surveys', 'gmiproto' ),
				'not_found'           => __( 'No Surveys found', 'gmiproto' ),
				'not_found_in_trash'  => __( 'No Surveys found in Trash', 'gmiproto' ),
				'parent_item_colon'   => __( 'Parent Survey:', 'gmiproto' ),
				'menu_name'           => __( 'Surveys', 'gmiproto' ),
			);

			$args = array(
				'labels'                   => $labels,
				'hierarchical'        => true,
				'description'         => 'description',
				'taxonomies'          => array(),
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
					'excerpt','custom-fields', 'comments',
					'revisions', 'page-attributes', 'post-formats'
				)
			);

			register_post_type( 'survey', $args );
		}

		if( !post_type_exists( 'response' )) {
			$labels = array(
				'name'                => __( 'Responses', 'gmiproto' ),
				'singular_name'       => __( 'Response', 'gmiproto' ),
				'add_new'             => _x( 'Add New Response', 'gmiproto', 'gmiproto' ),
				'add_new_item'        => __( 'Add New Response', 'gmiproto' ),
				'edit_item'           => __( 'Edit Response', 'gmiproto' ),
				'new_item'            => __( 'New Response', 'gmiproto' ),
				'view_item'           => __( 'View Response', 'gmiproto' ),
				'search_items'        => __( 'Search Responses', 'gmiproto' ),
				'not_found'           => __( 'No Contests found', 'gmiproto' ),
				'not_found_in_trash'  => __( 'No Contests found in Trash', 'gmiproto' ),
				'parent_item_colon'   => __( 'Parent Question:', 'gmiproto' ),
				'menu_name'           => __( 'Responses', 'gmiproto' ),
			);

			$args = array(
				'labels'                   => $labels,
				'hierarchical'        => true,
				'description'         => 'description',
				'taxonomies'          => array(),
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => 'edit.php?post_type=survey',
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
					'excerpt','custom-fields', 'comments',
					'revisions', 'page-attributes', 'post-formats'
				)
			);

			register_post_type( 'response', $args );
		}
	}
}

new MigrationPostTypes();
