<?php

/**
 * Created by Eduard
 * Date: 15.10.2014
 */
class ShowsCPT {

	const SHOW_CPT      = 'show';
	const SHOW_TAXONOMY = '_shows';

	/**
	 * The singleton instance of the ShowsCPT class.
	 *
	 * @static
	 * @access private
	 * @var ShowsCPT
	 */
	private static $_instance = null;

	/**
	 * Returns instance of the ShowsCPT class.
	 *
	 * @static
	 * @access public
	 * @return ShowsCPT
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new ShowsCPT();

			add_action( 'init', array( self::$_instance, 'register_post_type' ) );
			add_action( 'init', array( self::$_instance, 'register_shadow_taxonomy' ) );
			if ( ! is_admin() ) {
				add_action( 'pre_get_posts', array( self::$_instance, 'update_show_archive_query' ) );
			}

			add_action( 'wp_ajax_gmr_show_load_live_links', array( self::$_instance, 'load_more_links' ) );
			add_action( 'wp_ajax_nopriv_gmr_show_load_live_links', array( self::$_instance, 'load_more_links' ) );

			add_filter( 'redirect_canonical', array( self::$_instance, 'check_redirect_canonical' ) );
			add_filter( 'gmr-homepage-curation-post-types', array( self::$_instance, 'register_curration_post_type' ) );
		}

		return self::$_instance;
	}

	/**
	 * Update main query for shows post type archive to not fetch shows without homepages.
	 *
	 * @access public
	 * @param \WP_Query $query
	 */
	public function update_show_archive_query( $query ) {
		if ( ! $query->is_main_query() ) {
			return;
		}

		remove_action( 'pre_get_posts', array( self::$_instance, 'update_show_archive_query' ) );

		if ( $query->is_post_type_archive( self::SHOW_CPT ) || ( $query->is_single() && $query->get( 'post_type' ) == self::SHOW_CPT ) ) {
			// hide shows without homepage
			$meta_query = $query->get( 'meta_query' );
			if ( ! is_array( $meta_query ) ) {
				$meta_query = array();
			}

			$meta_query[] = array(
				'key'   => 'show_homepage',
				'value' => '1',
			);

			$query->set( 'meta_query', $meta_query );
		}
	}

	/**
	 * Registers show post type in the curration types list.
	 *
	 * @filter gmr-homepage-curation-post-types
	 * @param array $types Array of already registered types.
	 * @return array Extended array of post types.
	 */
	public function register_curration_post_type( $types ) {
		$types[] = self::SHOW_CPT;
		return $types;
	}

	/**
	 * Registers shows post types
	 *
	 * @access public
	 */
	public function register_post_type() {
		register_post_type( self::SHOW_CPT, array(
			'public'              => true,
			'menu_position'       => 37,
			'menu_icon'           => 'dashicons-megaphone',
			'has_archive'         => 'shows',
			'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'comments', 'revisions' ),
			'labels'              => array(
				'name'               => __( 'Shows', 'greatermedia' ),
				'singular_name'      => __( 'Show', 'greatermedia' ),
				'add_new'            => _x( 'Add New Show', 'greatermedia', 'greatermedia' ),
				'add_new_item'       => __( 'Add New Show', 'greatermedia' ),
				'edit_item'          => __( 'Edit Show', 'greatermedia' ),
				'new_item'           => __( 'New Show', 'greatermedia' ),
				'view_item'          => __( 'View Show', 'greatermedia' ),
				'search_items'       => __( 'Search Shows', 'greatermedia' ),
				'not_found'          => __( 'No Shows found', 'greatermedia' ),
				'not_found_in_trash' => __( 'No Shows found in Trash', 'greatermedia' ),
				'parent_item_colon'  => __( 'Parent Show:', 'greatermedia' ),
				'menu_name'          => __( 'Shows', 'greatermedia' ),
			),
			'capability_type' => array( 'show', 'shows' ),
			'map_meta_cap' => true,
			'rewrite' => array( 'slug' => 'shows' )
		) );
	}

	/**
	 * Regsiter shadow taxonomy for shows
	 *
	 * @access public
	 */
	public function register_shadow_taxonomy() {
		$labels = array(
			'name'                  => _x( 'Shows', 'Shows', 'greatermedia' ),
			'singular_name'         => _x( 'Show', 'Show', 'greatermedia' ),
			'search_items'          => __( 'Search Shows', 'greatermedia' ),
			'popular_items'         => __( 'Popular Shows', 'greatermedia' ),
			'all_items'             => __( 'All Shows', 'greatermedia' ),
			'parent_item'           => __( 'Parent Show', 'greatermedia' ),
			'parent_item_colon'     => __( 'Parent Show', 'greatermedia' ),
			'edit_item'             => __( 'Edit Show', 'greatermedia' ),
			'update_item'           => __( 'Update Show', 'greatermedia' ),
			'add_new_item'          => __( 'Add New Show', 'greatermedia' ),
			'new_item_name'         => __( 'New Show Name', 'greatermedia' ),
			'add_or_remove_items'   => __( 'Add or remove Show', 'greatermedia' ),
			'choose_from_most_used' => __( 'Choose from most used greatermedia', 'greatermedia' ),
			'menu_name'             => __( 'Shows', 'greatermedia' ),
		);

		$args = array(
			'labels'                => $labels,
			'public'                => true,
			'show_in_rest'          => true,
			'rest_base'             => '_shows',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'show_in_nav_menus'     => false,
			'show_admin_column'     => false,
			'hierarchical'          => true, // Show check boxes in the Shows meta box.
			'show_tagcloud'         => true,
			'show_ui'               => true,
			'query_var'             => true,
			'rewrite'               => true,
			'query_var'             => true,
			'capabilities'          => array(),
		);

		$supported_posttypes = self::get_supported_post_types();

		register_taxonomy( self::SHOW_TAXONOMY, (array) $supported_posttypes, $args );

		if ( function_exists( 'TDS\add_relationship' ) ) {
			TDS\add_relationship( self::SHOW_CPT, self::SHOW_TAXONOMY );
		}
	}

	/**
	 * Get supported post types.
	 *
	 * @return array The post types
	 */
	public static function get_supported_post_types() {
		$post_types = array(
			'post',
			'albums',
			'contest',
			'podcast',
			'tribe_events',
			'survey',
			'gmr_gallery',
			'gmr_album',
		);

		return $post_types;
	}

	/**
	 * Loads more live links for a show homepage.
	 *
	 * @access public
	 */
	public function load_more_links() {
		$show = filter_input( INPUT_GET, 'show', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
		$nonce = wp_verify_nonce( filter_input( INPUT_GET, 'nonce' ), 'show_load_more_links_' . $show );
		if ( ! $show || ! $nonce ) {
			status_header( 404 );
			exit;
		}

		$paged = filter_input( INPUT_GET, 'page', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1, 'default' => 1 ) ) );
		$query = \GreaterMedia\Shows\get_show_live_links_query( $show, $paged );
		if ( ! $query->have_posts() ) {
			exit;
		}

		while ( $query->have_posts() ) {
			$query->the_post();

			?><li class="live-link__type--<?php echo ( $format = get_post_format() ) ? $format : 'standard'; ?>">
				<div class="live-link__title">
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</div>
			</li><?php
		}
		exit;
	}

	/**
	 * Prevents canonical redirect for show archive.
	 */
	public function check_redirect_canonical( $redirect_url ) {
		return get_query_var( 'post_type' ) != self::SHOW_CPT || get_query_var( 'paged' ) < 2
			? $redirect_url
			: false;
	}

}

ShowsCPT::get_instance();
