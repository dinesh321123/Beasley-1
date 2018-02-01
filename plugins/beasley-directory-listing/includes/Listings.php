<?php

namespace Beasley\DirectoryListing;

class Listings {

	use \Beasley\DirectoryListing\FeaturedImage;

	const TYPE_LISTING      = 'listing';
	const TAXONOMY_CATEGORY = 'listing-category';
	const TAXONOMY_TAG      = 'listing-tag';

	/**
	 * Registers hooks.
	 *
	 * @access public
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_cpt' ) );

		$this->register_featured_image( self::TAXONOMY_CATEGORY );
	}

	/**
	 * Registers custom post type and taxonomies.
	 *
	 * @access public
	 * @action init
	 */
	public function register_cpt() {
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

		register_post_type( self::TYPE_LISTING, array(
			'label'         => 'Listings',
			'labels'        => $labels,
			'public'        => true,
			'menu_position' => 21,
			'menu_icon'     => 'dashicons-exerpt-view',
			'has_archive'   => true,
			'rewrite'       => array( 'slug' => apply_filters( 'beasley_listing_slug', 'listing' ) ),
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		) );

		register_taxonomy( self::TAXONOMY_CATEGORY, array( self::TYPE_LISTING ), array(
			'public'        => true,
			'show_tagcloud' => false,
			'hierarchical'  => true,
		) );

		register_taxonomy( self::TAXONOMY_TAG, array( self::TYPE_LISTING ), array(
			'public'        => true,
			'show_tagcloud' => false,
		) );
	}

}
