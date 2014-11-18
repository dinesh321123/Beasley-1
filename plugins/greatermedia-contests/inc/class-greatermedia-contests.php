<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

/**
 * Class GreaterMediaContests
 * @see  https://core.trac.wordpress.org/ticket/12668#comment:27
 * @TODO abstract GreaterMediaContestEntry into its own class?
 */
class GreaterMediaContests {

	function __construct() {

		add_action( 'init', array( $this, 'register_contest_post_type' ) );
		add_action( 'init', array( $this, 'register_contest_type_taxonomy' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'restrict_manage_posts', array( $this, 'admin_contest_type_filter' ) );
		add_action( 'pre_get_posts', array( $this, 'admin_filter_contest_list' ) );
	}

	/**
	 * Register a Custom Post Type representing a contest
	 * @uses register_post_type
	 */
	public function register_contest_post_type() {

		$labels = array(
			'name'               => _x( 'Contests', 'Post Type General Name', 'greatermedia_contests' ),
			'singular_name'      => _x( 'Contest', 'Post Type Singular Name', 'greatermedia_contests' ),
			'menu_name'          => __( 'Contests', 'greatermedia_contests' ),
			'parent_item_colon'  => __( 'Parent Contest:', 'greatermedia_contests' ),
			'all_items'          => __( 'All Contests', 'greatermedia_contests' ),
			'view_item'          => __( 'View Contest', 'greatermedia_contests' ),
			'add_new_item'       => __( 'Add New Contest', 'greatermedia_contests' ),
			'add_new'            => __( 'Add New', 'greatermedia_contests' ),
			'edit_item'          => __( 'Edit Contest', 'greatermedia_contests' ),
			'update_item'        => __( 'Update Contest', 'greatermedia_contests' ),
			'search_items'       => __( 'Search Contests', 'greatermedia_contests' ),
			'not_found'          => __( 'Not found', 'greatermedia_contests' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'greatermedia_contests' ),
		);
		$args   = array(
			'label'               => __( 'contest', 'greatermedia_contests' ),
			'description'         => __( 'Contest', 'greatermedia_contests' ),
			'labels'              => $labels,
			'supports'            => array( 'title', ),
			'taxonomies'          => array( 'contest_type' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		);

		register_post_type( 'contest', $args );
		add_post_type_support( 'contest', 'timed-content' );

	}

	/**
	 * Register a custom taxonomy representing Contest Types
	 * @uses register_taxonomy
	 */
	public function register_contest_type_taxonomy() {

		$labels = array(
			'name'                       => _x( 'Contest Types', 'Taxonomy General Name', 'greatermedia_contests' ),
			'singular_name'              => _x( 'Contest Type', 'Taxonomy Singular Name', 'greatermedia_contests' ),
			'menu_name'                  => __( 'Contest Type', 'greatermedia_contests' ),
			'all_items'                  => __( 'All Contest Types', 'greatermedia_contests' ),
			'parent_item'                => __( 'Parent Contest Type', 'greatermedia_contests' ),
			'parent_item_colon'          => __( 'Parent Contest Type:', 'greatermedia_contests' ),
			'new_item_name'              => __( 'New Contest Type Name', 'greatermedia_contests' ),
			'add_new_item'               => __( 'Add New Contest Type', 'greatermedia_contests' ),
			'edit_item'                  => __( 'Edit Contest Type', 'greatermedia_contests' ),
			'update_item'                => __( 'Update Contest Type', 'greatermedia_contests' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'greatermedia_contests' ),
			'search_items'               => __( 'Search Contest Types', 'greatermedia_contests' ),
			'add_or_remove_items'        => __( 'Add or remove contest types', 'greatermedia_contests' ),
			'choose_from_most_used'      => __( 'Choose from the most used contest types', 'greatermedia_contests' ),
			'not_found'                  => __( 'Not Found', 'greatermedia_contests' ),
		);

		$args = array(
			'labels'            => $labels,
			// The data isn't hierarchical. This is just to make WP display checkboxes instead of free-form text entry
			'hierarchical'      => true,
			'public'            => false,
			'show_ui'           => true,
			'show_admin_column' => false,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
		);

		register_taxonomy( 'contest_type', array( 'contest' ), $args );

		$this->maybe_seed_contest_type_taxonomy();

	}

	/**
	 * Populate the initial records in the Contest Type taxonomy
	 *
	 * @uses wp_insert_term
	 * @uses get_option
	 * @uses set_option
	 */
	public function maybe_seed_contest_type_taxonomy() {

		$seeded = get_option( 'contest_type_seeded', false );

		if ( $seeded ) {
			return;
		}

		wp_insert_term(
			'On Air',
			'contest_type',
			array(
				'description' => 'On-air contests generally require a call or, perhaps, text message, from the entrant. The specific requirements and number to text or call can be written directly in the "how to enter" section of the contest.',
			)
		);

		wp_insert_term(
			'Online',
			'contest_type',
			array(
				'description' => '',
			)
		);

		delete_option( 'contest_type_seeded' );
		add_option( 'contest_type_seeded', true, '', true );

		if ( class_exists( 'GreaterMediaAdminNotifier' ) ) {
			GreaterMediaAdminNotifier::message( __( 'Seeded "Contest Types" taxonomy.', 'greatermedia_contests' ) );
		}

	}

	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'greatermedia-contests', trailingslashit( GREATER_MEDIA_CONTESTS_URL ) . 'css/greatermedia-contests.css' );
	}

	/**
	 * Add a dropdown on the contest list page to filter by contest type.
	 */
	public function admin_contest_type_filter() {
		global $typenow;
		$contest_type_tax_id = 0;

		if ( 'contest' !== $typenow || ! is_admin() ) {
			return;
		}

		if ( isset( $_GET['type_filter'] ) ) {
			// If user selected a term in the filter drop-down on the contest list page
			$contest_type_tax_id = intval( $_GET['type_filter'] );
		} else if ( isset( $_GET['contest_type'] ) ) {
			// If user clicked on the post count next to the taxonomy term
			$term = get_term_by( 'slug', $_GET['contest_type'], 'contest_type' );

			if ( false !== $term ) {
				$contest_type_tax_id = intval( $term->term_id );
			}
		}

		$args = array(
			'show_option_all'   => __( 'All contest types', 'greatermedia_contests' ),
			'hierarchical'       => true,
			'name'               => 'type_filter',
			'id'                 => 'type-filter',
			'class'              => 'postform',
			'orderby'            => 'name',
			'taxonomy'           => 'contest_type',
			'hide_if_empty'      => true,
			'selected'			 => $contest_type_tax_id,
		);

		wp_dropdown_categories( $args );
	}

	/**
	 * Handle the request to filter contests by type.
	 *
	 * @param  WP_Query $wp_query
	 */
	public function admin_filter_contest_list( $wp_query ) {
		global $typenow;

		$contest_type_tax_id = isset( $_GET['type_filter'] ) ? intval( $_GET['type_filter'] ) : 0;

		if ( 'contest' !== $typenow || ! is_admin() || empty( $contest_type_tax_id ) ) {
			return;
		}

		$args = array(
			array(
				'taxonomy' => 'contest_type',
				'field'    => 'id',
				'terms'    => $contest_type_tax_id,
			),
		);

		$wp_query->set( 'tax_query', $args );
	}
}

$GreaterMediaContests = new GreaterMediaContests();
