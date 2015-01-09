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

	public static function init() {
		self::$post = new WP_Post( new stdClass() );
		self::$post->post_type = GMR_SURVEY_RESPONSE_CPT;

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'wp_enqueue_scripts' ) );
	}

	public static function wp_enqueue_scripts() {
		$base_path = trailingslashit( GREATER_MEDIA_CONTESTS_URL );
		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_script( 'greatermedia-surveys', "{$base_path}js/surveys{$postfix}.js", array( 'jquery' ), GREATER_MEDIA_CONTESTS_VERSION, true );
	}

	public static function render( $post_id ) {

		$form = get_post_meta( $post_id, 'survey_embedded_form', true );
		if ( is_string( $form ) ) {
			$clean_form = trim( $form, '"' );
			$form = json_decode( $clean_form );
		}

		if ( null === $form || ! is_array( $form ) ) {
			return;
		}

		$html = '';

		$title = get_post_meta( $post_id, 'form-title', true );
		if ( ! empty( $title ) ) {
			$html .= '<h3 class="contest__form--heading">' . esc_html( $title ) . '</h3>';
		}
		
		$html .= '<form method="post" enctype="multipart/form-data" data-parsley-validate>';

		foreach ( $form as $field ) {

			$renderer_method = 'render_' . $field->field_type;

			// Make sure the field type has been implemented/is valid
			if ( method_exists( 'GreaterMediaFormbuilderRender', $renderer_method ) ) {
				$html .= '<div class="contest__form--row">';
				$html .= wp_kses( GreaterMediaFormbuilderRender::$renderer_method( $post_id, $field ), GreaterMediaFormbuilderRender::allowed_tags() );
				$html .= '</div>';
			}

		}

		$html .= GreaterMediaFormbuilderRender::get_submit_button( 'Enter', null, null, true );

		$html .= '</form>';

		return $html;
	}

}

GreaterMediaSurveyFormRender::init();
