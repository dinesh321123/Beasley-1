<?php
/**
 * Plugin Name: Beasley Directory Listing
 * Description: The directory listing functionality.
 * Version: 1.0
 * Author: 10up
 * Author URI: http://10up.com/
 */

function listings_init() {
	$labels = array(
		'name'                  => 'Listings',
		'singular_name'         => 'Listing',
		'add_new_item'          => 'Add New Listing',
		'edit_item'             => 'Edit Listing',
		'new_item'              => 'New Listing',
		'view_item'             => 'View Listing',
		'view_items'            => 'View Listings',
		'search_items'          => 'Search Listings',
		'not_found'             => 'No listings found',
		'not_found_in_trash'    => 'No listings found in Trash',
		'all_items'             => 'All Listings',
		'archives'              => 'Listing Archives',
		'attributes'            => 'Listing Attributes',
		'insert_into_item'      => 'Insert into listing',
		'uploaded_to_this_item' => 'Upload to this listing',
	);

	register_post_type( 'listing', array(
		'label'         => 'Listings',
		'labels'        => $labels,
		'public'        => true,
		'menu_position' => 21,
		'menu_icon'     => 'dashicons-exerpt-view',
		'has_archive'   => true,
		'rewrite'       => array( 'slug' => apply_filters( 'beasley_listing_slug', 'listing' ) ),
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
	) );

	register_taxonomy( 'listing-category', array( 'listing' ), array(
		'public'        => true,
		'show_tagcloud' => false,
		'hierarchical'  => true,
	) );

	register_taxonomy( 'listing-tag', array( 'listing' ), array(
		'public'        => true,
		'show_tagcloud' => false,
	) );
}
add_action( 'init', 'listings_init' );