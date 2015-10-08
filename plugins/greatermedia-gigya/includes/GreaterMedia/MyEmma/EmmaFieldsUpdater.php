<?php

namespace GreaterMedia\MyEmma;

class EmmaFieldsUpdater {

	public $fields = array(
		'first_name'             => array(
			'shortcut_name'      => 'first_name',
			'display_name'       => 'First Name',
			'field_type'         => 'text',
			'widget_type'        => 'text',
			'column_order'       => 1,
		),
		'last_name'         => array(
			'shortcut_name' => 'last_name',
			'display_name'  => 'Last Name',
			'field_type'    => 'text',
			'widget_type'   => 'text',
			'column_order'  => 2,
		),
		'birthday'          => array(
			'shortcut_name' => 'birthday',
			'display_name'  => 'Birthday',
			'field_type'    => 'date',
			'widget_type'   => 'date',
			'column_order'  => 3,
		),
		'gigya_user_id' => array(
			'shortcut_name' => 'gigya_user_id',
			'display_name'  => 'Gigya User ID',
			'field_type'    => 'text',
			'widget_type'   => 'text',
			'column_order'  => 4,
		),
	);

	public $emma_api;

	function __construct() {
		$this->emma_api = new EmmaAPI();
	}

	function update() {
		$active_fields = $this->fetch();
		$fields_to_add = $this->calc_fields_to_add( $active_fields );

		$this->add_fields( $fields_to_add );
	}

	function fetch() {
		$response = $this->emma_api->myFields();
		return json_decode( $response, true );
	}

	function add_fields( $fields ) {
		foreach ( $fields as $field ) {
			$this->add_field( $field );
		}
	}

	function add_field( $field ) {
		$response = $this->emma_api->fieldsAddSingle( $field );
		return json_decode( $response );
	}

	function calc_fields_to_add( $active_fields ) {
		$active_field_names   = array_column( $active_fields, 'shortcut_name' );
		$required_field_names = array_keys( $this->fields );
		$field_names_to_add   = array_diff( $required_field_names, $active_field_names );
		$fields_to_add        = array();

		foreach ( $field_names_to_add as $field ) {
			$fields_to_add[] = $this->fields[ $field ];
		}

		return $fields_to_add;
	}

}
