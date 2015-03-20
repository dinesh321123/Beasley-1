<?php

namespace WordPress\Entities;

class SurveyEntry extends Post {

	public $survey_entry_counter = 0;

	function get_post_type() {
		return 'survey_response';
	}

	function add( &$fields ) {
		$member_name  = $fields['member_name'];
		$member_id    = $fields['member_id'];
		$member_email = $fields['member_email'];
		$created_on   = $fields['created_on'];
		$answers      = $fields['answers'];
		$survey_id    = $fields['survey_id'];

		$survey_entry_uid = $this->survey_entry_counter++;

		$fields['post_parent'] = $survey_id;
		$fields['post_title']  = $survey_entry_uid;

		$meta = array(
			'entrant_name'      => $member_email,
			'entrant_reference' => $member_id,
			'entry_source'      => 'embedded_form',
			'entry_reference'   => json_encode( $answers )
		);

		$fields['postmeta'] = $meta;

		$fields = parent::add( $fields );
		//error_log( 'survey_entry_id: ' . $fields['ID'] . ' for survey: ' . $survey_id );

		return $fields;
	}

}
