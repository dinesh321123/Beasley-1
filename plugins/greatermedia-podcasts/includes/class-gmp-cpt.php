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

	const PODCAST_POST_TYPE = 'podcast'; // todo fix all instances where this is hard coded to use this constant, then NAMESPACE
	const EPISODE_POST_TYPE = 'episode'; // todo fix all instances where this is hard coded to use this constant, then NAMESPACE

	/**
	 * Hook into the appropriate actions when the class is initiated.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'podcast_cpt' ), 0 );
		add_action( 'init', array( __CLASS__, 'episode_cpt' ), 0 );
		add_filter( 'gmr_live_link_suggestion_post_types', array( __CLASS__, 'extend_live_link_suggestion_post_types' ) );
		self::add_save_post_actions();
	}


	public static function add_save_post_actions() {
		add_action( 'save_post_' . self::EPISODE_POST_TYPE, array( __CLASS__, 'save_post' ), 10, 2 );
	}

	public static function remove_save_post_actions() {
		// Removes actions that might cause infinite loops
		remove_action( 'save_post_' . self::EPISODE_POST_TYPE, array( __CLASS__, 'save_post' ), 10, 2 );
	}

	/**
	 * Add the Podcast Custom Post Type
	 */
	public static function podcast_cpt() {

		$labels = array(
			'name'                => _x( 'Podcasts', 'Post Type General Name', 'gmpodcasts' ),
			'singular_name'       => _x( 'Podcast', 'Post Type Singular Name', 'gmpodcasts' ),
			'menu_name'           => __( 'Podcast/Episode', 'gmpodcasts' ),
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
			'register_meta_box_cb' => array( __CLASS__, 'parent_metabox' ),
		);
		register_post_type( self::EPISODE_POST_TYPE, $args );

	}

	public static function parent_metabox( \WP_Post $post ) {
		add_meta_box( 'episode-parent', 'Podcast', array( __CLASS__, 'render_parent_metabox' ), $post->post_type, 'side' );
	}

	public static function render_parent_metabox( \WP_Post $post ) {
		$podcast_args = array(
			'post_type' => self::PODCAST_POST_TYPE,
			'posts_per_page' => 100,
			'paged' => 0,
		);

		wp_nonce_field( 'save_podcast_parent', 'podcast_parent_nonce' );

		?>
		<select name="podcast-episode-parent" id="podcast-episode-parent">
		<option value="0">--Select a Podcast--</option>
		<?php

			do {
				$podcast_args['paged']++;
				$podcast_query = new WP_Query( $podcast_args );
				while( $podcast_query->have_posts() ) {
					$podcast = $podcast_query->next_post();

					?><option value="<?php echo intval( $podcast->ID ); ?>" <?php selected( $podcast->ID, $post->post_parent ); ?>><?php echo esc_html( $podcast->post_title ); ?></option><?php
				}
			} while ( $podcast_args['paged'] < $podcast_query->max_num_pages );

		?></select><?php

		wp_dropdown_pages( array( 'post_type' => self::PODCAST_POST_TYPE ) );
	}

	public static function save_post( $post_id, $post ) {
		if ( ! isset( $_POST['podcast_parent_nonce'] ) || ! wp_verify_nonce( $_POST['podcast_parent_nonce'], 'save_podcast_parent' ) ) {
			return;
		}

		if ( ! isset( $_POST['podcast-episode-parent'] ) ) {
			return;
		}

		$parent_id = intval( $_POST['podcast-episode-parent'] );

		$post->post_parent = $parent_id;

		self::remove_save_post_actions();
		wp_update_post( $post );
		self::add_save_post_actions();
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