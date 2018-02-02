<?php

namespace Beasley\DirectoryListing;

class Listings {

	use \Beasley\DirectoryListing\FeaturedImage;

	const TYPE_LISTING        = 'listing';
	const TAXONOMY_CATEGORY   = 'listing-category';
	const TAXONOMY_TAG        = 'listing-tag';
	const TAG_LISTING_SLUG    = '%listing-slug%';
	const TAG_LISTING_CAT     = '%listing-cat%';
	const TAG_LISTING_ARCHIVE = '%listing-archive%';

	/**
	 * Determines whether or not nonce field has been rendered.
	 *
	 * @access protected
	 * @var boolean
	 */
	protected $_rendered_nonce = false;

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
		add_filter( 'term_link', array( $this, 'update_term_link' ), 10, 3 );

		$this->register_featured_image( self::TAXONOMY_CATEGORY );
	}

	/**
	 * Handles plugin activation.
	 *
	 * @static
	 * @access public
	 */
	public static function activation_hook() {
		update_option( 'listing-archive-permalink', 'directory' );
		update_option( 'listing-category-permalink', sprintf( '/%s/%s/', self::TAG_LISTING_ARCHIVE, self::TAG_LISTING_CAT ) );
		update_option( 'listing-permalink', sprintf( '/%s/%s/%s/', self::TAG_LISTING_ARCHIVE, self::TAG_LISTING_CAT, self::TAG_LISTING_SLUG) );

		flush_rewrite_rules();
	}

	/**
	 * Handles plugin deactivation.
	 *
	 * @static
	 * @access public
	 */
	public static function deactivation_hook() {
		flush_rewrite_rules();
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
		$callback = array( $this, 'render_permalink_field' );

		add_settings_section( 'listings', 'Directory Listing Permalinks', array( $this, 'render_settings_description' ), 'permalink' );

		add_settings_field( 'listing-archive-permalink', 'Archive Slug', $callback, 'permalink', 'listings', 'name=listing-archive-permalink' );
		add_settings_field( 'listing-cat-permalink', 'Category', $callback, 'permalink', 'listings', 'name=listing-category-permalink' );
		add_settings_field( 'listing-permalink', 'Listing', $callback, 'permalink', 'listings', 'name=listing-permalink' );

		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			$nonce = filter_input( INPUT_POST, '__listing_nonce' );
			if ( wp_verify_nonce( $nonce, 'listing-permalink' ) && current_user_can( 'manage_option' ) ) {
				$options = array( 'listing-archive-permalink', 'listing-category-permalink', 'listing-permalink' );
				foreach ( $options as $option ) {
					$permalink = filter_input( INPUT_POST, $option );
					update_option( $option, sanitize_text_field( $permalink ) );
				}

				flush_rewrite_rules();
			}
		}
	}

	/**
	 * Renders description for settings section.
	 *
	 * @access public
	 */
	public function render_settings_description() {
		?><p>
			Use this settings to define permalink structures for directory listing archive,
			categories and individual listings. You can use <code><?php echo esc_html( self::TAG_LISTING_ARCHIVE ); ?></code>
			to define archive slug, <code><?php echo esc_html( self::TAG_LISTING_CAT ); ?></code> to define category slug
			and <code><?php echo esc_html( self::TAG_LISTING_SLUG ); ?></code> to define listing slug.
		</p><?php
	}

	/**
	 * Renders permalink setting field.
	 *
	 * @access public
	 */
	public function render_permalink_field( $args ) {
		if ( ! $this->_rendered_nonce ) {
			wp_nonce_field( 'listing-permalink', '__listing_nonce', false );
			$this->_rendered_nonce = true;
		}

		$args = wp_parse_args( $args );

		printf(
			'<input type="text" class="regular-text code" name="%s" value="%s">',
			esc_attr( $args['name'] ),
			esc_attr( get_option( $args['name'] ) )
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

		$archvie = trim( get_option( 'listing-archive-permalink' ), '/' );

		$permastruct = get_option( 'listing-permalink' );
		$permastruct = str_replace( self::TAG_LISTING_ARCHIVE, $archvie, $permastruct );

		add_permastruct( self::TYPE_LISTING, $permastruct, array(
			'with_front'  => false,
			'paged'       => false,
			'feed'        => false,
			'forcomments' => false,
			'endpoints'   => false,
		) );

		add_rewrite_rule( $archvie . '/?$', 'index.php?post_type=' . self::TYPE_LISTING, 'top' );
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

	/**
	 * Updates category permalink.
	 * 
	 * @access public
	 * @filter term_link
	 * @param string $link
	 * @param \WP_Term $term
	 * @param string $taxonomy
	 * @return string
	 */
	public function update_term_link( $link, $term, $taxonomy ) {
		if ( $taxonomy === self::TAXONOMY_CATEGORY ) {
			$permalink = get_option( 'listing-category-permalink' );
			if ( ! empty( $permalink ) ) {
				$permalink = str_replace( self::TAG_LISTING_CAT, $term->slug, $permalink );
				$permalink = str_replace( self::TAG_LISTING_ARCHIVE, get_option( 'listing-archive-permalink' ), $permalink );

				return $permalink;
			}
		}

		return $link;
	}

}
