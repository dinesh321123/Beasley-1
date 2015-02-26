<?php

namespace GreaterMedia\Gigya\Action;

class ContestEntryDispatcher {

	function register() {
		add_action( 'greatermedia_contest_entry_save', array( $this, 'did_save_contest_entry' ) );
		add_action( 'greatermedia_survey_entry_save', array( $this, 'did_save_survey_entry' ) );
	}

	function publish( $action ) {
		save_gigya_action( $action, 'logged_in_user' );
	}

	function did_save_contest_entry( $entry ) {
		$action = $this->action_for_entry( $entry, 'contest' );
		if ( $action ) {
			$this->publish( $action );
		}
	}

	function did_save_survey_entry( $entry ) {
		$action = $this->action_for_entry( $entry, 'survey' );
		if ( $action ) {
			$this->publish( $action );
		}
	}

	function action_for_entry( $entry, $type = 'contest' ) {
		$data = $this->action_data_for_entry_reference( $entry );
		
		return empty( $data ) ? false : array(
			'actionType' => 'action:' . $type,
			'actionID'   => strval( $entry->post->post_parent ),
			'actionData' => $data,
		);
	}

	function action_data_for_entry_reference( $entry ) {
		$data = $entry;
		if ( is_object( $entry ) ) {
			$data = json_decode( $entry->entry_reference, true );

			$extra_data = array(
				'entrant_name', 'entrant_gender', 'entrant_email',
				'entrant_zip', 'entrant_birth_year', 'entrant_birth_date',
			);
			
			foreach ( $extra_data as $key ) {
				if ( property_exists( $entry, $key ) ) {
					$data[ $key ] = $entry->$key;
				}
			}
		} elseif ( is_string( $entry ) ) {
			$data = json_decode( $entry, true );
		}

		$actionData = array();

		foreach ( $data as $key => $value ) {
			$actionData[] = array(
				'name'  => (string) $key,
				'value' => $value,
			);
		}

		return $actionData;
	}

}
