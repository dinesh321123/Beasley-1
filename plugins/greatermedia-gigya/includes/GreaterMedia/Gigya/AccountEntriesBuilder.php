<?php

namespace GreaterMedia\Gigya;

class AccountEntriesBuilder {

	public $form;
	public $entry;
	public $entry_meta;

	// TODO: Full GravityForms mapping
	public $suffixes = array(
		'text'   => 't',
		'number' => 'f',
		'email'  => 't',
		'time'   => 'i',
		'select' => 't',
	);

	function __construct( $form, $entry, $entry_meta ) {
		$this->form       = $form;
		$this->entry      = $entry;
		$this->entry_meta = $entry_meta;
	}

	function build() {
		$entries = array();
		$fields  = $this->form['fields'];

		foreach ( $fields as $field ) {
			$entries[] = $this->entry_object_for( $field );
		}

		return $entries;
	}

	function entry_object_for( $field ) {
		$id           = $field['id'];
		$type         = $field['type'];
		$entry_object = array(
			'entry_type_s'    => $this->entry_meta['entry_type'],
			'entry_type_id_i' => $this->entry_meta['entry_type_id'],
			'entry_id_i'      => $this->entry['id'],
			'field_id_i'      => $id,
		);

		$value_key                  = $this->field_value_key_for( $type );
		$entry_object[ $value_key ] = $this->form_entry_for( $field );

		return $entry_object;
	}

	function form_entry_for( $field ) {
		$id = $field['id'];
		return $this->entry[ $id ];
	}

	function field_value_key_for( $type ) {
		return 'field_value_' . $this->field_value_suffix_for( $type );
	}

	function field_value_suffix_for( $type ) {
		if ( array_key_exists( $type, $this->suffixes ) ) {
			return $this->suffixes[ $type ];
		} else {
			return 's';
		}
	}

}
