<?php
/**
 * Created by Eduard
 * Date: 18.12.2014 4:01
 */

if ( ! defined( 'WPINC' ) ) {
	die( "Please don't try to access this file directly." );
}

class GreaterMediaSurveyFormRender {

	public static $post;
	const FORM_CLASS = 'survey_entry_form';

	public static function init() {
		self::$post            = new WP_Post( new stdClass() );
		self::$post            = 'survey_response';
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			// Register AJAX handlers
			add_action( 'wp_ajax_enter_survey', array( __CLASS__, 'process_form_submission' ) );
			add_action( 'wp_ajax_nopriv_enter_survey', array( __CLASS__, 'process_form_submission' ) );
		} else {
			// Register a generic POST handler for if/when there's a fallback from the AJAX method
			add_action( 'wp', array( __CLASS__, 'process_form_submission' ) );
		}

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'wp_enqueue_scripts' ) );
	}

	public static function wp_enqueue_scripts() {
		wp_enqueue_script( 'greatermedia-surveys', trailingslashit( GREATER_MEDIA_CONTESTS_URL ) . 'js/greater_media_surveys.js', array( 'jquery' ), false, true );
		$settings = array(
			'form_class' => self::FORM_CLASS,
			'ajax_url'   => admin_url( 'admin-ajax.php' ),
		);
		wp_localize_script( 'greatermedia-surveys', 'GreaterMediaSurveys', $settings );
	}

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

		if ( defined( 'SURVEY_' . $post_id . '_SUCCESS' ) && 'SURVEY_' . $post_id . '_SUCCESS' ) {

			/**
			 * Fallback to rendering the thank-you message on the server side.
			 * This should be OK since a POST won't be cached.
			 */
			$html .= '<p>' .
			         get_post_meta( $post_id, 'form-thankyou', true ) .
			         '</p>';

		} else {

			$html .= '<form action="" method="post" enctype="multipart/form-data" data-parsley-validate class="' . esc_attr( self::FORM_CLASS ) . '">'
			         . '<input type="hidden" name="action" value="enter_survey" />'
			         . '<input type="hidden" name="survey_id" value="' . absint( $post_id ) . '" />';

			foreach ( $form as $field ) {

				$renderer_method = 'render_' . $field->field_type;

				// Make sure the field type has been implemented/is valid
				if ( ! method_exists( 'GreaterMediaFormbuilderRender', $renderer_method ) ) {
					throw new InvalidArgumentException( sprintf( 'Form field %s has unimplemented field type %s', wp_kses_data( $field->cid ), wp_kses_data( $field->field_type ) ) );
				}

				$html .= wp_kses( GreaterMediaFormbuilderRender::$renderer_method( $post_id, $field ), GreaterMediaFormbuilderRender::allowed_tags() );

			}

			$html .= GreaterMediaFormbuilderRender::get_submit_button( 'Enter', null, null, true );

			$html .= '</form>';

		}

		echo $html;
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

			if ( !isset( $_POST['survey_id'] ) ) {
				throw new InvalidArgumentException( 'Missing survey_id' );
			}

			$survey_id = absint( $_POST['survey_id'] );
			if ( empty( $survey_id ) ) {
				throw new InvalidArgumentException( 'Invalid survey_id' );
			}

			$survey = get_post( $survey_id );
			if ( empty( $survey ) ) {
				throw new InvalidArgumentException( 'No survey found with given ID' );
			}

			if ( 'survey' !== $survey->post_type ) {
				throw new InvalidArgumentException( 'survey_id does not reference a contest' );
			}

			list( $entrant_reference, $entrant_name ) = GreaterMediaFormbuilderRender::entrant_id_and_name();

			// Pretty sure this is our form submission at this point
			$form = json_decode( get_post_meta( $survey_id, 'survey_embedded_form', true ) );

			if ( empty( $form ) ) {
				throw new InvalidArgumentException( 'Survey is missing an embedded form');
			}

			$submitted_values = array();

			foreach ( $form as $field ) {

				$post_array_key = 'form_field_' . $field->cid;

				if ( 'file' === $field->field_type ) {

					if ( isset( $_FILES[ $post_array_key ] ) ) {
						$file_type_index = GreaterMediaFormbuilderRender::file_type_index( $_FILES[ $post_array_key ]['tmp_name'] );
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

			$entry = GreaterMediaSurveyEntry::create_for_data(
				$survey_id,
				$entrant_name,
				$entrant_reference,
				GreaterMediaContestEntry::ENTRY_SOURCE_EMBEDDED_FORM,
				json_encode( $submitted_values )
			);

			$entry->save( $entry );

			do_action( 'greatermedia_survey_entry_save', $entry );

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				$response          = new stdClass();
				$response->message = get_post_meta( $survey_id, 'form-thankyou', true );
				wp_send_json_success( $response );
				exit();
			} else {

				/**
				 * If we've fallen back to an old-school non-AJAX POST,
				 * use a constant to communicate status to the rendering function
				 * since this class isn't meant to be instantiated.
				 */
				define( 'SURVEY_' . $survey_id . '_SUCCESS', true );

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

	/*public static function save( $entry ) {

		$post_id = wp_update_post( self::$post, true );

		update_post_meta( $post_id, 'entrant_name', $entry->entrant_name );
		update_post_meta( $post_id, 'entrant_reference', $entry->entrant_reference );
		update_post_meta( $post_id, 'entry_source', $entry->entry_source );
		update_post_meta( $post_id, 'entry_reference', $entry->entry_reference );
	}

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
	}*/
}

GreaterMediaSurveyFormRender::init();