<?php

namespace GreaterMedia\Gigya\Ajax;

class ListEntryFieldsAjaxHandler extends AjaxHandler {

	public $fields_to_exclude = array(
		'file',
		'section_break',
	);

	public function get_action() {
		return 'list_entry_fields';
	}

	public function run( $params ) {
		$form_id      = $params['entryTypeID'];
		$form_fields  = $this->form_fields_for( $form_id );
		$entry_fields = array();

		if ( is_array( $form_fields ) ) {
			foreach ( $form_fields as $id => $form_field ) {
				if ( $this->is_allowed_field( $form_field ) ) {
					$entry_fields[] = $this->entry_field_for( $form_field, $id );
				}
			}
		}

		return $entry_fields;
	}

	public function is_allowed_field( $form_field ) {
		return ! in_array( $form_field['field_type'], $this->fields_to_exclude );
	}

	function form_data_key_for( $form_id ) {
		$post_type = get_post_type( $form_id );

		if ( $post_type === 'survey' ) {
			return 'survey_embedded_form';
		} else {
			return 'embedded_form';
		}
	}

	public function form_fields_for( $form_id ) {
		$data_key  = $this->form_data_key_for( $form_id );
		$form_data = get_post_meta( $form_id, $data_key, true );
		$form_json = json_decode( $form_data, true );

		// TODO: figure out why double decoding is needed here
		if ( is_string( $form_json ) ) {
			$form_json = json_decode( $form_json, true );
		}

		return $form_json;
	}

	public function entry_field_for( $form_field, $id ) {
		$entry_field            = array();
		$entry_field['label']   = $form_field['label'];
		$entry_field['value']   = $form_field['cid'];
		$entry_field['type']    = $form_field['field_type'];
		$entry_field['choices'] = $this->choices_for( $form_field );
		$entry_field['fieldOptions'] = $this->field_options_for( $form_field );

		return $entry_field;
	}

	public function choices_for( $form_field ) {
		$type    = $form_field['field_type'];
		$choices = array();

		if ( $type === 'dropdown' || $type === 'checkboxes' || $type === 'radio' ) {
			$choices = $this->choices_for_select( $form_field );
		} elseif ( $type === 'text' || $type === 'email' ) {
			$choices = array();
		} elseif ( $type === 'number' ) {
			$choices = array();
		} elseif ( $type === 'boolean' ) {
			$choices = array(
				array( 'label' => 'yes', 'value' => true ),
				array( 'label' => 'no', 'value' => false ),
			);
		}

		return $choices;
	}

	public function choices_for_select( $form_field ) {
		$form_choices = $form_field['field_options']['options'];
		$choices      = array();

		foreach ( $form_choices as $index => $form_choice ) {
			$choices[] = array(
				'label' => $form_choice['label'],
				// KLUDGE: The form entry is stored as the value not
				// the selection index, so using the label as the value
				'value' => $form_choice['label'],
			);
		}

		return $choices;
	}

	public function field_options_for( $form_field ) {
		if ( array_key_exists( 'field_options', $form_field ) ) {
			$options = array_diff_key(
				$form_field['field_options'],
				array( 'options' => new \stdClass() )
			);

			$options['fieldType'] = $form_field['field_type'];
		} else {
			$options = new \stdClass();
		}

		return $options;
	}

}
