<?php

/**
 * Created by Eduard
 * Date: 15.10.2014
 */
class ShowsCPT {

	const SHOW_CPT      = 'show';
	const SHOW_TAXONOMY = '_shows';
	const EPISODE_CPT   = 'show-episode';

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
			add_action( 'wp_enqueue_scripts', array( self::$_instance, 'enqueue_scripts' ) );

			add_action( 'wp_ajax_gmr_show_load_live_links', array( self::$_instance, 'load_more_links' ) );
			add_action( 'wp_ajax_nopriv_gmr_show_load_live_links', array( self::$_instance, 'load_more_links' ) );

			add_filter( 'gmr_blogroll_widget_item_post_types', array( self::$_instance, 'add_episode_pt_to_blogroll_widget' ) );
			add_filter( 'gmr_blogroll_widget_item_ids', array( self::$_instance, 'get_episodes_blogroll_widget_item_ids' ) );
		}

		return self::$_instance;
	}

	/**
	 * Registers shows post types
	 *
	 * @access public
	 */
	public function register_post_type() {
		register_post_type( self::SHOW_CPT, array(
			'public'              => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-megaphone',
			'has_archive'         => true,
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
		) );

		register_post_type( self::EPISODE_CPT, array(
			'public'     => false,
			'rewrite'    => false,
			'can_export' => true,
			'labels'     => array(
				'name'          => __( 'Show Episodes', 'greatermedia' ),
				'singular_name' => __( 'Show Episode', 'greatermedia' ),
			),
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
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => false,
			'hierarchical'      => true, // Show check boxes in the Shows meta box.
			'show_tagcloud'     => true,
			'show_ui'           => true,
			'query_var'         => true,
			'rewrite'           => true,
			'query_var'         => true,
			'capabilities'      => array(),
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
			'personality',
			'tribe_events',
			'survey',
			'livefyre-media-wall',
		);

		return $post_types;
	}

	/**
	 * Registers show episode post type in the blogroll widget.
	 *
	 * @filter gmr_blogroll_widget_item_post_types
	 * @param array $post_types The post types array.
	 * @return array The post types array.
	 */
	public function add_episode_pt_to_blogroll_widget( $post_types ) {
		$post_types[] = self::EPISODE_CPT;
		return $post_types;
	}

	/**
	 * Returns show episode ids to include into blogroll widget.
	 *
	 * @filter gmr_blogroll_widget_item_ids
	 * @param array $posts The array post ids.
	 * @return array The extended array with show episodes ids.
	 */
	public function get_episodes_blogroll_widget_item_ids( $posts ) {
		$query = new WP_Query();

		return array_merge( $posts, $query->query(  array(
			'post_type'           => self::EPISODE_CPT,
			'post_status'         => 'publish',
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'posts_per_page'      => 20,
			'fields'              => 'ids',
		) ) );
	}

	/**
	 * Enqueues front end scripts.
	 *
	 * @access public
	 */
	public function enqueue_scripts() {
		if ( is_singular( self::SHOW_CPT ) ) {
			$show_id = get_queried_object_id();
			$postfix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_script( 'greatermedia-show', GMEDIA_SHOWS_URL . 'assets/js/show' . $postfix . '.js', array( 'jquery' ), GMEDIA_SHOWS_VERSION, true );
			wp_localize_script( 'greatermedia-show', 'gmr_show', array(
				'ajaxurl' => add_query_arg( array(
					'action' => 'gmr_show_load_live_links', 
					'show'   => $show_id,
					'nonce'  => wp_create_nonce( 'show_load_more_links_' . $show_id ),
				), admin_url( 'admin-ajax.php' ) ),
			) );
		}
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

}

ShowsCPT::get_instance();
