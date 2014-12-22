<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class GreaterMediaContestEntry {

	const ENTRY_SOURCE_EMBEDDED_FORM = 'embedded_form';

	public $post;

	public $entrant_name;
	public $entrant_reference; // Gigya ID
	public $entry_source; // How this entry was created (i.e. "gravity-forms"
	public $entry_reference; // Reference/link to the source of the entry (i.e. Gravity Forms submission ID)

	protected function __construct( WP_Post $post_obj = null, $contest_id = null ) {

		if ( null !== $post_obj ) {

			if ( ! ( $post_obj instanceof WP_Post ) ) {
				throw new UnexpectedValueException( '$post_obj must be a WP_Post' );
			}

			$this->post              = $post_obj;
			$this->entrant_name      = get_post_meta( $this->post->ID, 'entrant_name', true );
			$this->entrant_reference = get_post_meta( $this->post->ID, 'entrant_reference', true );
			$this->entry_source      = get_post_meta( $this->post->ID, 'entry_source', true );
			$this->entry_reference   = get_post_meta( $this->post->ID, 'entry_reference', true );
		} else {
			$this->post            = new WP_Post( new stdClass() );
			$this->post->post_type = 'contest_entry';
		}

		if ( null !== $contest_id ) {

			if ( isset( $this->post->post_parent ) && ! empty( $this->post->post_parent ) ) {
				throw new UnexpectedValueException( 'Underlying "Contest Entry" post already has a parent Contest' );
			}

			$contest = get_post( $contest_id );
			if ( 'contest' !== $contest->post_type ) {
				throw new UnexpectedValueException( 'Contest ID passed as Parent does not reference a "Contest" post' );
			}

			$this->post->post_parent = $contest_id;

		}

	}

	/**
	 * Update the post an all associated metadata
	 */
	public function save() {

		if ( empty( $this->post->ID ) ) {
			$post_id = wp_insert_post( $this->post, true );
		} else {
			$post_id = wp_update_post( $this->post, true );
		}

		// Refresh the data
		$this->post = get_post( $post_id );

		update_post_meta( $post_id, 'entrant_name', $this->entrant_name );
		update_post_meta( $post_id, 'entrant_reference', $this->entrant_reference );
		update_post_meta( $post_id, 'entry_source', $this->entry_source );
		update_post_meta( $post_id, 'entry_reference', $this->entry_reference );

	}

	/**
	 * Set up hooks that don't relate to a particular instance of this class
	 */
	public static function register_cpt() {
		add_action( 'init', array( __CLASS__, 'contest_entry' ), 0 );
	}

	/**
	 * Register Custom Post Type
	 */
	public static function contest_entry() {

		$labels = array(
			'name'               => _x( 'Contest Entry', 'Post Type General Name', 'greatermedia_contests' ),
			'singular_name'      => _x( 'Contest Entry', 'Post Type Singular Name', 'greatermedia_contests' ),
			'menu_name'          => __( 'Contest Entry', 'greatermedia_contests' ),
			'parent_item_colon'  => __( 'Parent Contest:', 'greatermedia_contests' ),
			'all_items'          => __( 'All Entries', 'greatermedia_contests' ),
			'view_item'          => __( 'View Entry', 'greatermedia_contests' ),
			'add_new_item'       => __( 'Add New Entry', 'greatermedia_contests' ),
			'add_new'            => __( 'Add New', 'greatermedia_contests' ),
			'edit_item'          => __( 'Edit Entry', 'greatermedia_contests' ),
			'update_item'        => __( 'Update Entry', 'greatermedia_contests' ),
			'search_items'       => __( 'Search Entry', 'greatermedia_contests' ),
			'not_found'          => __( 'Not found', 'greatermedia_contests' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'greatermedia_contests' ),
		);
		$args   = array(
			'label'               => __( 'contest_entry', 'greatermedia_contests' ),
			'description'         => __( 'An entry in a Contest', 'greatermedia_contests' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'custom-fields' ),
			'taxonomies'          => array( 'category' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
		);

		register_post_type( 'contest_entry', $args );

	}

	/**
	 * Factory method to create a new contest entry for a given set of data
	 *
	 * @param int    $contest_id        Post ID of the related contest
	 * @param string $entrant_name      Name of the entrant
	 * @param string $entrant_reference Gigya ID
	 * @param string $entry_source      Source of the entry (i.e. "gravity-forms")
	 * @param string $entry_reference   ID or link to the source of the entry
	 *
	 * @throws UnexpectedValueException
	 * @return GreaterMediaContestEntry
	 */
	public static function create_for_data( $contest_id, $entrant_name, $entrant_reference, $entry_source, $entry_reference ) {

		$entry_source_camel_case      = str_replace( ' ', '', ucwords( str_replace( '-', ' ', $entry_source ) ) );
		$possible_entry_subclass_name = 'GreaterMediaContestEntry' . $entry_source_camel_case;
		if ( class_exists( $possible_entry_subclass_name ) ) {
			$entry = new $possible_entry_subclass_name( null, $contest_id );
		} else {
			$entry = new self( null, $contest_id );
		}


		if ( ! is_scalar( $entrant_name ) ) {
			throw new UnexpectedValueException( 'Entrant Name must be a scalar value' );
		}

		if ( ! is_scalar( $entry_source ) ) {
			throw new UnexpectedValueException( 'Entry Source must be a scalar value' );
		}

		// This is an assumption. We can always get rid of this check.
		if ( ! is_scalar( $entry_reference ) ) {
			throw new UnexpectedValueException( 'Entry Reference must be a scalar value' );
		}

		$entry->entrant_name      = $entrant_name;
		$entry->entrant_reference = $entrant_reference;
		$entry->entry_source      = $entry_source;
		$entry->entry_reference   = $entry_reference;

		return $entry;

	}

	/**
	 * Factory method to retrieve a GreaterMediaContestEntry object for a given post ID
	 *
	 * @param int $post_id
	 *
	 * @return GreaterMediaContestEntry
	 * @throws UnexpectedValueException
	 */
	public static function for_post_id( $post_id ) {

		$entry_post = get_post( $post_id );
		if ( 'contest_entry' !== $entry_post->post_type ) {
			throw new UnexpectedValueException( 'Post ID passed does not reference a "Contest Entry" post' );
		}

		$entry_source = get_post_meta( $post_id, 'entry_source', true );
		if ( self::ENTRY_SOURCE_EMBEDDED_FORM === $entry_source ) {
			$entry = new GreaterMediaContestEntryEmbeddedForm( $entry_post );
		} else {
			$entry = new self( $entry_post );
		}

		return $entry;

	}

	public function render_preview() {
		return "This is a generic submission";
	}

	/**
	 * Get the entry's Post ID
	 * @return int post ID
	 */
	public function post_id() {
		return $this->post->ID;
	}

}

GreaterMediaContestEntry::register_cpt();
