<?php

// action hooks
add_action( 'init', 'gmr_streams_register_post_type', PHP_INT_MAX );

/**
 * Registers Live Stream post type.
 *
 * @action init
 */
function gmr_streams_register_post_type() {
	register_post_type( GMR_LIVE_STREAM_CPT, array(
		'public'               => false,
		'show_ui'              => true,
		'rewrite'              => false,
		'query_var'            => false,
		'can_export'           => false,
		'menu_position'        => 5,
		'supports'             => array( 'title', ),
		'taxonomies'           => array(),
		'label'                => 'Live Streams',
		'labels'               => array(
			'name'               => 'Live Streams',
			'singular_name'      => 'Live Stream',
			'menu_name'          => 'Live Streams',
			'name_admin_bar'     => 'Live Stream',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Stream',
			'new_item'           => 'New Stream',
			'edit_item'          => 'Edit Stream',
			'view_item'          => 'View Stream',
			'all_items'          => 'All Streams',
			'search_items'       => 'Search Streams',
			'parent_item_colon'  => 'Parent Streams:',
			'not_found'          => 'No links found.',
			'not_found_in_trash' => 'No links found in Trash.',
		),
	) );
}