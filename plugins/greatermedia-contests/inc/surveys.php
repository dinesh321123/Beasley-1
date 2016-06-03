<?php

// action hooks
add_action( 'template_redirect', 'gmr_surveys_process_action' );
add_action( 'wp_enqueue_scripts', 'gmr_surveys_enqueue_scripts' );

add_action( 'gmr_survey_load', 'gmr_surveys_render_form' );
add_action( 'gmr_survey_submit', 'gmr_surveys_process_form_submission' );

// filter hooks
add_filter( 'gmr-homepage-curation-post-types', 'gmr_survey_register_curration_post_type' );
add_filter( 'gmr-show-curation-post-types', 'gmr_survey_register_curration_post_type' );

/**
 * Registers survey post type in the curration types list.
 *
 * @filter gmr-homepage-curation-post-types
 * @filter gmr-show-curation-post-types
 * @param array $types Array of already registered types.
 * @return array Extended array of post types.
 */
function gmr_survey_register_curration_post_type( $types ) {
	$types[] = GMR_SURVEY_CPT;
	return $types;
}

/**
 * Processes survey actions triggered from front end.
 *
 * @action template_redirect
 */
function gmr_surveys_process_action() {
	// do nothing if it is a regular request
	if ( ! is_singular( GMR_SURVEY_CPT ) ) {
		return;
	}

	$action = get_query_var( 'action' );
	if ( ! empty( $action ) ) {
		// disable batcache if it is activated
		if ( function_exists( 'batcache_cancel' ) ) {
			batcache_cancel();
		}

		// disble HTTP cache
		nocache_headers();


		// do contest action
		do_action( "gmr_survey_{$action}" );
		exit;
	}
}

/**
 * Enqueues survey script.
 *
 * @action wp_enqueue_scripts
 */
function gmr_surveys_enqueue_scripts() {
	$base_path = trailingslashit( GREATER_MEDIA_CONTESTS_URL );
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_script( 'greatermedia-surveys', "{$base_path}js/surveys{$postfix}.js", array( 'jquery' ), GREATER_MEDIA_CONTESTS_VERSION, true );
}

/**
 * Displays survey container attributes required for proper work of survey JS.
 *
 * @param WP_Post|int $post The contest id or object.
 */
function gmr_survey_container_attributes( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return;
	}

	$permalink = untrailingslashit( get_permalink( $post->ID ) );

	$endpoints = array(
		'load' => "{$permalink}/action/load/",
	);

	foreach ( $endpoints as $attribute => $value ) {
		echo sprintf( ' data-%s="%s"', $attribute, esc_url( $value ) );
	}
}

/**
 * Renders survey form.
 *
 * @action gmr_survey_load
 */
function gmr_surveys_render_form() {
	// check if user has to be logged in
	if ( function_exists( 'is_gigya_user_logged_in' ) && ! is_gigya_user_logged_in() ) {
		wp_send_json_error( array( 'restriction' => 'signin' ) );
	}

	$survey_id = get_the_ID();

	// check if user already submitted survey response
	if ( function_exists( 'has_user_entered_survey' ) && has_user_entered_survey( $survey_id ) ) {
		wp_send_json_error( array( 'restriction' => 'one-entry' ) );
	}

	$form = get_post_meta( $survey_id, 'survey_embedded_form', true );
	if ( is_string( $form ) ) {
		$form = json_decode( trim( $form, '"' ) );
	}

	// render the form
	wp_send_json_success( array(
		'html' => GreaterMediaFormbuilderRender::render( $survey_id, $form, false ),
	) );
}

/**
 * Verifies form submission.
 */
function gmr_survey_verify_form_submission( $form ) {
	_deprecated_function( 'gmr_survey_verify_form_submission', '1.1.3', 'gmr_verify_form_submission' );
	gmr_verify_form_submission( $form );
}

/**
 * Processes survey submission.
 *
 * @action gmr_survey_submit
 */
function gmr_surveys_process_form_submission() {
	if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
		return;
	}

	$survey_id = get_the_ID();
	$form = @json_decode( get_post_meta( $survey_id, 'survey_embedded_form', true ) );
	gmr_verify_form_submission( $form );

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';

	$submitted_values = $submitted_files  = array();

	foreach ( $form as $field ) {
		$post_array_key = 'form_field_' . $field->cid;
		if ( isset( $_POST[ $post_array_key ] ) ) {
			if ( is_scalar( $_POST[ $post_array_key ] ) ) {
				$submitted_values[ $field->cid ] = sanitize_text_field( $_POST[ $post_array_key ] );
			} else if ( is_array( $_POST[ $post_array_key ] ) ) {
				$submitted_values[ $field->cid ] = array_map( 'sanitize_text_field', $_POST[ $post_array_key ] );
			}
		}
	}

	list( $entrant_reference, $entrant_name ) = gmr_surveys_get_gigya_entrant_id_and_name();

	$entry = GreaterMediaSurveyEntry::create_for_data( $survey_id, $entrant_name, $entrant_reference, GreaterMediaContestEntry::ENTRY_SOURCE_EMBEDDED_FORM, json_encode( $submitted_values ) );
	$entry->save();

	do_action( 'greatermedia_survey_entry_save', $entry );

	echo '<html>';
		echo '<head></head>';
		echo '<body>';
			$thankyou = get_post_meta( $survey_id, 'form-thankyou', true );
			$thankyou = $thankyou ? $thankyou : "Thanks for your response!";
			echo wpautop( $thankyou );

			$fields = GreaterMediaFormbuilderRender::parse_entry( $survey_id, $entry->post->ID, $form );
			if ( ! empty( $fields ) ) :
				?><h4 class="contest__submission--entries-title">Here is your response:</h4>
				<dl class="contest__submission--entries">
					<?php foreach ( $fields as $field ) : ?>
						<?php if ( 'file' != $field['type'] ) : ?>
							<dt>
								<?php echo esc_html( $field['label'] ); ?>
							</dt>
							<dd>
								<?php echo is_array( $field['value'] ) ? esc_html( implode( ', ', $field['value'] ) ) : esc_html( $field['value'] ); ?>
							</dd>
						<?php endif; ?>
					<?php endforeach; ?>
				</dl><?php
			endif;
		echo '</body>';
	echo '</html>';
}

/**
 * Get Gigya ID and build name, from Gigya session data if available
 *
 * @return array
 */
function gmr_surveys_get_gigya_entrant_id_and_name() {
	$entrant_name = 'Anonymous Listener';
	$entrant_reference = null;

	if ( class_exists( '\GreaterMedia\Gigya\GigyaSession' ) ) {
		$gigya_session = \GreaterMedia\Gigya\GigyaSession::get_instance();
		$gigya_id = $gigya_session->get_user_id();
		if ( ! empty( $gigya_id ) ) {
			$entrant_reference = $gigya_id;
			$entrant_name      = $gigya_session->get_key( 'firstName' ) . ' ' . $gigya_session->get_key( 'lastName' );
		}
	}

	return array( $entrant_reference, $entrant_name );
}
