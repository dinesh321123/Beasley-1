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
	 * Registers hooks.
	 *
	 * @access public
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_cpt' ) );
		add_action( 'init', array( $this, 'setup_permalinks' ) );
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		add_filter( 'post_type_link', array( $this, 'update_post_link' ), 10, 2 );
		add_filter( 'term_link', array( $this, 'update_term_link' ), 10, 3 );
		add_filter( 'whitelist_options', array( $this, 'whitelist_options' ) );

		$this->register_featured_image( self::TAXONOMY_CATEGORY );
	}

	/**
	 * Handles plugin activation.
	 *
	 * @static
	 * @access public
	 */
	public static function activation_hook() {
		$options = array(
			'listing-archive-permalink'  => 'directory',
			'listing-category-permalink' => sprintf( '/%s/%s/', self::TAG_LISTING_ARCHIVE, self::TAG_LISTING_CAT ),
			'listing-permalink'          => sprintf( '/%s/%s/%s/', self::TAG_LISTING_ARCHIVE, self::TAG_LISTING_CAT, self::TAG_LISTING_SLUG ),
		);

		foreach ( $options as $key => $default ) {
			$value = trim( get_option( $key ) );
			if ( empty( $value ) ) {
				update_option( $key, $default );
			}
		}

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
		$callback = array( $this, 'render_input_field' );

		add_settings_section( 'listing-archive', 'Archive Settings', '__return_false', 'directory-listing' );
		add_settings_section( 'listing-permalinks', 'Directory Listing Permalinks', array( $this, 'render_settings_description' ), 'directory-listing' );

		add_settings_field( 'listing-archive-permalink', 'Archive Slug', $callback, 'directory-listing', 'listing-permalinks', 'name=listing-archive-permalink' );
		add_settings_field( 'listing-category-permalink', 'Category', $callback, 'directory-listing', 'listing-permalinks', 'name=listing-category-permalink' );
		add_settings_field( 'listing-permalink', 'Listing', $callback, 'directory-listing', 'listing-permalinks', 'name=listing-permalink' );

		add_settings_field( 'listgin-archive-title', 'Title', $callback, 'directory-listing', 'listing-archive', 'name=listing-archive-title' );
		add_settings_field( 'listgin-archive-image', 'Featured Image', array( $this, 'render_image_field' ), 'directory-listing', 'listing-archive' );
		add_settings_field( 'listgin-archive-description', 'Description', array( $this, 'render_editor_field' ), 'directory-listing', 'listing-archive' );
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

	/**
	 * Registers settings page.
	 *
	 * @access public
	 * @action admin_menu
	 */
	public function register_menu() {
		$title = 'Directory Listing';
		$callback = array( $this, 'render_settings_page' );
		add_options_page( $title, $title, 'manage_options', 'directory-listing', $callback );
	}

	/**
	 * Renders settings page.
	 *
	 * @access public
	 */
	public function render_settings_page() {
		?><div class="wrap">
			<h1>Directory Listing</h1>

			<form id="settings-form" action="options.php" method="post">
				<?php settings_fields( 'directory-listing' ); ?>
				<?php do_settings_sections( 'directory-listing' ); ?>
				<?php submit_button(); ?>
			</form>
		</div><?php
	}

	/**
	 * Renders image selection field.
	 *
	 * @access public
	 */
	public function render_image_field() {
		wp_enqueue_media();
		wp_enqueue_script( 'featured-image', BEASLEY_LISTINGS_ABSURL . 'assets/media-assets.js', array( 'jquery' ), null, true );

		$image_id = get_option( 'listgin-archive-image' );

		$thumbnail_image = '';
		$thumbnail_image_src = wp_get_attachment_image_src( $image_id, 'thumbnail' );
		if ( ! empty( $thumbnail_image_src ) ) {
			$thumbnail_image = current( $thumbnail_image_src );
		}

		$styles = "background: url('{$thumbnail_image}') 50% 50% / cover no-repeat; width: 240px; height: 135px; margin-bottom: 1em; border: 1px solid #aaa";

		?><div>
			<input type="hidden" name="listgin-archive-image" value="<?php echo esc_attr( $image_id ); ?>">
			<div style="<?php echo esc_attr( $styles ); ?>"></div>
			<button type="button" class="button select-image" title="Select Image">Select</button>
			<button type="button" class="button clear-image">Clear</button>
		</div><?php
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
	public function render_input_field( $args ) {
		$args = wp_parse_args( $args );

		printf(
			'<input type="text" class="regular-text code" name="%s" value="%s">',
			esc_attr( $args['name'] ),
			esc_attr( get_option( $args['name'] ) )
		);
	}

	/**
	 * Renders editor field.
	 *
	 * @access public
	 */
	public function render_editor_field() {
		$content = get_option( 'listgin-archive-description' );
		wp_editor( $content, 'listgin-archive-description', array(
			'media_buttons' => false,
			'tinymce'       => false,
		) );
	}

	/**
	 * Updates whitelisted options list.
	 *
	 * @access public
	 * @filter whitelist_options
	 * @param array $options
	 * @return array
	 */
	public function whitelist_options( $options ) {
		$options['directory-listing'] = array(
			'listing-archive-title',
			'listgin-archive-image',
			'listgin-archive-description',
			'listing-archive-permalink',
			'listing-category-permalink',
			'listing-permalink',
		);

		return $options;
	}

}
