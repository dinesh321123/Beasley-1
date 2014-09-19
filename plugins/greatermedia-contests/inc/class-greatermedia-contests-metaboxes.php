<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class GreaterMediaContestsMetaboxes {

	function __construct() {

		add_action( 'custom_metadata_manager_init_metadata', array( $this, 'custom_metadata_manager_init_metadata' ), 20, 3 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

	}

	public function custom_metadata_manager_init_metadata() {

		// Groups
		x_add_metadata_group(
			'prizes',
			array( 'contest' ),
			array(
				'label' => 'Prizes', // Label for the group
			)
		);

		x_add_metadata_group(
			'how-to-enter',
			array( 'contest' ),
			array(
				'label' => 'How to Enter', // Label for the group
			)
		);

		x_add_metadata_group(
			'rules',
			array( 'contest' ),
			array(
				'label' => 'Rules', // Label for the group
			)
		);

		x_add_metadata_group(
			'dates',
			array( 'contest' ),
			array(
				'label' => 'Eligible Dates', // Label for the group
			)
		);

		x_add_metadata_group(
			'gform-select',
			array('contest'),
			array(
				'label'    => 'Entry Form', // Label for the group
			)
		);

		// Fields
		x_add_metadata_field(
			'prizes-desc',
			array( 'contest' ),
			array(
				'group'      => 'prizes', // The slug of group the field should be added to. This needs to be registered with x_add_metadata_group first.
				'field_type' => 'wysiwyg', // The type of field; 'text', 'textarea', 'password', 'checkbox', 'radio', 'select', 'upload', 'wysiwyg', 'datepicker', 'taxonomy_select', 'taxonomy_radio'
				'label'      => 'What You Win', // Label for the field
			)
		);

		x_add_metadata_field(
			'how-to-enter-desc',
			array( 'contest' ),
			array(
				'group'      => 'how-to-enter', // The slug of group the field should be added to. This needs to be registered with x_add_metadata_group first.
				'field_type' => 'wysiwyg', // The type of field; 'text', 'textarea', 'password', 'checkbox', 'radio', 'select', 'upload', 'wysiwyg', 'datepicker', 'taxonomy_select', 'taxonomy_radio'
				'label'      => null, // Label for the field
			)
		);

		x_add_metadata_field(
			'rules-desc',
			array( 'contest' ),
			array(
				'group'      => 'rules', // The slug of group the field should be added to. This needs to be registered with x_add_metadata_group first.
				'field_type' => 'wysiwyg', // The type of field; 'text', 'textarea', 'password', 'checkbox', 'radio', 'select', 'upload', 'wysiwyg', 'datepicker', 'taxonomy_select', 'taxonomy_radio'
				'label'      => 'Official Contest Rules', // Label for the field
			)
		);

		x_add_metadata_field(
			'start-date',
			array( 'contest' ),
			array(
				'group'      => 'dates', // The slug of group the field should be added to. This needs to be registered with x_add_metadata_group first.
				'field_type' => 'datepicker', // The type of field; 'text', 'textarea', 'password', 'checkbox', 'radio', 'select', 'upload', 'wysiwyg', 'datepicker', 'taxonomy_select', 'taxonomy_radio'
				'label'      => 'Start Date', // Label for the field
			)
		);

		x_add_metadata_field(
			'end-date',
			array( 'contest' ),
			array(
				'group'      => 'dates', // The slug of group the field should be added to. This needs to be registered with x_add_metadata_group first.
				'field_type' => 'datepicker', // The type of field; 'text', 'textarea', 'password', 'checkbox', 'radio', 'select', 'upload', 'wysiwyg', 'datepicker', 'taxonomy_select', 'taxonomy_radio'
				'label'      => 'End Date', // Label for the field
			)
		);

		x_add_metadata_field(
			'form',
			array('contest'),
			array(
				'group'                   => 'gform-select', // The slug of group the field should be added to. This needs to be registered with x_add_metadata_group first.
				'field_type'              => 'radio', // The type of field; 'text', 'textarea', 'password', 'checkbox', 'radio', 'select', 'upload', 'wysiwyg', 'datepicker', 'taxonomy_select', 'taxonomy_radio'
				'label'                   => 'Select a Gravity Form', // Label for the field
				'values' 				  =>  $this->get_gravity_forms()
			)
		);

	}

	/**
	 * Return an array of active Gravity Forms
	 *
	 */
	public function get_gravity_forms() {
		if ( class_exists( 'RGFormsModel' ) ) {
			$forms      = RGFormsModel::get_forms( null, 'title' );
			$form_array = array();
			foreach ( $forms as $form ) {
				$form_array[$form->id] = $form->title;
			}

			return $form_array;
		}
	}

	public function add_meta_boxes() {

		add_meta_box(
			'contest-entries',
			'Contest Entries',
			array( $this, 'contest_entries_meta_box' ),
			'contest',
			'advanced',
			'default',
			array()
		);

	}

	public function contest_entries_meta_box() {

		global $post;

		$entries = get_children(
			array(
				'post_parent'    => $post->ID,
				'post_type'      => 'contest_entry',
				'posts_per_page' => - 1,
				'post_status'    => array( 'pending', 'publish' )
			)
		);

		include trailingslashit( GREATER_MEDIA_CONTESTS_PATH ) . 'tpl/contest-entries-meta-box.tpl.php';

	}

}

$GreaterMediaContestsMetaboxes = new GreaterMediaContestsMetaboxes();