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

	private function __construct() {
		// Use the public static methods. Don't instantiate this class directly.
	}

	/**
	 * Register WordPress actions & filters
	 */
	public static function register_actions() {

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			// Register AJAX handlers
			add_action( 'wp_ajax_enter_contest', array( __CLASS__, 'process_form_submission' ) );
			add_action( 'wp_ajax_nopriv_enter_contest', array( __CLASS__, 'process_form_submission' ) );
		} else {
			// Register a generic POST handler for if/when there's a fallback from the AJAX method
			add_action( 'wp', array( __CLASS__, 'process_form_submission' ) );
		}

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'wp_enqueue_scripts' ) );

	}

	/**
	 * Enqueue scripts & styles
	 * Implements wp_enqueue_scripts action
	 */
	public static function wp_enqueue_scripts() {

		wp_enqueue_script( 'parsleyjs' );
		wp_enqueue_style( 'parsleyjs' );
		wp_enqueue_script( 'parsleyjs-words' );

		wp_enqueue_script( 'greatermedia-contests', trailingslashit( GREATER_MEDIA_CONTESTS_URL ) . 'js/greatermedia-contests.js', array( 'jquery' ), false, true );
		$settings = array(
			'form_class' => self::FORM_CLASS,
			'ajax_url'   => admin_url( 'admin-ajax.php' ),
		);
		wp_localize_script( 'greatermedia-contests', 'GreaterMediaContests', $settings );

		wp_enqueue_script( 'datetimepicker' );
		wp_enqueue_style( 'datetimepicker' );
		wp_enqueue_style( 'greatermedia-contests', trailingslashit( GREATER_MEDIA_CONTESTS_URL ) . 'css/greatermedia-contests.css' );

	}

	/**
	 * @uses do_action
	 * @uses wp_send_json_success
	 */
	public static function process_form_submission() {

		try {

			if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
				throw new InvalidArgumentException( 'Request should be a POST' );
			}

			if ( ! isset( $_POST['contest_id'] ) ) {
				throw new InvalidArgumentException( 'Missing contest_id' );
			}

			$contest_id = absint( $_POST['contest_id'] );
			if ( empty( $contest_id ) ) {
				throw new InvalidArgumentException( 'Invalid contest_id' );
			}

			$contest = get_post( $contest_id );
			if ( empty( $contest ) ) {
				throw new InvalidArgumentException( 'No contest found with given ID' );
			}

			if ( 'contest' !== $contest->post_type ) {
				throw new InvalidArgumentException( 'contest_id does not reference a contest' );
			}

			list( $entrant_reference, $entrant_name ) = self::entrant_id_and_name();


			// Pretty sure this is our form submission at this point
			$form = json_decode( get_post_meta( $contest_id, 'embedded_form', true ) );
			if ( empty( $form ) ) {
				throw new InvalidArgumentException( 'Contest is missing an embedded form' );
			}

			$submitted_values = array();
			$submitted_files  = array(
				'images' => array(),
				'other'  => array(),
			);
			foreach ( $form as $field ) {

				$post_array_key = 'form_field_' . $field->cid;

				if ( 'file' === $field->field_type ) {

					if ( isset( $_FILES[ $post_array_key ] ) ) {
						$file_type_index                                        = self::file_type_index( $_FILES[ $post_array_key ]['tmp_name'] );
						$submitted_files[ $file_type_index ][ $post_array_key ] = $_FILES[ $post_array_key ];
					}

				} else if ( isset( $_POST[ $post_array_key ] ) ) {

					if ( is_scalar( $_POST[ $post_array_key ] ) ) {
						$submitted_values[ $field->cid ] = sanitize_text_field( $_POST[ $post_array_key ] );
					} else if ( is_array( $_POST[ $post_array_key ] ) ) {
						$submitted_values[ $field->cid ] = array_map( 'sanitize_text_field', $_POST[ $post_array_key ] );
					}

				}

			}

			$entry = GreaterMediaContestEntryEmbeddedForm::create_for_data(
				$contest_id,
				$entrant_name,
				$entrant_reference,
				GreaterMediaContestEntry::ENTRY_SOURCE_EMBEDDED_FORM,
				json_encode( $submitted_values )
			);

			$entry->save();

			self::handle_submitted_files( $submitted_files, $entry );

			do_action( 'greatermedia_contest_entry_save', $entry );

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				$response          = new stdClass();
				$response->message = get_post_meta( $contest_id, 'form-thankyou', true );
				wp_send_json_success( $response );
				exit();
			} else {

				/**
				 * If we've fallen back to an old-school non-AJAX POST,
				 * use a constant to communicate status to the rendering function
				 * since this class isn't meant to be instantiated.
				 */
				define( 'CONTEST_' . $contest_id . '_SUCCESS', true );

			}

		} catch ( InvalidArgumentException $e ) {

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				$response          = new stdClass();
				$response->message = $e->getMessage();
				wp_send_json_error( $response );
				exit();
			} else {
				return;
			}

		}

	}

	/**
	 * Retrieve a custom list of HTML tags & attributes we're allowing in a rendered form
	 * @return array valid tags
	 */
	protected static function allowed_tags() {

		static $tags;
		if ( ! isset( $tags ) ) {

			$tags = array();

			$tags['fieldset'] = array();
			$tags['hr']       = array();

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

	/**
	 * Render a form attached to a given post
	 *
	 * @param int    $post_id Post ID
	 * @param string $form    JSON-encoded form data
	 *
	 * @uses render_text
	 */
	public static function render( $post_id, $form ) {

		$html = '';

		if ( ! is_numeric( $post_id ) ) {
			throw new InvalidArgumentException( '$post_id must be an integer post ID' );
		}

		if ( is_string( $form ) ) {
			$clean_form = trim( $form, '"' );
			$form       = json_decode( $clean_form );
		}

		if ( null === $form ) {
			throw new InvalidArgumentException( '$form parameter is invalid' );
		}

		if ( ! is_array( $form ) ) {
			throw new InvalidArgumentException( '$form should be a JSON string or an Object' );
		}

		if ( defined( 'CONTEST_' . $post_id . '_SUCCESS' ) && 'CONTEST_' . $post_id . '_SUCCESS' ) {

			/**
			 * Fallback to rendering the thank-you message on the server side.
			 * This should be OK since a POST won't be cached.
			 */
			$html .= '<p>' .
			         get_post_meta( $post_id, 'form-thankyou', true ) .
			         '</p>';

		} else {

			$html .= '<form action="" method="post" enctype="multipart/form-data" class="' . esc_attr( self::FORM_CLASS ) . '" data-parsley-validate>' .
			         '<input type="hidden" name="action" value="enter_contest" />' .
			         '<input type="hidden" name="contest_id" value="' . absint( $post_id ) . '" />';

			foreach ( $form as $field ) {

				$renderer_method = 'render_' . $field->field_type;

				// Make sure the field type has been implemented/is valid
				if ( ! method_exists( __CLASS__, $renderer_method ) ) {
					throw new InvalidArgumentException( sprintf( 'Form field %s has unimplemented field type %s', wp_kses_data( $field->cid ), wp_kses_data( $field->field_type ) ) );
				}

				$html .= '<div class="contest__form--row">';
				$html .= wp_kses( self::$renderer_method( $post_id, $field ), self::allowed_tags() );
				$html .= '</div>';

			}

			$submit_text = get_post_meta( $post_id, 'form-submitbutton', true );
			if ( empty( $submit_text ) ) {
				// If you change this string, be sure to get all the places it's used in this class
				$submit_text = __( 'Submit', 'greatermedia_contests' );
			}

			$html .= self::get_submit_button( $submit_text, null, null, true );

			$html .= '</form>';

		}

		echo $html;

	}

	/**
	 * Render a field's label tag
	 *
	 * @param stdClass $field
	 *
	 * @return string html
	 */
	protected static function render_label( stdClass $field ) {

		$html = '';

		$field_id = 'form_field_' . $field->cid;

		$label_tag_attributes = array(
			'for' => $field_id,
			'class' => 'contest__form--label'
		);

		$label = ( isset( $field->label ) ) ? $field->label : '';

		// Give the theme a chance to alter the attributes for the input field
		$label_tag_attributes = apply_filters( 'gm_form_text_label_attrs', $label_tag_attributes );
		$label_tag_attributes = apply_filters( 'gm_form_label_attrs', $label_tag_attributes );
		$label                = apply_filters( 'gm_form_text_label_text', $label );
		$label                = apply_filters( 'gm_form_label_text', $label );

		if ( ! empty( $label ) ) {

			$html .= '<label ';

			foreach ( $label_tag_attributes as $attribute => $value ) {
				$html .= wp_kses_data( $attribute ) . '="' . esc_attr( $value ) . '" ';
			}

			$html .= '>' . wp_kses_data( $label ) . '</label>';

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
	protected static function render_legend( stdClass $field ) {

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

	protected static function render_description( stdClass $field ) {

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
	protected static function render_text( $post_id, stdClass $field ) {

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
	protected static function render_paragraph( $post_id, stdClass $field ) {

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
		$html .= ' >';
		$html .= '</textarea>';

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
	protected static function render_dropdown( $post_id, stdClass $field ) {

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

		foreach ( $field->field_options->options as $option_index => $option_data ) {
			$html .= '<option value="' . esc_attr( $option_data->label ) . '" ' . selected( 1, $option_data->checked, false ) . '>' . wp_kses_data( $option_data->label ) . '</option>';
		}

		$html .= '</select>';

		$html .= self::render_description( $field );

		return $html;

	}

	protected static function render_radio( $post_id, stdClass $field ) {
		return self::render_checkboxes( $post_id, $field, 'radio' );
	}

	protected static function render_checkboxes( $post_id, stdClass $field, $input_type = 'checkbox' ) {

		$html = '';

		$html .= '<fieldset>';

		$html .= self::render_legend( $field );

		if ( isset( $field->field_options->options ) && is_array( $field->field_options->options ) ) {
			foreach ( $field->field_options->options as $field_option_index => $field_option_data ) {

				$html .= self::render_single_checkbox( $field->cid, $field_option_index, $field_option_data, $input_type );

			}
		}

		if ( isset( $field->field_options->include_other_option ) && true == $field->field_options->include_other_option ) {

			$other_option_data = new stdClass();

			$other_option_data->label   = __( 'Other', 'greatermedia_contests' );
			$other_option_data->checked = false;
			$other_option_data->other   = true;

			$html .= self::render_single_checkbox( $field->cid, 'other', $other_option_data, $input_type );

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
	protected static function render_date( $post_id, stdClass $field ) {
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
	protected static function render_time( $post_id, stdClass $field ) {
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
	protected static function render_website( $post_id, stdClass $field ) {
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
	protected static function render_email( $post_id, stdClass $field ) {
		return self::render_input_tag( 'email', $post_id, $field );
	}

	protected static function render_price( $post_id, stdClass $field ) {

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
	protected static function render_address( $post_id, stdClass $field ) {

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
	protected static function render_file( $post_id, stdClass $field ) {

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
	protected static function render_input_tag( $type = 'text', $post_id, stdClass $field, Array $special_attributes = null ) {

		if ( null === $special_attributes ) {
			$special_attributes = array();
		}

		$html = '';

		$html .= self::render_label( $field );

		$field_id = 'form_field_' . $field->cid;

		$input_tag_attributes = array_merge( $special_attributes, array(
			'id'   => $field_id,
			'name' => $field_id,
			'type' => $type,
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
	protected static function get_submit_button( $text = null, $type = 'primary large', $name = 'submit', $wrap = true, $other_attributes = null ) {
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

		$button = '<input type="submit" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" class="' . esc_attr( $class );
		$button .= '" value="' . esc_attr( $text ) . '" ' . $attributes . ' />';

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
	protected static function render_single_checkbox( $cid, $field_option_index, stdClass $field_option_data, $input_type ) {

		if ( 'checkbox' !== $input_type && 'radio' !== $input_type ) {
			throw new InvalidArgumentException( 'Input type must be checkbox or radio' );
		}

		$html = '';

		$field_id = 'form_field_' . $cid . '_' . $field_option_index;

		$input_tag_attributes = array(
			'id'    => $field_id,
			'name'  => 'form_field_' . $cid . '[]',
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

		$html .= '<input ';
		foreach ( $input_tag_attributes as $attribute => $value ) {
			$html .= wp_kses_data( $attribute ) . '="' . esc_attr( $value ) . '" ';
		}
		$html .= ' />';

		$html .= '<label ';
		foreach ( $label_tag_attributes as $attribute => $value ) {
			$html .= wp_kses_data( $attribute ) . '="' . esc_attr( $value ) . '" ';
		}
		$html .= '>' . $label . '</label>';

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
	protected static function get_us_states() {

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
	 * Get Gigya ID and build name, from Gigya session data if available
	 *
	 * @return array
	 */
	protected static function entrant_id_and_name() {

		if ( class_exists( 'GreaterMedia\Gigya\GigyaSession' ) ) {

			$gigya_session = \GreaterMedia\Gigya\GigyaSession::get_instance();
			$gigya_id      = $gigya_session->get_user_id();
			if ( ! empty( $gigya_id ) ) {

				$entrant_reference = $gigya_id;
				$entrant_name      = $gigya_session->get_key( 'firstName' ) . ' ' . $gigya_session->get_key( 'lastName' );

			} else {

				$entrant_name      = 'Anonymous Listener';
				$entrant_reference = null;

			}

		} else {

			$entrant_name      = 'Anonymous Listener';
			$entrant_reference = null;

		}

		return array( $entrant_reference, $entrant_name );

	}

	/**
	 * Set the appropriate attributes for character/word restrictions on a paragraph form field
	 *
	 * @param stdClass $field
	 * @param array    $textarea_tag_attributes
	 *
	 * @return array
	 */
	protected static function paragraph_length_restriction_attributes( stdClass $field, array $textarea_tag_attributes ) {

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
	protected static function paragraph_field_size_attributes( stdClass $field, array $textarea_tag_attributes ) {

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

	/**
	 * Identify what type of file an upload is so it can be handled appropriately
	 *
	 * @param $filename path to filename
	 *
	 * @return string 'images'|'other'
	 */
	protected static function file_type_index( $filename ) {

		if ( file_is_valid_image( $filename ) ) {
			return 'images';
		} else {
			return 'other';
		}

	}

	/**
	 * @param array                    $submitted_files
	 * @param GreaterMediaContestEntry $entry
	 *
	 * @return GreaterMediaUserGeneratedContent|null
	 */
	protected static function handle_submitted_files( array $submitted_files, GreaterMediaContestEntry $entry ) {

		/**
		 * Ignoring the "other" files per GMR-343
		 * "There's no reason for Contest or Survey upload fields to allow any filetypes other than images. Aside
		 * from security considerations, it also becomes much more complex to manage user generated content if it's
		 * anything beside photos."
		 */
		if ( empty( $submitted_files['images'] ) ) {

			// No need to create UGC
			return null;

		} elseif ( 1 === count( $submitted_files['images'] ) ) {

			// Single image. Create a GreaterMediaUserGeneratedImage.
			$ugc                    = GreaterMediaUserGeneratedContent::for_data_type( 'image' );
			$ugc->post->post_parent = $entry->post_id();
			$ugc_post_id            = $ugc->save();

			$upload_field = array_keys( $submitted_files['images'] )[0];
			$upload_data  = array_values( $submitted_files['images'] )[0];

			$attachment_id = media_handle_upload(
				$upload_field,
				$ugc_post_id
			);

			$ugc->post->post_content = wp_get_attachment_image( $attachment_id, 'full' );

			$ugc->save();

		} else {

			// Multiple images. Create a GreaterMediaUserGeneratedGallery.
			$ugc                    = GreaterMediaUserGeneratedContent::for_data_type( 'gallery' );
			$ugc->post->post_parent = $entry->post_id();
			$ugc_post_id            = $ugc->save();

			$attachment_ids = array();
			foreach($submitted_files['images'] as $upload_field => $upload_data) {

				$attachment_ids[] = media_handle_upload(
					$upload_field,
					$ugc_post_id
				);
			}

			$ugc->post->post_content = '[gallery ids="' . implode( ',', $attachment_ids ) . '"]';

			$ugc->save();

		}

		return $ugc;

	}

}

GreaterMediaFormbuilderRender::register_actions();
