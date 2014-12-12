<?php
/**
 * Class GMP_CPT
 *
 * This class creates the required `Podcasts` and `Episodes` Custom Post Types.
 *
 * A custom taxonomy of `_podcasts` is being constructed using a shadow taxonomy. Upon saving or updating a `Podcast` in
 * the `Podcast` custom post type, a check is run to see if an associated `_podcast` term has been generated for the
 * `Podcast`. If a term has not been generated, a term will then be created that is relational to the `Podcasts` and
 * is available in a `Podcast` meta box on the `Episodes` edit screen. If an associated term has already been generated,
 * the process will not generate a new one. A check is also in place to prohibit an `auto-save` from generating a term.
 *
 * Functionality is in place to delete a `_podcast` term if the associated `Podcast` has been deleted.
 *
 * The shadow taxonomy will allow an `Episode` to be associated with a `Podcast`.
 */
class GMP_CPT {

	const PODCAST_POST_TYPE = 'podcast';

	/**
	 * Hook into the appropriate actions when the class is initiated.
	 */
	public static function init() {

		add_action( 'init', array( __CLASS__, 'podcast_cpt' ), 0 );
		add_action( 'init', array( __CLASS__, 'episode_cpt' ), 0 );
		add_action( 'init', array( __CLASS__, 'register_shadow_taxonomy' ) );
		add_action( 'save_post', array( __CLASS__, 'update_shadow_taxonomy' ) );
		add_action( 'before_delete_post', array( __CLASS__, 'delete_shadow_tax_term' ) );

		add_filter( 'gmr_live_link_suggestion_post_types', array( __CLASS__, 'extend_live_link_suggestion_post_types' ) );

	}

