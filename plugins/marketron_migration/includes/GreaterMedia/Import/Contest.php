<?php

namespace GreaterMedia\Import;

class Contest extends BaseImporter {

	function get_tool_name() {
		return 'contest';
	}

	function import_source( $source ) {
		$contests     = $this->contests_from_source( $source );
		$total        = count( $contests );
		$msg          = "Importing $total Contests";
		$progress_bar = new \WordPress\Utils\ProgressBar( $msg, $total );

		foreach ( $contests as $contest ) {
			$this->import_contest( $contest );
			$progress_bar->tick();
		}

		$progress_bar->finish();
	}

	function import_contest( $contest ) {
		$contest_name   = $this->import_string( $contest['ContestName'] );
		//\WP_CLI::log( "Importing Contest: $contest_name" );

		// For testing
		//if ( strtotime( (string) $contest['DateCreated'] ) < strtotime( '-2 year' ) ) {
			//return;
		//}

		$entity          = $this->get_entity( 'contest' );
		$contest_id      = $this->import_string( $contest['ContestID'] );
		$featured_image  = $this->import_string( $contest['ImageFilename'] );
		$contest_entries = $this->contest_entries_from_contest( $contest, $contest_id );
		$contest_shows   = $this->contest_shows_from_contest( $contest );

		$post                         = array();
		$post['contest_id']           = $contest_id;
		$post['contest_type']         = $this->contest_type_from_contest( $contest );
		$post['contest_title']        = $this->title_from_contest( $contest );
		$post['created_on']           = $this->import_string( $contest['DateCreated'] );
		$post['modified_on']          = $this->import_string( $contest['DateModified'] );
		$post['contest_start']        = $this->import_string( $contest['StartDate'] );
		$post['contest_end']          = $this->import_string( $contest['EndDate'] );
		$post['contest_single_entry'] = $this->single_entry_from_contest( $contest );
		$post['contest_members_only'] = $this->members_only_from_contest( $contest );
		$post['contest_survey']       = $this->survey_from_contest( $contest );
		$post['inline_contest_form']  = $this->contest_form_from_contest( $contest );
		$post['post_content']         = $this->content_from_contest( $contest );
		$post['contest_confirmation'] = $this->confirmation_from_contest( $contest );
		$post['contest_entries']      = $contest_entries;
		$post['contest_shows']        = $contest_shows;
		$post['categories']           = $this->categories_from_contest( $contest );

		if ( ! empty( $featured_image ) ) {
			$post['featured_image'] = $featured_image;
		}

		$entity->add( $post );

		return $post;
	}

	function contest_type_from_contest( $contest ) {
		$giveaway_medium = $this->import_string( $contest['GiveawayMedium'] );

		if ( ! empty( $giveaway_medium ) ) {
			$giveaway_medium = strtolower( $giveaway_medium );
		} else if ( ! empty( $contest->CustomQuestionConfigurations ) ) {
			// has form, so assuming online
			$giveaway_medium = 'web';
		}

		$has_on_air = strpos( $giveaway_medium, 'air' ) !== false;
		$has_web = strpos( $giveaway_medium, 'web' ) !== false;
		$has_both = $has_on_air && $has_web;

		if ( $has_both ) {
			return 'both';
		} else if ( $has_web ) {
			return 'online';
		} else {
			return 'onair';
		}
	}

	function single_entry_from_contest( $contest ) {
		$entry_restriction = $this->import_string( $contest['EntryRestriction'] );

		if ( strpos( $entry_restriction, 'Single' ) !== false ) {
			return true;
		} else {
			return false;
		}
	}

	function contest_shows_from_contest( $contest ) {
		$title   = $this->title_from_contest( $contest );
		$authors = $this->container->mappings->get_matched_authors( $title );

		return $authors;
	}

	function contest_form_from_contest( $contest ) {
		$form_config = $contest->CustomQuestionConfigurations;

		if ( ! empty( $form_config ) ) {
			$form_items = array();
			$field_items = $form_config->CustomQuestionConfiguration;

			foreach ( $field_items as $field_item ) {
				$form_item = $this->form_item_from_custom_question( $field_item );

				if ( ! empty( $form_item ) ) {
					$form_items[] = $form_item;
				}
			}

			return $form_items;
		} else {
			return null;
		}
	}

	function form_item_from_custom_question( $question ) {
		$form_item     = array();
		$field_name    = $this->import_string( $question['FieldName'] );
		$question_text = $this->import_string( $question['Question'] );
		$question_text = str_replace( '"', '&quot;', $question_text );
		$question_id   = ltrim( $field_name, 'Field' );
		$answer_type   = $this->import_string( $question['AnswerType'] );
		$required      = $this->import_bool( $question['Required'] );

		switch ( $answer_type ) {
			case 'Text':
				$field_type    = 'text';
				$field_options = array( 'size' => 'large' );
				break;

			case 'TextArea':
				$field_type = 'paragraph';
				$field_options = array( 'size' => 'large' );
				break;

			case 'Checkbox':
				$field_type = 'checkboxes';
				$field_options = array(
					array( 'label' => $question_text, 'checked' => false ),
				);
				break;

			case 'Select':
				$field_type    = 'dropdown';
				$field_options = array();
				$options       = array();

				foreach ( $question->QuestionOptions->QuestionOption as $option ) {
					$field_value = $this->import_string( $option['FieldValue'] );
					$field_value = str_replace( '"', '&quot;', $field_value );
					$options[] = array(
						'label' => $field_value,
						'checked' => false,
					);
				}

				$field_options['options'] = $options;
				break;

			case 'Radio':
				$field_type    = 'radio';
				$field_options = array();
				$options       = array();

				if ( empty( $question->QuestionOptions ) ) {
					$field_type = 'text';
					$field_options = array( 'size' => 'large' );
					\WP_CLI::warning( "Invalid QuestionOptions: $question_text" );
				} else {
					foreach ( $question->QuestionOptions->QuestionOption as $option ) {
						$field_value = $this->import_string( $option['FieldValue'] );
						$field_value = str_replace( '"', '&quot;', $field_value );
						$options[] = array(
							'label' => $field_value,
							'checked' => false,
						);
					}

					$field_options['options'] = $options;
				}

				break;

			default:
				var_dump( $field_name );
				\WP_CLI::error( 'Unknown Contest Form AnswerType: ' . $answer_type );
		}

		$form_item['label']         = $question_text;
		$form_item['cid']           = 'c' . $question_id;
		$form_item['field_type']    = $field_type;
		$form_item['required']      = $required;
		$form_item['field_options'] = $field_options;

		return $form_item;
	}

