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
		add_action( 'init', array( $this, 'setup_permalinks' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );

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
			'rewrite'       => false,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		) );

		register_taxonomy( self::TAXONOMY_CATEGORY, array( self::TYPE_LISTING ), array(
			'public'        => true,
			'show_tagcloud' => false,
			'hierarchical'  => true,
			'rewrite'       => false,
		) );

		register_taxonomy( self::TAXONOMY_TAG, array( self::TYPE_LISTING ), array(
			'public'        => true,
			'show_tagcloud' => false,
			'rewrite'       => false,
		) );
	}

	/**
	 * Registers settings and checks if we need to save setting fields.
	 *
	 * @access public
	 * @action admin_init
	 */
	public function register_settings() {
		add_settings_field( 'listing-permalink', 'Directory Listing', array( $this, 'render_permalink_field' ), 'permalink', 'optional' );
		register_setting( 'permalink', 'listing-permalink', 'sanitize_text_field' );

		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			$nonce = filter_input( INPUT_POST, '__listing_nonce' );
			if ( wp_verify_nonce( $nonce, 'listing-permalink' ) && current_user_can( 'manage_option' ) ) {
				$permalink = filter_input( INPUT_POST, 'listing-permalink' );
				update_option( 'listing-permalink', sanitize_text_field( $permalink ), 'no' );
				flush_rewrite_rules();
			}
		}
	}

	/**
	 * Returns permastructure.
	 *
	 * @static
	 * @access protected
	 * @return string
	 */
	protected static function _get_permastruct() {
		return get_option( 'listing-permalink', '/directory/%listing-cat%/%listing-slug%/' );
	}

	/**
	 * Renders permalink setting field.
	 *
	 * @access public
	 */
	public function render_permalink_field() {
		wp_nonce_field( 'listing-permalink', '__listing_nonce', false );

		printf(
			'<input type="text" class="regular-text code" name="listing-permalink" value="%s">',
			esc_attr( self::_get_permastruct() )
		);

		echo '<p>Use <code>%listing-cat%</code> to define category slug and <code>%listing-slug%</code> to define listing slug.</p>';
	}

	/**
	 * Sets up permalink structure.
	 *
	 * @access public
	 * @action init
	 */
	public function setup_permalinks() {
		add_rewrite_tag( '%listing-cat%', '([^/]+)', self::TAXONOMY_CATEGORY . '=' );
		add_rewrite_tag( '%listing-slug%', '([^/]+)', self::TYPE_LISTING . '=' );

		add_permastruct( 'listing', self::_get_permastruct(), array(
			'with_front'  => false,
			'paged'       => false,
			'feed'        => false,
			'forcomments' => false,
			'endpoints'   => false,
		) );
	}

}
