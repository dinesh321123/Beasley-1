<?php
/**
 * Created by Eduard
 * Date: 23.12.2014 20:52
 */

if ( ! defined( 'WPINC' ) ) {
	die( "Please don't try to access this file directly." );
}

class GreaterMediaSurveyEntry {

	public $post;

	public $entrant_name;
	public $entrant_reference;
	public $entry_source;
	public $entry_reference;

	public function __construct(  WP_Post $post_obj = null, $survey_id = null  ) {

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
			$this->post->post_type = 'survey_response';
		}

		if ( null !== $survey_id ) {

			if ( isset( $this->post->post_parent ) && ! empty( $this->post->post_parent ) ) {
				throw new UnexpectedValueException( 'Underlying "Survey Response" post already has a parent Survey' );
			}

			$survey = get_post( $survey_id );
			if ( 'survey' !== $survey->post_type ) {
				throw new UnexpectedValueException( 'Survey ID passed as Parent does not reference a "Survey" post' );
			}

			$this->post->post_parent = $survey_id;

		}
	}

	public static function register_survey_response() {
		add_action( 'init', array( __CLASS__, 'register_survey_response_cpt' ) );
	}

	/**
	 * Registers survey response cpt
	 */
	public static function register_survey_response_cpt() {

		$labels = array(
			'name'                => __( 'Survey responses', 'greatermedia' ),
			'singular_name'       => __( 'Survey response', 'greatermedia' ),
			'add_new'             => _x( 'Add New Survey response', 'greatermedia', 'greatermedia' ),
			'add_new_item'        => __( 'Add New Survey response', 'greatermedia' ),
			'edit_item'           => __( 'Edit Survey response', 'greatermedia' ),
			'new_item'            => __( 'New Survey response', 'greatermedia' ),
			'view_item'           => __( 'View Survey response', 'greatermedia' ),
			'search_items'        => __( 'Search Survey responses', 'greatermedia' ),
			'not_found'           => __( 'No survey responses found', 'greatermedia' ),
			'not_found_in_trash'  => __( 'No Survey responses found in Trash', 'greatermedia' ),
			'parent_item_colon'   => __( 'Parent Survey:', 'greatermedia' ),
			'menu_name'           => __( 'Survey responses', 'greatermedia' ),
		);

		$args = array(
			'labels'              => $labels,
			'hierarchical'        => true,
			'description'         => 'description',
			'taxonomies'          => array(),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => 'edit.php?post_type=' . GreaterMediaSurveys::$survey_slug,
			'show_in_admin_bar'   => false,
			'menu_position'       => null,
			'menu_icon'           => null,
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'has_archive'         => true,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => true,
			'capability_type'     => 'post',
			'supports'            => array(
				'title', 'custom-fields'
			)
		);

		register_post_type( 'survey_response', $args );
	}

	/**
	 * Update the post an all associated metadata
	 */
	public function save() {

		$post_id = wp_insert_post( $this->post, true );

		update_post_meta( $post_id, 'entrant_name', $this->entrant_name );
		update_post_meta( $post_id, 'entrant_reference', $this->entrant_reference );
		update_post_meta( $post_id, 'entry_source', $this->entry_source );
		update_post_meta( $post_id, 'entry_reference', $this->entry_reference );

	}

	public static function create_for_data( $survey_id, $entrant_name, $entrant_reference, $entry_source, $entry_reference ) {

		if ( class_exists( 'GreaterMediaSurveyEntry' ) ) {
			$entry = new GreaterMediaSurveyEntry( null, $survey_id );
		} else {
			$entry = new self( null, $survey_id );
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
}

GreaterMediaSurveyEntry::register_survey_response();