	function members_only_from_contest( $contest ) {
		$non_club = $this->import_bool( $contest['IsNonClubContest'] );
		return $non_club === false;
	}

	function survey_from_contest( $contest ) {
		if ( ! empty( $contest['SurveyID'] ) ) {
			return $this->import_string( $contest['SurveyID'] );
		} else {
			return null;
		}
	}

	function title_from_contest( $contest ) {
		$title = $this->import_string( $contest->ContestText['ContestHeader'] );
		//$title = ltrim( $title, '\[ONLINE\]' );
		//$title = ltrim( $title, '\[ONLINE\*\]' );
		//$title = ltrim( $title, '\[ON-AIR\]' );
		//$title = ltrim( $title, '\[ON-AIR\*\]' );
		$title = ltrim( $title, ' ' );
		$title = ltrim( $title, '-' );
		$title = ltrim( $title, ' ' );
		$title = ucwords( $title );

		return $title;
	}

	function content_from_contest( $contest ) {
		$content = $this->import_string( $contest->ContestText['ContestDescription'] );

		return $content;
	}

	function confirmation_from_contest( $contest ) {
		$confirmation = $this->import_string( $contest->ConfirmationText );
		return wp_strip_all_tags( $confirmation );
	}

	function contests_from_source( $source ) {
		return $source->Contest;
	}

	function entries_from_contest( $contest ) {
		return $contest->Entries->Entry;
	}

	function contest_entries_from_contest( $contest ) {
		$contest_id = $this->import_string( $contest['ContestID'] );
		$contest_entries = array();
		$entries         = $this->entries_from_contest( $contest );

		if ( empty( $entries ) ) {
			return $contest_entries;
		}

		$total          = count( $entries );
		$msg            = "  Importing $total Contest Entries";
		$gigya_users    = $this->container->entity_factory->build( 'gigya_user' );
		$user_entries   = array();
		//$progress_bar = new \WordPress\Utils\ProgressBar( $msg, $total );

		foreach ( $entries as $entry ) {
			$member_id = $this->import_string( $entry['MemberID'] );

			if ( array_key_exists( $member_id, $user_entries ) ) {
				continue;
			}

			$contest_entry = $this->contest_entry_from_entry(
				$entry, $contest_id, $contest, $gigya_users
			);

			if ( ! empty( $contest_entry ) ) {
				$user_entries[ $member_id ] = true;
				$contest_entries[] = $contest_entry;
			}
			//$progress_bar->tick();
		}

		//$progress_bar->finish();

		return $contest_entries;
	}

	function categories_from_contest( $contest ) {
		return $this->contest_shows_from_contest( $contest );
	}

	function contest_entry_from_entry( $entry, $contest_id, $contest, $gigya_users ) {
		$contest_entry                         = array();
		$contest_entry['marketron_contest_id'] = $contest_id;
		$contest_entry['member_id']            = $this->import_string( $entry['MemberID'] );

		if ( ! $gigya_users->can_import_member( $contest_entry['member_id'] ) ) {
			return null;
		}

		$contest_entry['answers']              = $this->answers_from_entry( $entry, $contest_id, $contest );
		$contest_entry['created_on']           = $this->import_string( $entry['UTCEntryDate'] );
		$contest_entry['user_survey_id']       = $this->import_string( $entry['UserSurveyID'] );

		return $contest_entry;
	}

	function answers_from_entry( $entry, $contest_id, $contest ) {
		$member_id   = $this->import_string( $entry['MemberID'] );
		$gigya_users = $this->get_entity( 'gigya_user' );

		if ( empty( $entry['UserSurveyID'] ) ) {
			// no linked survey, enter to win type of contest
			$answers   = array();
			$questions = $contest->CustomQuestionConfigurations;

			if ( ! empty( $questions->CustomQuestionConfiguration ) ) {
				foreach ( $questions->CustomQuestionConfiguration as $question ) {
					$field_name = $this->import_string( $question['FieldName'] );

					if ( ! empty( $entry[ $field_name ] ) ) {
						$field_key   = 'c' . ltrim( $field_name, 'Field' );
						$field_value = $this->import_string( $entry[ $field_name ] );
						$answers[ $field_key ] = $field_value;
					}
				}
			}

			return $answers;
		} else {
			// answers will be picked up from the corresponding survey
			$user_survey_id = $this->import_string( $entry['UserSurveyID'] );

			if ( ! empty( $user_survey_id ) ) {
				// For Contests linked to Surveys we lookup the answer
				// from the corresponding survey response
				return $gigya_users->get_user_survey_answers( $member_id, $user_survey_id );
			} else {
				return array();
			}
		}
	}

}
