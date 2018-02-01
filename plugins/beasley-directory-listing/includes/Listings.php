<?php

namespace Beasley\DirectoryListing;

class Listings {

	use \Beasley\DirectoryListing\FeaturedImage;

	const TYPE_LISTING      = 'listing';
	const TAXONOMY_CATEGORY = 'listing-category';
	const TAXONOMY_TAG      = 'listing-tag';
	const TAG_LISTING_SLUG  = '%listing-slug%';
	const TAG_LISTING_CAT   = '%listing-cat%';

	/**
	 * Registers hooks.
	 *
	 * @access public
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_cpt' ) );
		add_action( 'init', array( $this, 'setup_permalinks' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		add_filter( 'post_type_link', array( $this, 'update_post_link' ), 10, 2 );

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
		return get_option(
			'listing-permalink',
			sprintf( '/directory/%s/%s/', self::TAG_LISTING_CAT, self::TAG_LISTING_SLUG )
		);
	}

	/**
	 * Renders permalink setting field.
	 *
	 * @access public
	 */
	public function render_permalink_field() {
		wp_nonce_field( 'listing-permalink', '__listing_nonce', false );

		printf(
			'<input type="text" class="regular-text code" name="listing-permalink" value="%s">' .
			'<p>Use <code>%s</code> to define category slug and <code>%s</code> to define listing slug.</p>',
			esc_attr( self::_get_permastruct() ),
			self::TAG_LISTING_CAT,
			self::TAG_LISTING_SLUG
		);
	}

	/**
	 * Sets up permalink structure.
	 *
	 * @access public
	 * @action init
	 */
	public function setup_permalinks() {
		add_rewrite_tag( self::TAG_LISTING_CAT, '([^/]+)', self::TAXONOMY_CATEGORY . '=' );
		add_rewrite_tag( self::TAG_LISTING_SLUG, '([^/]+)', self::TYPE_LISTING . '=' );

		add_permastruct( self::TYPE_LISTING, self::_get_permastruct(), array(
			'with_front'  => false,
			'paged'       => false,
			'feed'        => false,
			'forcomments' => false,
			'endpoints'   => false,
		) );
	}

	/**
	 * Updates listing permalink by adding listing slug and category.
	 *
	 * @access public
	 * @filter post_type_link
	 * @param string $link
	 * @param \WP_Post $post
	 * @return string
	 */
	public function update_post_link( $link, $post ) {
		if ( $post->post_type != self::TYPE_LISTING ) {
			return $link;
		}

		if ( stripos( $link, self::TAG_LISTING_SLUG ) !== false ) {
			$link = str_replace( self::TAG_LISTING_SLUG, $post->post_name, $link );
		}

		if ( stripos( $link, self::TAG_LISTING_CAT ) !== false ) {
			$category_slug = '';

			$cats = wp_get_post_terms( $post->ID, self::TAXONOMY_CATEGORY );
			if ( is_array( $cats ) && ! empty( $cats ) ) {
				$category = current( $cats );
				if ( is_a( $category, '\WP_Term' ) ) {
					$category_slug = $category->slug;
				}
			}

			$link = str_replace( self::TAG_LISTING_CAT, $category_slug, $link );
		}

		return $link;
	}

}
