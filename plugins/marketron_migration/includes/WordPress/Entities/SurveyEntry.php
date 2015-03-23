<?php

namespace WordPress\Entities;

class SurveyEntry extends Post {

	public $survey_entry_counter = 0;

	function get_post_type() {
		return 'survey_response';
	}

	function add( &$fields ) {
		$member_id    = $fields['member_id'];
		$member_email = $fields['member_email'];
		$entity       = $this->get_entity( 'gigya_user' );

		if ( $entity->user_exists( $member_id ) ) {
			$member_name  = $entity->get_full_name( $member_id );

			if ( empty( $member_email ) ) {
				$member_email = $entity->get_email( $member_id );
			}
		} else {
			$member_name = 'Guest';
		}

		$created_on   = $fields['created_on'];
		$answers      = $fields['answers'];
		$survey_id    = $fields['survey_id'];

		$survey_entry_uid = 'survey_entry_' . $this->survey_entry_counter++;

		$fields['post_parent'] = $survey_id;
		$fields['post_title']  = $survey_entry_uid;

		$meta = array(
			'entrant_name'      => $member_name,
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
