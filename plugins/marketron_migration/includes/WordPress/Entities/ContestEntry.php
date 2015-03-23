<?php

namespace WordPress\Entities;

class ContestEntry extends Post {

	public $contest_entry_counter = 0;
	public $member_contests = array();

	function get_post_type() {
		return 'contest_entry';
	}

	function add( &$fields ) {
		$gigya_users       = $this->get_entity( 'gigya_user' );
		$member_id         = $fields['member_id'];
		$answers           = $fields['answers'];
		$contest_id        = $fields['contest_id'];
		$contest_entry_uid = 'contest_entry_' . $this->contest_entry_counter++;

		if ( $gigya_users->user_exists( $member_id ) ) {
			$user         = $gigya_users->get_by_id( $member_id );
			$member_name  = $user['first_name'] . ' ' . $user['last_name'];

			if ( empty( $member_email ) ) {
				$member_email = $user['email'];
			}
		} else {
			$member_name = 'Guest';
		}

		$fields['post_title']  = $contest_entry_uid;
		$fields['post_parent'] = $contest_id;

		$fields['postmeta'] = array(
			'entrant_name'      => $member_name,
			'entrant_reference' => $member_id,
			'entry_source'      => 'embedded_form',
			'entry_reference'   => json_encode( $answers ),
		);

		$fields = parent::add( $fields );
		$contest_id = $fields['ID'];

		//if ( ! empty( $answers ) ) {
			//error_log( 'Found Answers in ' . $contest_id );
		//}

		$this->save_contest_participation( $member_id, $contest_id );

		return $fields;
	}

	function save_contest_participation( $member_id, $contest_id ) {
		if ( ! array_key_exists( $member_id, $this->member_contests ) ) {
			$this->member_contests[ $member_id ] = array();
		}

		$this->member_contests[ $member_id ][] = $contest_id;
	}

}
