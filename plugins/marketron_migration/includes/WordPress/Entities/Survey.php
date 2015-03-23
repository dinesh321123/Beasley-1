<?php

namespace WordPress\Entities;

class Survey extends Post {

	public $survey_forms    = array();
	public $contest_entries = array();
	public $survey_entries  = array();
	public $guest_entry_count = 0;

	function get_post_type() {
		return 'survey';
	}

	function add( &$fields ) {
		$contest_id                = $fields['contest_id'];
		$marketron_id              = $fields['marketron_id'];
		$survey_title              = $fields['survey_title'];
		$survey_content            = $fields['survey_content'];
		$survey_excerpt            = $fields['survey_excerpt'];
		$survey_entries            = $fields['survey_entries'];
		$survey_completion_message = $fields['survey_completion_message'];
		$survey_form               = $fields['survey_form'];

			/*  we file contest entries for later pickup by the contest  entity */
			/*  else it's a catch-22 contest vs surveys */
		/*
		if ( ! empty( $contest_id ) ) {
			if ( ! array_key_exists( $contest_id, $this->contest_entries ) ) {
				$this->contest_entries[ $contest_id ] = array();
			}

			foreach ( $survey_entries as $survey_entry ) {
				$member_id = $survey_entry['member_id'];

				if ( ! array_key_exists( $member_id, $this->contest_entries[ $contest_id ] ) ) {
					$this->contest_entries[ $contest_id ][ $member_id ] = array();
				}

				$this->contest_entries[ $contest_id ][ $member_id ][] = $survey_entry;
			}

			$survey_entries = array();
		}
*/

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
			return $this->survey_forms[ $form_id ];
		} else {
			return null;
		}
	}

	function import_survey_entries( $survey_id, $survey_entries ) {
		$entity      = $this->get_entity( 'survey_entry' );
		$gigya_users = $this->get_entity( 'gigya_user' );

		//$total = count( $survey_entries );
		//$msg = "  Adding $total Survey Entries";
		//$progress_bar = new \WordPress\Utils\ProgressBar( $msg, $total );

		foreach ( $survey_entries as $survey_entry ) {
			$member_id = $survey_entry['member_id'];

			if ( ! empty( $member_id ) ) {
				$survey_entry['survey_id'] = $survey_id;
				$survey_entry              = $entity->add( $survey_entry );
				$gigya_users->add_survey_entry( $member_id, $survey_entry );
			} else {
				$this->guest_entry_count++;
			}

			//$progress_bar->tick();
		}

		//$progress_bar->finish();
	}

	function has_contest_entries( $contest_id, $member_id ) {
		return array_key_exists( $contest_id, $this->contest_entries ) &&
			array_key_exists( $member_id, $this->contest_entries[ $contest_id ] );
	}

	function get_contest_entries( $contest_id, $member_id ) {
		return $this->contest_entries[ $contest_id ][ $member_id ];
	}

	function export_actions() {
		$export_file = $this->container->config->get_gigya_action_export_file();
		$actions     = $this->export_contest_actions();
		$actions = array_merge( $actions, $this->export_survey_actions() );

		file_put_contents( $export_file, json_encode( $actions, JSON_PRETTY_PRINT ) );
	}

	function export_survey_actions() {
		$actions      = array();
		$total        = count( $this->survey_entries );
		$table        = $this->get_table( 'posts' );
		$msg          = "Exporting $total Survey Actions";
		$progress_bar = new \WordPress\Utils\ProgressBar( $msg, $total );

		foreach ( $this->survey_entries as $member_id => $entries ) {
			foreach ( $entries as $survey_entry_id ) {
				$survey_entry = $table->get_row_by_id( $survey_entry_id );
				$survey_id    = $survey_entry['post_parent'];
				$survey       = $table->get_row_by_id( $survey_id );

				if ( ! empty( $survey['contest_id'] ) ) {
					// this is a contest, and will get added as a contest
					// action elsewhere
					continue;
				}

				$answers      = $survey_entry['answers'];
				$action_item  = $this->answers_to_action_item(
					$answers,
					$member_id,
					'action:survey',
					$survey_id
				);

				$actions[] = $action_item;
			}

			$progress_bar->tick();
		}

		$progress_bar->finish();

		return $actions;
	}

	function export_contest_actions() {
		$actions      = array();
		$total        = count( $this->contest_entries );
		$msg          = "Exporting $total Contest Actions";
		$progress_bar = new \WordPress\Utils\ProgressBar( $msg, $total );

		foreach ( $this->contest_entries as $contest_id => $member_entries ) {
			foreach ( $member_entries as $survey_entries ) {
				foreach ( $survey_entries as $survey_entry ) {
					if ( ! empty( $survey_entry['answers'] ) ) {
						$member_id = $survey_entry['member_id'];
						if ( empty( $member_id ) ) {
							continue;
						}

						$answers     = $survey_entry['answers'];
						$action_item = $this->answers_to_action_item(
							$answers,
							$member_id,
							'action:contest',
							$contest_id
					   	);

						$actions[] = $action_item;
					}
				}
			}

			$progress_bar->tick();
		}

		$progress_bar->finish();

		return $actions;
	}

	function answers_to_action_item( &$answers, $member_id, $action_type, $action_id ) {
		$actions = array();
		$action = array(
			'actionType' => $action_type,
			'actionID'   => strval( $action_id ),
			'actionData' => array()
		);

		foreach ( $answers as $key => $value ) {
			$action_data_item = array( 'name' => $key );

			if ( is_array( $value ) ) {
				$action_data_item['value_list'] = $value;
			} else {
				$action_data_item['value_t'] = $value;
			}

			$action['actionData'][] = $action_data_item;
		}

		return array(
			'UID' => $member_id,
			'data' => array(
				'actions' => array( $action )
			),
		);
	}

}
