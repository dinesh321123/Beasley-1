<?php
/**
 * General functions.
 *
 * @package GreaterMediaContests
 */

/**
 * Verify that form submission contains all required fields.
 *
 * @param  array $form Array of form field objects.
 * @return void
 */
function gmr_verify_form_submission( $form ) {
	
	foreach ( (array) $form as $field ) {
		if ( ! $field->required || 'section_break' === $field->field_type ) {
			continue;
		}

		$field_key = 'form_field_' . $field->cid;
		if ( 'file' === $field->field_type ) {

			if ( isset( $_FILES[ $field_key ] ) && file_is_valid_image( $_FILES[ $field_key ]['tmp_name'] ) ) {
				continue;
			}

		} else if ( isset( $_POST[ $field_key ] ) ) {

			if ( is_scalar( $_POST[ $field_key ] ) ) {

				$value = $_POST[ $field_key ];
				if ( 'radio' == $field->field_type && 'other' == $value ) {
					if ( ! empty( $_POST[ "{$field_key}_other_value" ] ) ) {
						$value = $_POST[ "{$field_key}_other_value" ];
					}
				}

				$value = trim( $value );
				if ( ! empty( $value ) || ( 'radio' === $field->field_type && in_array( $value, [ 0, '0' ] ) ) ) {
					continue;
				}

			} else if ( is_array( $_POST[ $field_key ] ) ) {

				$array_data = array();
				foreach ( $_POST[ $field_key ] as $value ) {
					if ( 'checkboxes' == $field->field_type && 'other' == $value ) {
						if ( empty( $_POST[ "{$field_key}_other_value" ] ) ) {
							continue;
						}

						$value = $_POST[ "{$field_key}_other_value" ];
					}

					$array_data[] = sanitize_text_field( $value );
				}

				$array_data = array_filter( array_map( 'trim', $array_data ), function( $val ) use ( $field ) {
					return ( ! empty( $val ) || ( 'checkboxes' === $field->field_type && in_array( $val, [ 0, '0' ] ) ) );
				} );
				if ( ! empty( $array_data ) ) {
					continue;
				}

			}
		}

		// required field is empty, so we need to interupt submission process
		status_header( 400 );
		exit;
	}
}
