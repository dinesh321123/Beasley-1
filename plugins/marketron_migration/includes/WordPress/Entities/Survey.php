<?php

namespace WordPress\Entities;

class Survey extends Post {

	public $survey_forms = array();

	function get_post_type() {
		return 'survey';
	}

	function add( &$fields ) {
		$marketron_id              = $fields['marketron_id'];
		$survey_title              = $fields['survey_title'];
		$survey_content            = $fields['survey_content'];
		$survey_excerpt            = $fields['survey_excerpt'];
		$survey_entries            = $fields['survey_entries'];
		$survey_completion_message = $fields['survey_completion_message'];
		$survey_form               = $fields['survey_form'];

		$meta                         = array();
		$meta['form-title']           = $survey_title;
		$meta['form-thankyou']        = $survey_completion_message;

		if ( ! empty( $survey_form ) ) {
			$meta['survey_embedded_form'] = json_encode( $survey_form );
		} else {
			\WP_CLI::error( 'Survey without form: ' );
		}

		$fields['post_title']   = $survey_title;
		$fields['post_content'] = $survey_content;
		$fields['post_excerpt'] = $survey_excerpt;
		$fields['postmeta']     = $meta;

		$fields = parent::add( $fields );
		$survey_id = $fields['ID'];

		$this->survey_forms[ $marketron_id ] = $survey_form;

		if ( ! empty( $survey_entries ) ) {
			$this->import_survey_entries( $survey_id, $survey_entries );
		}

		return $fields;
	}

	function get_survey_form( $form_id ) {
		if ( array_key_exists( $form_id, $this->survey_forms ) ) {
			return $this->survey_forms[ $form_id ]['form'];
		} else {
			return null;
		}
	}

	function import_survey_entries( $survey_id, $survey_entries ) {
		$entity = $this->get_entity( 'survey_entry' );
		$total = count( $survey_entries );
		$msg = "  Adding $total Survey Entries";
		$progress_bar = new \cli\progress\Bar( $msg, $total );

		foreach ( $survey_entries as $survey_entry ) {
			$survey_entry['survey_id'] = $survey_id;
			$entity->add( $survey_entry );
			$progress_bar->tick();
		}

		$progress_bar->finish();
	}

}
