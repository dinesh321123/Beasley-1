<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class GreaterMediaFormbuilderRender {

	const FORM_CLASS = 'contest_entry_form';

	const INPUT_SIZE_SMALL = '10';
	const INPUT_SIZE_MEDIUM = '25';
	const INPUT_SIZE_LARGE = '40';

	const TEXTAREA_SIZE_SMALL = '3';
	const TEXTAREA_SIZE_MEDIUM = '5';
	const TEXTAREA_SIZE_LARGE = '10';

	private static $_entries = array();

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

	public static function parse_entry( $contest_id, $entry_id ) {
		if ( isset( self::$_entries[ $contest_id ][ $entry_id ] ) ) {
			return self::$_entries[ $contest_id ][ $entry_id ];
		}

		if ( ! isset( self::$_entries[ $contest_id ] ) ) {
			self::$_entries[ $contest_id ] = array();
		}

		$form = get_post_meta( $contest_id, 'embedded_form', true );
		if ( empty( $form ) ) {
			return array();
		}

		if ( is_string( $form ) ) {
			$clean_form = trim( $form, '"' );
			$form = json_decode( $clean_form );
		}

		$contest_entry = get_post_meta( $entry_id, 'entry_reference', true );
		if ( empty( $contest_entry ) ) {
			return array();
		}

		$results = array();
		$contest_entry = @json_decode( $contest_entry, true );
		foreach ( $form as $field ) {
			if ( isset( $contest_entry[ $field->cid ] ) ) {
				if ( 'radio' == $field->field_type ) {
					$results[ $field->cid ] = array(
						'type'  => $field->field_type,
						'label' => $field->label,
						'value' => ! empty( $field->field_options->options[ $contest_entry[ $field->cid ] ] )
							? $field->field_options->options[ $contest_entry[ $field->cid ] ]->label
							: $contest_entry[ $field->cid ],
					);
				} elseif ( 'checkboxes' == $field->field_type && is_array( $contest_entry[ $field->cid ] ) ) {
					$values = array();
					foreach ( $contest_entry[ $field->cid ] as $value ) {
						$values[] = ! empty( $field->field_options->options[ $value ] )
							? $field->field_options->options[ $value ]->label
							: $value;
					}

					$results[ $field->cid ] = array(
						'type'  => $field->field_type,
						'label' => $field->label,
						'value' => $values,
					);
				} else {
					$results[ $field->cid ] = array(
						'type'  => $field->field_type,
						'label' => $field->label,
						'value' => $contest_entry[ $field->cid ],
					);
				}
			}
		}

		self::$_entries[ $contest_id ][ $entry_id ] = $results;

		return self::$_entries[ $contest_id ][ $entry_id ];
	}

	/**
	 * Render a form attached to a given post
	 *
	 * @param int $post_id Post ID
	 */
	public static function render( $post_id ) {

		$form = get_post_meta( $post_id, 'embedded_form', true );
		if ( empty( $form ) ) {
			return;
		}
		
		if ( is_string( $form ) ) {
			$clean_form = trim( $form, '"' );
			$form = json_decode( $clean_form );
		}

		$html = '<h3 class="contest__form--heading">Enter Here to Win</h3>';
		$html .= '<form method="post" enctype="multipart/form-data" class="' . esc_attr( self::FORM_CLASS ) . '" data-parsley-validate>';

		foreach ( $form as $field ) {

			$renderer_method = 'render_' . $field->field_type;

			// Make sure the field type has been implemented/is valid
			if ( method_exists( __CLASS__, $renderer_method ) ) {
				$html .= '<div class="contest__form--row">';
				$html .= wp_kses( self::$renderer_method( $post_id, $field ), self::allowed_tags() );
				$html .= '</div>';
			}

		}

		$submit_text = get_post_meta( $post_id, 'form-submitbutton', true );
		if ( empty( $submit_text ) ) {
			// If you change this string, be sure to get all the places it's used in this class
			$submit_text = __( 'Submit', 'greatermedia_contests' );
		}

		$html .= self::get_submit_button( $submit_text, null, null, true );

		$html .= '</form>';

		echo $html;

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

		$label_tag_attributes = array(
			'for' => $field_id,
			'class' => 'contest__form--label'
		);

		$label = ( isset( $field->label ) ) ? esc_html( $field->label ) : '';

		// Give the theme a chance to alter the attributes for the input field
		$label_tag_attributes = apply_filters( 'gm_form_text_label_attrs', $label_tag_attributes );
		$label_tag_attributes = apply_filters( 'gm_form_label_attrs', $label_tag_attributes );
		$label                = apply_filters( 'gm_form_text_label_text', $label );
		$label                = apply_filters( 'gm_form_label_text', $label );

		if ( ! empty( $label ) ) {

			if ( ! empty( $field->required ) && 'section_break' != $field->field_type ) {
				$label .= ' <abbr title="required">*</abbr>';
			}
			
			$html .= '<label ';

			foreach ( $label_tag_attributes as $attribute => $value ) {
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

		$html = '';

		$legend_tag_attributes = array();

		$label = ( isset( $field->label ) ) ? $field->label : '';

		// Give the theme a chance to alter the attributes for the input field
		$legend_tag_attributes = apply_filters( 'gm_form_text_label_attrs', $legend_tag_attributes );
		$legend_tag_attributes = apply_filters( 'gm_form_label_attrs', $legend_tag_attributes );
		$label                 = apply_filters( 'gm_form_text_label_text', $label );
		$label                 = apply_filters( 'gm_form_label_text', $label );

		if ( ! empty( $label ) ) {

			$html .= '<legend ';

			foreach ( $legend_tag_attributes as $attribute => $value ) {
				$html .= wp_kses_data( $attribute ) . '="' . esc_attr( $value ) . '" ';
			}

			$html .= '>' . wp_kses_data( $label ) . '</legend>';

		}

		return $html;

	}

	public static function render_description( stdClass $field ) {

		$html = '';

		$description = ( isset( $field->field_options->description ) ) ? $field->field_options->description : '';

		$description_tag_attributes = array();

		// Give the theme a chance to alter the attributes for the description
		$description_tag_attributes = apply_filters( 'gm_form_text_description_attrs', $description_tag_attributes );
		$description_tag_attributes = apply_filters( 'gm_form_input_description_attrs', $description_tag_attributes );
		$description                = apply_filters( 'gm_form_text_description_text', $description );
		$description                = apply_filters( 'gm_form_description_text', $description );

		if ( ! empty( $description ) ) {

			$html .= '<p ';

			foreach ( $description_tag_attributes as $attribute => $value ) {
				$html .= wp_kses_data( $attribute ) . '="' . esc_attr( $value ) . '" ';
			}

			$html .= ' >' . wp_kses_data( $description ) . '</p>';
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
			foreach ( $field->field_options->options as $field_option_index => $field_option_data ) {
				$html .= self::render_single_checkbox( $field->cid, $field_option_index, $field_option_data, $input_type, $multiple_choices );
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

		$html = '';

		$html .= self::render_label( $field );

		$field_id = 'form_field_' . $field->cid;

		$input_tag_attributes = array_merge( $special_attributes, array(
			'id'    => $field_id,
			'name'  => $field_id,
			'type'  => $type,
		) );

		if ( isset( $field->required ) && $field->required ) {
			$input_tag_attributes['required'] = 'required';
		}

		// Give the theme a chance to alter the attributes for the input field
		$input_tag_attributes = apply_filters( 'gm_form_' . $type . '_input_attrs', $input_tag_attributes );
		$input_tag_attributes = apply_filters( 'gm_form_input_attrs', $input_tag_attributes );

		$input_tag_html = '<input ';
		foreach ( $input_tag_attributes as $attribute => $value ) {
			$input_tag_html .= wp_kses_data( $attribute ) . '="' . esc_attr( $value ) . '" ';
		}
		$input_tag_html .= ' />';

		// Call filters in case certain input types need extra markup (like 'units' following a number field)
		$input_tag_html = apply_filters( 'gm_form_' . $type . '_input_tag', $input_tag_html, $post_id, $field );
		$input_tag_html = apply_filters( 'gm_form_input_tag', $input_tag_html, $post_id, $field );
		$html .= $input_tag_html;

		$html .= self::render_description( $field );

		return $html;

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
	public static function get_submit_button( $text = null, $type = 'primary large', $name = 'submit', $wrap = true, $other_attributes = null ) {
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

		$button = '<button type="submit" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '" ' . $attributes . '><i class="fa fa-spinner fa-spin"></i> ' . esc_attr( $text ) . '</button>';

		if ( $wrap ) {
			$button = '<p class="submit">' . $button . '</p>';
		}

		return $button;
	}

	/**
	 * @param string     $cid
	 * @param int|string $field_option_index
	 * @param stdClass   $field_option_data
	 * @param string     $input_type 'checkbox' or 'radio'
	 *
	 * @return string
	 */
	public static function render_single_checkbox( $cid, $field_option_index, stdClass $field_option_data, $input_type, $multiple_choices = false ) {

		$html = '';

		$field_id = 'form_field_' . $cid . '_' . $field_option_index;

		$input_tag_attributes = array(
			'id'    => $field_id,
			'name'  => 'form_field_' . $cid . ( $multiple_choices ? '[]' : '' ),
			'type'  => $input_type,
			'value' => $field_option_index,
		);

		$label_tag_attributes = array(
			'for' => $field_id,
		);

		$label = ! empty( $field_option_data->label ) ? $field_option_data->label : '';

		if ( isset( $field_option_data->checked ) && $field_option_data->checked ) {
			$input_tag_attributes['checked'] = 'checked';
		}

		// Give the theme a chance to alter the attributes for the input field
		$input_tag_attributes = apply_filters( 'gm_form_checkbox_input_attrs', $input_tag_attributes );
		$input_tag_attributes = apply_filters( 'gm_form_input_attrs', $input_tag_attributes );

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
		foreach ( $input_tag_attributes as $attribute => $value ) {
			$html .= wp_kses_data( $attribute ) . '="' . esc_attr( $value ) . '" ';
		}
		$html .= '> ';

		$html .= $label . '</label>';

		if ( isset( $field_option_data->other ) && $field_option_data->other ) {

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
