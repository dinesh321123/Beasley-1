<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class GreaterMediaFormbuilderRender {

	const INPUT_SIZE_SMALL = '10';
	const INPUT_SIZE_MEDIUM = '25';
	const INPUT_SIZE_LARGE = '40';

	const TEXTAREA_SIZE_SMALL = '3';
	const TEXTAREA_SIZE_MEDIUM = '5';
	const TEXTAREA_SIZE_LARGE = '10';

	private static $_entries = array();
	private static $_profile = null;

	/**
	 * Retrieve a custom list of HTML tags & attributes we're allowing in a rendered form
	 * @return array valid tags
	 */
	public static function allowed_tags() {

		static $tags;
		if ( ! isset( $tags ) ) {

			$tags = array();

			$tags['fieldset'] = array();
			$tags['hr']       = array();
			$tags['abbr']     = array( 'title' => 1 );

			// Add form tags
			$tags['input'] = array(
				'id'          => 1,
				'class'       => 1,
				'type'        => 1,
				'name'        => 1,
				'value'       => 1,
				'disabled'    => 1,
				'size'        => 1,
				'minlength'   => 1,
				'maxlength'   => 1,
				'pattern'     => 1,
				'placeholder' => 1,
				'step'        => 1,
				'readonly'    => 1,
				'min'         => 1,
				'max'         => 1,
				'form'        => 1,
				'checked'     => 1,
				'required'    => 1,
				'accept'      => 1,
			);

			$tags['textarea'] = array(
				'id'                     => 1,
				'name'                   => 1,
				'class'                  => 1,
				'autofocus'              => 1,
				'disabled'               => 1,
				'form'                   => 1,
				'required'               => 1,
				'onkeypress'             => 1,
				'pattern'                => 1,
				'data-parsley-minwords'  => 1,
				'data-parsley-maxwords'  => 1,
				'data-parsley-minlength' => 1,
				'data-parsley-maxlength' => 1,
				'minlength'              => 1,
				'maxlength'              => 1,
				'rows'                   => 1,
			);

			$tags['select'] = array(
				'id'        => 1,
				'name'      => 1,
				'class'     => 1,
				'autofocus' => 1,
				'disabled'  => 1,
				'form'      => 1,
				'multiple'  => 1,
				'required'  => 1,
				'size'      => 1,
			);

			$tags['option'] = array(
				'value'    => 1,
				'selected' => 1,
			);

			$tags['label'] = array(
				'for'   => 1,
				'form'  => 1,
				'class' => 1,
			);

			$tags['legend'] = array();

			$tags['p'] = array(
				'class' => 1,
			);

		}

		return $tags;
	}

	public static function parse_entry( $contest_id, $entry_id, $form = null, $strip_files = false ) {
		if ( isset( self::$_entries[ $contest_id ][ $entry_id ] ) ) {
			return self::$_entries[ $contest_id ][ $entry_id ];
		}

		if ( ! isset( self::$_entries[ $contest_id ] ) ) {
			self::$_entries[ $contest_id ] = array();
		}

		if ( ! $form ) {
			$form = get_post_meta( $contest_id, 'embedded_form', true );
			if ( empty( $form ) ) {
				return array();
			}

			if ( is_string( $form ) ) {
				$clean_form = trim( $form, '"' );
				$form = json_decode( $clean_form );
			}
		}

		$contest_entry = get_post_meta( $entry_id, 'entry_reference', true );
		if ( empty( $contest_entry ) ) {
			return array();
		}

		$results = array();
		$contest_entry = str_replace( "\\'", "'", $contest_entry ); // strip useless slashes
		$contest_entry = json_decode( $contest_entry, true );
		foreach ( $form as $field ) {
			if ( isset( $contest_entry[ $field->cid ] ) ) {
				if ( 'radio' == $field->field_type ) {
					$results[ $field->cid ] = array(
						'cid'	=> $field->cid,
						'type'  => $field->field_type,
						'label' => $field->label,
						'entry_field' => $field->sticky ? true: false,
						'display_name' => $field->admin_only ? true: false,
						'value' => ! empty( $field->field_options->options[ $contest_entry[ $field->cid ] ] )
							? $field->field_options->options[ $contest_entry[ $field->cid ] ]->label
							: $contest_entry[ $field->cid ],
					);
				} elseif ( 'checkboxes' == $field->field_type ) {
					$values = array();
					if ( is_array( $contest_entry[ $field->cid ] ) ) {
						foreach ( $contest_entry[ $field->cid ] as $value ) {
							$values[] = ! empty( $field->field_options->options[ $value ] )
								? $field->field_options->options[ $value ]->label
								: $value;
						}
					} else {
						$values = ! empty( $field->field_options->options[ $contest_entry[ $field->cid ] ] )
							? $field->field_options->options[ $contest_entry[ $field->cid ] ]->label
							: $contest_entry[ $field->cid ];
					}

					$results[ $field->cid ] = array(
						'cid'	=> $field->cid,
						'type'  => $field->field_type,
						'label' => $field->label,
						'value' => $values,
						'entry_field' => $field->sticky ? true : false,
						'display_name' => $field->admin_only ? true: false,
					);
				} elseif ( 'file' != $field->field_type || ! $strip_files ) {
					$results[ $field->cid ] = array(
						'cid'	=> $field->cid,
						'type'  => $field->field_type,
						'label' => $field->label,
						'value' => $contest_entry[ $field->cid ],
						'entry_field' => $field->sticky ? true: false,
						'display_name' => $field->admin_only ? true: false,
					);
				}
			}
		}

		self::$_entries[ $contest_id ][ $entry_id ] = $results;

		return self::$_entries[ $contest_id ][ $entry_id ];
	}

	private static function _get_user_field( $field, $default = null ) {
		if ( is_null( self::$_profile ) ) {
			self::$_profile = get_gigya_user_profile();
		}

		return ! empty( self::$_profile[ $field ] ) ? self::$_profile[ $field ] : $default;
	}

	private static function _get_default_fields() {
		$submitted_by = new stdClass();
		$submitted_by->cid = 'submitted_by';
		$submitted_by->name = 'userinfo_submitted_by';
		$submitted_by->label = 'Submitted By';
		$submitted_by->field_type = 'text';
		$submitted_by->required = true;

		$email_address = new stdClass();
		$email_address->cid = 'email';
		$email_address->name = 'userinfo_email';
		$email_address->label = 'Email Address';
		$email_address->field_type = 'email';
		$email_address->required = true;

		$date_of_birth = new stdClass();
		$date_of_birth->cid = 'dob';
		$date_of_birth->name = 'userinfo_dob';
		$date_of_birth->label = 'Date of Birth';
		$date_of_birth->field_type = 'date';
		$date_of_birth->required = true;

		$zip = new stdClass();
		$zip->cid = 'zip';
		$zip->name = 'userinfo_zip';
		$zip->label = 'Zip';
		$zip->field_type = 'text';
		$zip->required = true;

		return array( $submitted_by, $email_address, $date_of_birth, $zip );
	}

	/**
	 * Render a form attached to a given post
	 *
	 * @param int $post_id Post ID
	 * @param array $form The form fields array.
	 * @param bool $use_user_info Determines whether to show user info or not.
	 */
	public static function render( $post_id, $form = null, $use_user_info = true ) {
		if ( ! $form  ) {
			$form = get_post_meta( $post_id, 'embedded_form', true );
			if ( empty( $form ) ) {
				return;
			}

			if ( is_string( $form ) ) {
				$form = json_decode( trim( $form, '"' ) );
			}
		}

		// print_r( $form );

		$is_preivew = is_preview();
		$permalink = untrailingslashit( get_permalink( $post_id ) );

		$html = '';
		$title = get_post_meta( $post_id, 'form-title', true );
		if ( ! empty( $title ) ) {
			$html .= '<h3 class="contest__form--heading">' . esc_html( $title ) . '</h3>';
		}

		if ( $is_preivew ) {
		} else {
			$html .= '<iframe id="theiframe" name="theiframe" style="width:1px;height:1px;border:none;display:none"></iframe>';
			$html .= '<form action="' . esc_url( $permalink ) . '/action/submit/" target="theiframe" method="post" enctype="multipart/form-data" novalidate>';
		}

		if ( $use_user_info ) {
			$html .= '<div class="contest__form--user-info" style="display:none">';
			if ( function_exists( 'is_gigya_user_logged_in' ) && is_gigya_user_logged_in() ) {
				$html .= '<div class="user-info-box"></div>';
			} else {
				$html .= '<i>Enter this contest as a guest</i> <a href="' . esc_url( gmr_contests_get_login_url() ) . '">Login or Register</a>';
				foreach ( self::_get_default_fields() as $field ) {
					$renderer_method = 'render_' . $field->field_type;
					if ( method_exists( __CLASS__, $renderer_method ) ) {
						$html .= '<div class="contest__form--row">';
						$html .= self::$renderer_method( $post_id, $field );
						$html .= '</div>';
					}
				}
			}
			$html .= '</div>';
		}

		foreach ( $form as $field ) {
			$renderer_method = 'render_' . $field->field_type;
			if ( method_exists( __CLASS__, $renderer_method ) ) {
				$html .= '<div class="contest__form--row">';
				$html .= self::$renderer_method( $post_id, $field );
				$html .= '</div>';
			}
		}

		$submit_text = get_post_meta( $post_id, 'form-submitbutton', true );
		if ( empty( $submit_text ) ) {
			// If you change this string, be sure to get all the places it's used in this class
			$submit_text = __( 'Submit', 'greatermedia_contests' );
		}

		if ( $is_preivew ) {
			$html .= self::get_submit_button( $submit_text, null, 'button', null, true );
		} else {
			$html .= self::get_submit_button( $submit_text, null, 'submit', null, true );
			$html .= '</form>';
		}

		return $html;
	}

	/**
	 * Render a field's label tag
	 *
	 * @param stdClass $field
	 *
	 * @return string html
	 */
	public static function render_label( stdClass $field ) {

		$html = '';

		$field_id = 'form_field_' . $field->cid;

		$attributes = array(
			'for'   => $field_id,
			'class' => 'contest__form--label',
		);

		$label = ( isset( $field->label ) ) ? esc_html( $field->label ) : '';

		// Give the theme a chance to alter the attributes for the input field
		$attributes = apply_filters( 'gm_form_text_label_attrs', $attributes );
		$attributes = apply_filters( 'gm_form_label_attrs', $attributes );
		$label                = apply_filters( 'gm_form_text_label_text', $label );
		$label                = apply_filters( 'gm_form_label_text', $label );

		if ( ! empty( $label ) ) {

			if ( ! empty( $field->required ) && 'section_break' != $field->field_type ) {
				$label .= ' <abbr title="required">*</abbr>';
			}

			$html .= '<label ';

			foreach ( $attributes as $attribute => $value ) {
				$html .= wp_kses_data( $attribute ) . '="' . esc_attr( $value ) . '" ';
			}

			$html .= '>' . $label . '</label>';

		}

		return $html;
	}

	/**
	 * Render a legend tag
	 *
	 * @param stdClass $field
	 *
	 * @return string html
	 */
	public static function render_legend( stdClass $field ) {
		$label = ( isset( $field->label ) ) ? $field->label : '';
		$attributes = array(
			'class' => 'contest__form--label',
		);

		// Give the theme a chance to alter the attributes for the input field
		$attributes = apply_filters( 'gm_form_text_label_attrs', $attributes );
		$attributes = apply_filters( 'gm_form_label_attrs', $attributes );
		$label = apply_filters( 'gm_form_text_label_text', $label );
		$label = apply_filters( 'gm_form_label_text', $label );
		$label = esc_html( $label );

		$html = '';
		if ( ! empty( $label ) ) {
			if ( ! empty( $field->required ) && 'section_break' != $field->field_type ) {
				$label .= ' <abbr title="required">*</abbr>';
			}

			$html .= '<legend ';
			foreach ( $attributes as $attribute => $value ) {
				$html .= $attribute . '="' . esc_attr( $value ) . '" ';
			}
			$html .= '>' . $label . '</legend>';
		}

		return $html;
	}

	public static function render_description( stdClass $field ) {
		$description = isset( $field->field_options->description ) ? $field->field_options->description : '';

		$attributes = array(
			'class' => 'contest__form--description',
		);

		// Give the theme a chance to alter the attributes for the description
		$attributes = apply_filters( 'gm_form_text_description_attrs', $attributes );
		$attributes = apply_filters( 'gm_form_input_description_attrs', $attributes );
		$description = apply_filters( 'gm_form_text_description_text', $description );
		$description = apply_filters( 'gm_form_description_text', $description );

		$html = '';
		if ( ! empty( $description ) ) {
			$html .= '<p ';
			foreach ( $attributes as $attribute => $value ) {
				$html .= $attribute . '="' . esc_attr( $value ) . '" ';
			}
			$html .= ' >' . esc_html( $description ) . '</p>';
		}

		return $html;
	}

	/**
	 * Render a text field on a form using data from formbuilder
	 *
	 * @param stdClass $field
	 *
	 * @return string HTML
	 */
	public static function render_text( $post_id, stdClass $field ) {

		$special_attributes = array();

		if ( isset( $field->required ) && $field->required ) {
			$special_attributes['required'] = 'required';
		}

		// if ( isset( $field->sticky ) && $field->sticky ) {
		// 	$special_attributes['entry_field'] = 'required';
		// }

		if ( isset( $field->field_options->size ) ) {

			if ( 'small' === $field->field_options->size ) {
				$special_attributes['size'] = self::INPUT_SIZE_SMALL;
			} else if ( 'medium' === $field->field_options->size ) {
				$special_attributes['size'] = self::INPUT_SIZE_MEDIUM;
			} else if ( 'large' === $field->field_options->size ) {
				$special_attributes['size'] = self::INPUT_SIZE_LARGE;
			} else {
				throw new InvalidArgumentException( sprintf( 'Field %d has an invalid size', $field->cid ) );
			}

		}

		if ( isset( $field->field_options->minlength ) ) {
			$special_attributes['minlength'] = $field->field_options->minlength;
		}

		if ( isset( $field->field_options->maxlength ) ) {
			$special_attributes['maxlength'] = $field->field_options->maxlength;
		}

		return self::render_input_tag( 'text', $post_id, $field, $special_attributes );

	}

	/**
	 * Render a paragraph (textarea tag) on a form using data from formbuilder
	 *
	 * @param stdClass $field
	 *
	 * @return string HTML
	 */
	public static function render_paragraph( $post_id, stdClass $field ) {

		$html     = '';
		$field_id = 'form_field_' . $field->cid;

		$textarea_tag_attributes = array(
			'id'   => $field_id,
			'name' => $field_id,
		);

		$html .= self::render_label( $field );

		if ( isset( $field->required ) && $field->required ) {
			$textarea_tag_attributes['required'] = 'required';
		}

		$textarea_tag_attributes = self::paragraph_length_restriction_attributes( $field, $textarea_tag_attributes );
		$textarea_tag_attributes = self::paragraph_field_size_attributes( $field, $textarea_tag_attributes );

		// Give the theme a chance to alter the attributes for the input field
		$textarea_tag_attributes = apply_filters( 'gm_form_text_input_attrs', $textarea_tag_attributes );
		$textarea_tag_attributes = apply_filters( 'gm_form_input_attrs', $textarea_tag_attributes );

		$html .= '<textarea ';
		foreach ( $textarea_tag_attributes as $attribute => $value ) {
			$html .= wp_kses_data( $attribute ) . '="' . esc_attr( $value ) . '" ';
		}
		$html .= '></textarea>';

		$html .= self::render_description( $field );

		return $html;

	}

	/**
	 * Render a dropdown (select tag)
	 *
	 * @param int      $post_id
	 * @param stdClass $field
	 *
	 * @return string html
	 */
	public static function render_dropdown( $post_id, stdClass $field ) {

		$html = '';

		$field_id = 'form_field_' . $field->cid;

		$select_tag_attributes = array(
			'id'   => $field_id,
			'name' => $field_id,
		);

		$html .= self::render_label( $field );

		if ( isset( $field->required ) && $field->required ) {
			$select_tag_attributes['required'] = 'required';
		}

		// Give the theme a chance to alter the attributes for the input field
		$select_tag_attributes = apply_filters( 'gm_form_select_attrs', $select_tag_attributes );
		$select_tag_attributes = apply_filters( 'gm_form_input_attrs', $select_tag_attributes );

		$html .= '<select ';
		foreach ( $select_tag_attributes as $attribute => $value ) {
			$html .= wp_kses_data( $attribute ) . '="' . esc_attr( $value ) . '" ';
		}
		$html .= '>';

		if ( isset( $field->field_options->include_blank_option ) && $field->field_options->include_blank_option ) {
			$html .= '<option value=""></option>';
		}

		foreach ( $field->field_options->options as $option_data ) {
			$selected = selected( $option_data->checked, 1, false );
			$html .= '<option value="' . esc_attr( $option_data->label ) . '"' . $selected . '>' . wp_kses_data( $option_data->label ) . '</option>';
		}

		$html .= '</select>';

		$html .= self::render_description( $field );

		return $html;

	}

	public static function render_radio( $post_id, stdClass $field ) {
		return self::render_checkboxes( $post_id, $field, 'radio' );
	}

	public static function render_checkboxes( $post_id, stdClass $field, $input_type = 'checkbox' ) {

		$html = '';

		$html .= '<fieldset>';

		$html .= self::render_legend( $field );

		if ( isset( $field->field_options->options ) && is_array( $field->field_options->options ) ) {
			$multiple_choices = $input_type == 'checkbox' && count( $field->field_options->options ) > 1;
			foreach ( $field->field_options->options as $option_index => $option_data ) {
				$option_data->required = $field->required;
				$html .= self::render_single_checkbox( $field->cid, $option_index, $option_data, $input_type, $multiple_choices );
			}
		}

		if ( isset( $field->field_options->include_other_option ) && true == $field->field_options->include_other_option ) {
			$other_option_data = new stdClass();

			$other_option_data->label   = __( 'Other', 'greatermedia_contests' );
			$other_option_data->checked = false;
			$other_option_data->other   = true;

			$html .= self::render_single_checkbox( $field->cid, 'other', $other_option_data, $input_type, $multiple_choices );
		}

		$html .= self::render_description( $field );

		$html .= '</fieldset>';

		return $html;

	}

	/**
	 * Render a date input
	 *
	 * @param int      $post_id
	 * @param stdClass $field
	 *
	 * @return string html
	 */
	public static function render_date( $post_id, stdClass $field ) {
		return self::render_input_tag( 'date', $post_id, $field );
	}

	/**
	 * Render a time input
	 *
	 * @param int      $post_id
	 * @param stdClass $field
	 *
	 * @return string html
	 */
	public static function render_time( $post_id, stdClass $field ) {
		return self::render_input_tag( 'time', $post_id, $field );
	}

	/**
	 * Render a website (url) input
	 *
	 * @param int      $post_id
	 * @param stdClass $field
	 *
	 * @return string html
	 */
	public static function render_website( $post_id, stdClass $field ) {
		return self::render_input_tag( 'url', $post_id, $field );
	}

	/**
	 * Render an email input
	 *
	 * @param int      $post_id
	 * @param stdClass $field
	 *
	 * @return string html
	 */
	public static function render_email( $post_id, stdClass $field ) {
		return self::render_input_tag( 'email', $post_id, $field );
	}

	public static function render_price( $post_id, stdClass $field ) {

		$special_attributes = array(
			'step'    => '0.01',
			'pattern' => '\d+(\.\d{2})?',
		);

		return self::render_input_tag( 'text', $post_id, $field, $special_attributes );
	}

	/**
	 * Render an email input
	 *
	 * @param int      $post_id
	 * @param stdClass $field
	 *
	 * @return string html
	 */
	public static function render_address( $post_id, stdClass $field ) {

		$html = '';

		$html .= '<fieldset>';

		$html .= self::render_legend( $field );
		$description = self::render_description( $field );

		$html .= '<div>';

		$address_field = clone( $field );
		$address_field->cid .= '[address]';
		$address_field->label = 'Address';
		unset( $address_field->field_options->description );
		$html .= self::render_input_tag( 'text', $post_id, $address_field );

		$city_field = clone( $field );
		$city_field->cid .= '[city]';
		$city_field->label = 'City';
		unset( $city_field->field_options->description );
		$html .= self::render_input_tag( 'text', $post_id, $city_field );

		$state_field = clone( $field );
		$state_field->cid .= '[state]';
		$state_field->label = 'State';
		unset( $state_field->field_options->description );
		if ( empty( $state_field->field_options ) ) {
			$state_field->field_options = new stdClass();
		}
		$state_field->field_options->options = self::get_us_states();
		$html .= self::render_dropdown( $post_id, $state_field );

		$postal_code_field = clone( $field );
		$postal_code_field->cid .= '[postal_code]';
		$postal_code_field->label = 'Zip Code/Postal Code';
		unset( $postal_code_field->field_options->description );
		$special_attributes = array(
			// 5-character zip with optional zip+4 separated by space or dash
			'pattern' => '^\d{5}(?:[-\s]\d{4})?$',
		);
		$html .= self::render_input_tag( 'number', $post_id, $postal_code_field, $special_attributes );

		$html .= '</div>';

		$html .= $description;

		$html .= '</fieldset>';

		return $html;

	}

	/**
	 * Render a file upload field
	 *
	 * @param integer  $post_id
	 * @param stdClass $field
	 *
	 * @return string html
	 */
	public static function render_file( $post_id, stdClass $field ) {

		$special_attributes = array(
			'accept' => "image/*",
		);

		return self::render_input_tag( 'file', $post_id, $field, $special_attributes );

	}

	/**
	 * Generic input field renderer (used by the more specific rendering functions)
	 *
	 * @param string   $type               HTML5 input type
	 * @param int      $post_id
	 * @param stdClass $field
	 * @param array    $special_attributes tag attributes specific to the input type
	 *
	 * @return string html
	 */
	public static function render_input_tag( $type, $post_id, $field, $special_attributes = null ) {
		if ( null === $special_attributes ) {
			$special_attributes = array();
		}

		$field_id = 'form_field_' . $field->cid;

		$attributes = array_merge( $special_attributes, array(
			'id'                      => $field_id,
			'name'                    => ! empty( $field->name ) ? $field->name : $field_id,
			'type'                    => $type,
			'data-parsley-trim-value' => 'true',
		) );

		if ( isset( $field->required ) && $field->required ) {
			$attributes['required'] = 'required';
		}

		// Give the theme a chance to alter the attributes for the input field
		$attributes = apply_filters( 'gm_form_' . $type . '_input_attrs', $attributes );
		$attributes = apply_filters( 'gm_form_input_attrs', $attributes );

		$tag = '<input ';
		foreach ( $attributes as $attribute => $value ) {
			$tag .= $attribute . '="' . esc_attr( $value ) . '" ';
		}
		$tag .= '>';

		// Call filters in case certain input types need extra markup (like 'units' following a number field)
		$tag = apply_filters( 'gm_form_' . $type . '_input_tag', $tag, $post_id, $field );
		$tag = apply_filters( 'gm_form_input_tag', $tag, $post_id, $field );

		return self::render_label( $field ) . $tag . self::render_description( $field );
	}

	/**
	 * Render a number input
	 *
	 * @param int      $post_id
	 * @param stdClass $field
	 *
	 * @return string html
	 */
	public static function render_number( $post_id, stdClass $field ) {

		$special_attributes = array();

		if ( isset( $field->field_options->min ) ) {
			$special_attributes['min'] = absint( $field->field_options->min );
		}

		if ( isset( $field->field_options->max ) ) {
			$special_attributes['max'] = absint( $field->field_options->max );
		}

		if ( isset( $field->field_options->integer_only ) && $field->field_options->integer_only ) {
			$special_attributes['step'] = 1;
		}

		// Set up a filter to render units
		add_filter(
			'gm_form_number_input_tag',
			function ( $html, $post_id, $field ) {
				if ( isset( $field->field_options->units ) ) {
					return $html .= '<span class="units">' . wp_kses_data( $field->field_options->units ) . '</span>';
				}

				// Default
				return $html;
			},
			10, 3
		);

		$html = self::render_input_tag( 'number', $post_id, $field, $special_attributes );

		return $html;

	}

	/**
	 * Render a section break
	 *
	 * @param int      $post_id
	 * @param stdClass $field
	 *
	 * @return string html
	 */
	public static function render_section_break( $post_id, stdClass $field ) {

		$html = '';

		$html .= '<hr />';

		$html .= self::render_label( $field );

		$html .= self::render_description( $field );

		return $html;

	}

	/**
	 * get_submit_button() from WordPress 4.0
	 * Returns a submit button, with provided text and appropriate class
	 *
	 * @since 3.1.0
	 *
	 * @param string       $text             The text of the button (defaults to 'Save Changes')
	 * @param string       $type             The type of button. One of: primary, secondary, delete
	 * @param string       $name             The HTML name of the submit button. Defaults to "submit". If no id attribute
	 *                                       is given in $other_attributes below, $name will be used as the button's id.
	 * @param bool         $wrap             True if the output button should be wrapped in a paragraph tag,
	 *                                       false otherwise. Defaults to true
	 * @param array|string $other_attributes Other attributes that should be output with the button,
	 *                                       mapping attributes to their values, such as array( 'tabindex' => '1' ).
	 *                                       These attributes will be output as attribute="value", such as tabindex="1".
	 *                                       Defaults to no other attributes. Other attributes can also be provided as a
	 *                                       string such as 'tabindex="1"', though the array format is typically cleaner.
	 *
	 * @return string HTML
	 */
	public static function get_submit_button( $text = null, $type = 'primary large', $button_type = 'submit', $name = 'submit', $wrap = true, $other_attributes = null ) {
		if ( ! is_array( $type ) ) {
			$type = explode( ' ', $type );
		}

		$button_shorthand = array( 'primary', 'small', 'large' );
		$classes          = array( 'button' );
		foreach ( $type as $t ) {
			if ( 'secondary' === $t || 'button-secondary' === $t ) {
				continue;
			}
			$classes[] = in_array( $t, $button_shorthand ) ? 'button-' . $t : $t;
		}
		$class = implode( ' ', array_unique( $classes ) );

		if ( 'delete' === $type ) {
			$class = 'button-secondary delete';
		}

		$text = $text ? $text : __( 'Save Changes' );

		// Default the id attribute to $name unless an id was specifically provided in $other_attributes
		$id = $name;
		if ( is_array( $other_attributes ) && isset( $other_attributes['id'] ) ) {
			$id = $other_attributes['id'];
			unset( $other_attributes['id'] );
		}

		$attributes = '';
		if ( is_array( $other_attributes ) ) {
			foreach ( $other_attributes as $attribute => $value ) {
				$attributes .= $attribute . '="' . esc_attr( $value ) . '" '; // Trailing space is important
			}
		} else if ( ! empty( $other_attributes ) ) { // Attributes provided as a string
			$attributes = $other_attributes;
		}

		$button = '<button type="' . esc_attr( $button_type ) . '" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '" ' . $attributes . '><i class="gmr-icon icon-spin icon-spin"></i> ' . esc_attr( $text ) . '</button>';

		if ( $wrap ) {
			$button = '<p class="submit">' . $button . '</p>';
		}

		return $button;
	}

	/**
	 * @param string     $cid
	 * @param int|string $option_index
	 * @param stdClass   $option_data
	 * @param string     $input_type 'checkbox' or 'radio'
	 *
	 * @return string
	 */
	public static function render_single_checkbox( $cid, $option_index, stdClass $option_data, $input_type, $multiple_choices = false ) {

		$html = '';

		$field_id = 'form_field_' . $cid . '_' . $option_index;

		$attributes = array(
			'id'    => $field_id,
			'name'  => 'form_field_' . $cid . ( $multiple_choices ? '[]' : '' ),
			'type'  => $input_type,
			'value' => $option_index,
		);

		if ( isset( $option_data->required ) && $option_data->required ) {
			$attributes['required'] = 'required';
		}

		$label_tag_attributes = array(
			'for' => $field_id,
		);

		$label = ! empty( $option_data->label ) ? $option_data->label : '';

		if ( isset( $option_data->checked ) && $option_data->checked ) {
			$attributes['checked'] = 'checked';
		}

		// Give the theme a chance to alter the attributes for the input field
		$attributes = apply_filters( 'gm_form_checkbox_input_attrs', $attributes );
		$attributes = apply_filters( 'gm_form_input_attrs', $attributes );

		$label_tag_attributes = apply_filters( 'gm_form_text_label_attrs', $label_tag_attributes );
		$label_tag_attributes = apply_filters( 'gm_form_label_attrs', $label_tag_attributes );
		$label                = apply_filters( 'gm_form_text_label_text', $label );
		$label                = apply_filters( 'gm_form_label_text', $label );

		$html .= '<div>';

		$html .= '<label ';
		foreach ( $label_tag_attributes as $attribute => $value ) {
			$html .= wp_kses_data( $attribute ) . '="' . esc_attr( $value ) . '" ';
		}
		$html .= '>';

		$html .= '<input ';
		foreach ( $attributes as $attribute => $value ) {
			$html .= wp_kses_data( $attribute ) . '="' . esc_attr( $value ) . '" ';
		}
		$html .= '> ';

		$html .= $label . '</label>';

		if ( isset( $option_data->other ) && $option_data->other ) {

			$other_input_tag_attributes = array(
				'id'   => $field_id . '_value',
				'name' => $field_id . '_value',
				'type' => 'text',
			);

			$other_input_tag_attributes = apply_filters( 'gm_form_text_input_attrs', $other_input_tag_attributes );
			$other_input_tag_attributes = apply_filters( 'gm_form_input_attrs', $other_input_tag_attributes );

			$html .= '<input ';
			foreach ( $other_input_tag_attributes as $attribute => $value ) {
				$html .= wp_kses_data( $attribute ) . '="' . esc_attr( $value ) . '" ';
			}
			$html .= ' />';

		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Build an array of US states for use when rendering address fields
	 *
	 * @return array abbreviation => stdClass(), where value is an option for rendering by render_dropdown()
	 */
	public static function get_us_states() {

		static $state_data;

		if ( ! isset( $state_data ) ) {

			$state_data = array();

			$states = array(
				'AL' => 'Alabama',
				'AK' => 'Alaska',
				'AZ' => 'Arizona',
				'AR' => 'Arkansas',
				'CA' => 'California',
				'CO' => 'Colorado',
				'CT' => 'Connecticut',
				'DE' => 'Delaware',
				'FL' => 'Florida',
				'GA' => 'Georgia',
				'HI' => 'Hawaii',
				'ID' => 'Idaho',
				'IL' => 'Illinois',
				'IN' => 'Indiana',
				'IA' => 'Iowa',
				'KS' => 'Kansas',
				'KY' => 'Kentucky',
				'LA' => 'Louisiana',
				'ME' => 'Maine',
				'MD' => 'Maryland',
				'MA' => 'Massachusetts',
				'MI' => 'Michigan',
				'MN' => 'Minnesota',
				'MS' => 'Mississippi',
				'MO' => 'Missouri',
				'MT' => 'Montana',
				'NE' => 'Nebraska',
				'NV' => 'Nevada',
				'NH' => 'New Hampshire',
				'NJ' => 'New Jersey',
				'NM' => 'New Mexico',
				'NY' => 'New York',
				'NC' => 'North Carolina',
				'ND' => 'North Dakota',
				'OH' => 'Ohio',
				'OK' => 'Oklahoma',
				'OR' => 'Oregon',
				'PA' => 'Pennsylvania',
				'RI' => 'Rhode Island',
				'SC' => 'South Carolina',
				'SD' => 'South Dakota',
				'TN' => 'Tennessee',
				'TX' => 'Texas',
				'UT' => 'Utah',
				'VT' => 'Vermont',
				'VA' => 'Virginia',
				'WA' => 'Washington',
				'WV' => 'West Virginia',
				'WI' => 'Wisconsin',
				'WY' => 'Wyoming',
			);


			foreach ( $states as $abbreviation => $label ) {
				$state_data[ $abbreviation ]          = new stdClass();
				$state_data[ $abbreviation ]->label   = $label;
				$state_data[ $abbreviation ]->checked = false;
			}

		}

		return $state_data;

	}

	/**
	 * Set the appropriate attributes for character/word restrictions on a paragraph form field
	 *
	 * @param stdClass $field
	 * @param array    $textarea_tag_attributes
	 *
	 * @return array
	 */
	public static function paragraph_length_restriction_attributes( stdClass $field, array $textarea_tag_attributes ) {

		if ( isset( $field->field_options->min_max_length_units ) && 'words' === $field->field_options->min_max_length_units ) {

			if ( isset( $field->field_options->minlength ) ) {
				$textarea_tag_attributes['data-parsley-minwords'] = absint( $field->field_options->minlength );
			}

			if ( isset( $field->field_options->maxlength ) ) {
				$textarea_tag_attributes['data-parsley-maxwords'] = absint( $field->field_options->maxlength );
			}

		} else if ( ! isset( $field->field_options->min_max_length_units ) || 'characters' === $field->field_options->min_max_length_units ) {

			if ( isset( $field->field_options->minlength ) ) {
				$textarea_tag_attributes['minlength']              = absint( $field->field_options->minlength );
				$textarea_tag_attributes['data-parsley-minlength'] = absint( $field->field_options->minlength );
			}

			if ( isset( $field->field_options->maxlength ) ) {
				$textarea_tag_attributes['maxlength']              = absint( $field->field_options->maxlength );
				$textarea_tag_attributes['data-parsley-maxlength'] = absint( $field->field_options->maxlength );
			}

		}

		return $textarea_tag_attributes;

	}

	/**
	 * Set the appropriate attributes for paragraph form field size
	 *
	 * @param stdClass $field
	 * @param array    $textarea_tag_attributes
	 *
	 * @return array
	 * @throws InvalidArgumentException
	 */
	public static function paragraph_field_size_attributes( stdClass $field, array $textarea_tag_attributes ) {

		if ( isset( $field->field_options->size ) ) {

			if ( 'small' === $field->field_options->size ) {
				$textarea_tag_attributes['rows'] = self::TEXTAREA_SIZE_SMALL;

				return $textarea_tag_attributes;
			} else if ( 'medium' === $field->field_options->size ) {
				$textarea_tag_attributes['rows'] = self::TEXTAREA_SIZE_MEDIUM;

				return $textarea_tag_attributes;
			} else if ( 'large' === $field->field_options->size ) {
				$textarea_tag_attributes['rows'] = self::TEXTAREA_SIZE_LARGE;

				return $textarea_tag_attributes;
			} else {
				throw new InvalidArgumentException( sprintf( 'Field %d has an invalid size', $field->cid ) );
			}

		}

		return $textarea_tag_attributes;

	}

}