	/**
	 * Add the Podcast Custom Post Type
	 */
	public static function podcast_cpt() {

		$labels = array(
			'name'                => _x( 'Podcasts', 'Post Type General Name', 'gmpodcasts' ),
			'singular_name'       => _x( 'Podcast', 'Post Type Singular Name', 'gmpodcasts' ),
			'menu_name'           => __( 'Podcasts', 'gmpodcasts' ),
			'parent_item_colon'   => __( 'Parent Item:', 'gmpodcasts' ),
			'all_items'           => __( 'All Podcasts', 'gmpodcasts' ),
			'view_item'           => __( 'View Podcast', 'gmpodcasts' ),
			'add_new_item'        => __( 'Add New Podcast', 'gmpodcasts' ),
			'add_new'             => __( 'Add New', 'gmpodcasts' ),
			'edit_item'           => __( 'Edit Podcast', 'gmpodcasts' ),
			'update_item'         => __( 'Update Podcast', 'gmpodcasts' ),
			'search_items'        => __( 'Search Podcasts', 'gmpodcasts' ),
			'not_found'           => __( 'Not found', 'gmpodcasts' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'gmpodcasts' ),
		);
		$rewrite = array(
			'slug'                => 'podcast',
			'with_front'          => true,
			'pages'               => true,
			'feeds'               => true,
		);
		$args = array(
			'label'               => __( 'podcast', 'gmpodcasts' ),
			'description'         => __( 'A post type for Podcasts', 'gmpodcasts' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', ),
			'taxonomies'          => array( 'post_tag' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-microphone',
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'capability_type'     => 'page',
		);
		register_post_type( self::PODCAST_POST_TYPE, $args );

	}

	/**
	 * Add the Episodes Custom Post Type
	 */
	public static function episode_cpt() {

		$labels = array(
			'name'                => _x( 'Episodes', 'Post Type General Name', 'gmpodcasts' ),
			'singular_name'       => _x( 'Episode', 'Post Type Singular Name', 'gmpodcasts' ),
			'menu_name'           => __( 'Episodes', 'gmpodcasts' ),
			'parent_item_colon'   => __( 'Parent Item:', 'gmpodcasts' ),
			'all_items'           => __( 'Episodes', 'gmpodcasts' ),
			'view_item'           => __( 'View Episode', 'gmpodcasts' ),
			'add_new_item'        => __( 'Add New Episode', 'gmpodcasts' ),
			'add_new'             => __( 'Add New', 'gmpodcasts' ),
			'edit_item'           => __( 'Edit Episode', 'gmpodcasts' ),
			'update_item'         => __( 'Update Episode', 'gmpodcasts' ),
			'search_items'        => __( 'Search Episodes', 'gmpodcasts' ),
			'not_found'           => __( 'Not found', 'gmpodcasts' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'gmpodcasts' ),
		);
		$rewrite = array(
			'slug'                => 'episode',
			'with_front'          => true,
			'pages'               => true,
			'feeds'               => true,
		);
		$args = array(
			'label'               => __( 'episode', 'gmpodcasts' ),
			'description'         => __( 'Episode CPT', 'gmpodcasts' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', ),
			'taxonomies'          => array( 'post_tag' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => 'edit.php?post_type=podcast',
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-media-text',
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'capability_type'     => 'page',
		);
		register_post_type( 'episode', $args );

	}

	/**
	 * Register the Podcasts Shadow Taxononmy
	 */
	public static function register_shadow_taxonomy() {

		$labels = array(
			'name'              => 'Podcast',
			'singular_name'     => 'Podcast',
			'search_items'      => 'Search',
			'all_items'         => 'All Podcasts',
			'parent_item'       => 'Parent Podcast',
			'parent_item_colon' => 'Parent Podcast: ',
			'edit_item'         => 'Edit Podcast',
			'update_item'       => 'Update Podcast',
			'add_new_item'      => 'Add New Podcast',
			'new_item_name'     => 'New Podcast',
			'menu_name'         => 'Podcasts',
		);

		$args = array(
			'labels'         => $labels,
			'rewrite'       => false,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'show_admin_column' => true,
			'show_tagcloud' => true,
			'hierarchical'  => true,

		);

		register_taxonomy( '_podcast', array( 'episode' ), $args );
	}

	/**
	 * Update the shadow taxonomy when a podcasts has been published or updated. Also ensure that the new term is no duplicated
	 *
	 * @param $post_id
	 */
	public static function update_shadow_taxonomy( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( 'podcast' !== get_post_type( $post_id ) ) {
			return;
		}

		$podcasts = get_post( $post_id );
		if ( null === $podcasts ) {
			return;
		}

		// To make sure we don't get things like 'auto-draft'
		if ( 'publish' != $podcasts->post_status ) {
			return;
		}

		if ( $podcasts->post_parent ) {
			$parent_podcast = get_post( $podcasts->post_parent );
			$parent_term = get_term_by( 'slug', $parent_podcast->post_name, '_podcast' );
		} else {
			$parent_term = false;
		}

		$term = get_term_by( 'slug', $podcasts->post_name, '_podcast' );

		if ( false === $term ) {
			$args = array();

			if ( $parent_term ) {
				$args['parent'] = $parent_term->term_id;
			}

			// See if there is an existing post_tag with the same slug as the publication. We can't trust WordPress to do this in wp_insert_term() because it will think "Complete Book Of Guns" and "Complete Book of Guns" (small "of") are different tags.
			$exising_term = get_term_by( 'slug', $podcasts->post_name, 'post_tag' );

			if ( false === $exising_term) {
				wp_insert_term( $podcasts->post_title, '_podcast', $args );
			} else {
				// If there is an existing term in post_tag, use its name instead of the publication's title. This bypasses any weird matching issues in wp_insert_term();
				wp_insert_term( $exising_term->name, '_podcast', $args );
			}
		} else {
			// We have the term. Make sure the term has the correct parent set (Could get out of sync if the issue was added without a parent, and changed later)

			// If we should have a parent term, but the term doesn't have this set - Add the parent ID
			if ( $parent_term && $parent_term->term_id != $term->parent ) {
				wp_update_term( $term->term_id, '_podcast', array( 'parent' => $parent_term->term_id ) );
			}

			// If we shouldn't have a parent term, but the term DOES have a parent set - Clear the parent ID
			if ( ! $parent_term && $term->parent != 0 ) {
				wp_update_term( $term->term_id, '_podcast', array( 'parent' => 0 ) );
			}
		}
	}

	/**
	 * Delete a podcast term when the corresponding podcast has been deleted
	 *
	 * @param $post_id
	 */
	public static function delete_shadow_tax_term( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( 'podcast' !== get_post_type( $post_id ) ) {
			return;
		}

		$podcasts = get_post( $post_id );
		if ( null === $podcasts ) {
			return;
		}

		$term = get_term_by( 'slug', $podcasts->post_name, '_podcast' );
		if ( false !== $term ) {
			wp_delete_term( $term->term_id, '_podcast' );
		}
	}

	/**
	 * Extends live link suggestion post types.
	 *
	 * @static
	 * @access public
	 * @param array $post_types The array of already registered post types.
	 * @return array The array of extended post types.
	 */
	public static function extend_live_link_suggestion_post_types( $post_types ) {
		$post_types[] = self::PODCAST_POST_TYPE;
		return $post_types;
	}

}

GMP_CPT::init();