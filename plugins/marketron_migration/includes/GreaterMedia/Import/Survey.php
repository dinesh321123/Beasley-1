<?php

namespace GreaterMedia\Import;

class Survey extends BaseImporter {

	function get_tool_name() {
		return 'survey';
	}

	function import_source( $source ) {
		$surveys      = $this->surveys_from_source( $source );
		$total        = count( $surveys );
		$progress_bar = new \cli\progress\Bar( "Importing $total Surveys", $total );
		$index = 0;
		$limit = 3;

		foreach( $surveys as $survey ) {
			$this->import_survey( $survey );
			$progress_bar->tick();

			//if ( $index++ >= $limit ) {
				//break;
			//}
		}

		$progress_bar->finish();
	}

	function surveys_from_source( $source ) {
		return $source->Survey;
	}

	function responses_from_survey( $survey ) {
		return $survey->Responses->Response;
	}

	function import_survey( $survey ) {
		$survey_name = $this->import_string( $survey['SurveyName'] );
		\WP_CLI::log( "Importing Survey: $survey_name" );

		$survey_entity = $this->survey_entity_from_survey( $survey );
		$entity        = $this->get_entity( 'survey' );

		if ( ! empty( $survey_entity['survey_form'] ) ) {
			$entity->add( $survey_entity );
		}
	}

	function survey_entity_from_survey( $survey ) {
		$survey_name           = $this->import_string( $survey['SurveyName'] );
		$survey_id             = $this->import_string( $survey['SurveyID'] );
		$survey_featured_image = $this->featured_image_from_survey( $survey );
		$survey_restrictions   = $this->restrictions_from_survey( $survey );
		$responses             = $this->responses_from_survey( $survey );
		$survey_entries        = $this->survey_entries_from_responses( $responses );

		$post = array(
			'created_on'                => $this->import_string( $survey['UTCDateCreated'] ),
			'modified_on'               => $this->import_string( $survey['UTCDateModified'] ),
			'post_name'                 => sanitize_title( $survey_name ),
			'marketron_id'              => $survey_id,
			'survey_title'              => $this->title_from_survey( $survey ),
			'survey_content'            => $this->content_from_survey( $survey ),
			'survey_excerpt'            => $this->excerpt_from_survey( $survey ),
			'survey_entries'            => $survey_entries,
			'survey_completion_message' => wp_strip_all_tags( $this->import_string( $survey['CompletionMessage'] ) ),
		);

		if ( ! empty( $survey_featured_image ) ) {
			$post['featured_image'] = $survey_featured_image;
		}

		$form = $this->form_from_survey( $survey );

		if ( ! empty( $form ) ) {
			if ( ! empty( $form['custom_label'] ) ) {
				$post['survey_content'] .= $form['custom_label'];
			}

			$post['survey_form'] = $form['form_items'];
		}

		return $post;
	}

	function content_from_survey( $survey ) {
		$content = $this->import_string( $survey['SurveyDescription'] );

		return $content;
	}

	function excerpt_from_survey( $survey ) {
		$excerpt = $this->import_string( $survey['SurveyBlurb'] );

		return $excerpt;
	}

	function title_from_survey( $survey ) {
		$title = $this->import_string( $survey['SurveyTitle'] );

		return $title;
	}

	function featured_image_from_survey( $survey ) {
		$survey_id = $this->import_string( $survey['SurveyID'] );
		$image     = $this->import_string( $survey['ImageFileName'] );

		if ( ! empty( $image ) && strpos( $image, '/Surveys/' ) === false ) {
			// Marketron survey image contains only the filename
			$image = "/Surveys/$survey_id/Large/$image";
		}

		return $image;
	}

	function restrictions_from_survey( $survey ) {
		$members_only = $this->import_string( $survey['IsClubMemberOnly'] );
		$members_only = filter_var( $members_only, FILTER_VALIDATE_BOOLEAN );

		$single_entry = strtolower( $this->import_string( $survey['EntryRestriction'] ) );
		$single_entry = $single_entry === 'single entry';

		$restrictions = array(
			'members_only' => $members_only,
			'single_entry' => $single_entry,
		);

		return array();
	}

	function form_from_survey( $survey ) {
		$questions  = $survey->Questions->Question;
		$form_items = array();
		$form = array( 'custom_label' => '' );
		$cid = 1;

		if ( ! empty( $questions ) ) {
			foreach ( $questions as $question ) {
				$form_item = $this->form_item_from_question( $question );
				$form_item['cid'] = 'c' . strval( $cid++ );

				if ( $form_item['field_type'] === 'custom_label' ) {
					$form['custom_label'] .= $form_item['custom_label'];
				} else if ( $form_item !== false ) {
					$form_items[] = $form_item;
				}
			}

			$form['form_items'] = $form_items;
			return $form;
		} else {
			return null;
		}
	}

	function form_item_from_question( $question ) {
		$input_style       = strtolower( $this->import_string( $question['InputStyle'] ) );
		$question_id       = $this->import_string( $question['QuestionID'] );
		$question_text     = $this->import_string( $question['QuestionText'] );
		$question_required = $this->import_bool( $question['isRequired'] );

		if ( ! empty( $question['FieldLabel'] ) ) {
			$field_label = $this->import_string( $question['FieldLabel'] );
		} else {
			$field_label = $question_text;
		}

		if ( $field_label === 'Email Address' ) {
			$input_style = 'email';
		}

		$form_item     = array();
		$field_options = array();

		switch ( $input_style ) {
			case 'checkboxes':
				$field_type    = 'checkboxes';
				$field_options = $this->field_options_for_checkbox( $question );
				break;

			case 'text box':
				$field_type = 'text';
				$field_options = $this->field_options_for_text_input( $question );
				break;

			case 'email':
				$field_type = 'email';
				$field_options = $this->field_options_for_email( $question );
				break;

			case 'text area':
				$field_type = 'paragraph';
				$field_options = $this->field_options_for_paragraph( $question );
				break;

			case 'address':
				$field_type = 'address';
				$field_options = $this->field_options_for_address( $question );
				break;

			case 'buttons':
				$field_type = 'radio';
				$field_options = $this->field_options_for_radio_button( $question );
				break;

			case 'dropdown':
				$field_type      = 'dropdown';
				$field_options = $this->field_options_for_dropdown( $question );;
				break;

			case 'calendar':
				$field_type = 'date';
				$field_options = $this->field_options_for_date( $question );
				break;

			case 'label':
				$field_type = 'custom_label';
				$form_item['custom_label'] = $question_text;
				break;

			default:
				\WP_CLI::error( 'Unknown Form Field: ' . $input_style );
				$field_type = 'unknown';
				$form_item  = false;
		}

		$form_item['label']         = $field_label;
		$form_item['cid']           = 'c' . $question_id;
		$form_item['field_type']    = $field_type;
		$form_item['required']      = $question_required;
		$form_item['field_options'] = $field_options;

		return $form_item;
	}

	function field_options_for_checkbox( $question ) {
		$field_options    = array();
		$question_options = $question->Option;

		foreach ( $question_options as $question_option ) {
			$label   = $this->import_string( $question_option['Value'] );
			$label   = str_replace( '"', "'", $label );
			$checked = $this->import_bool( $question_option['isCheckedByDefault'] );

			$option = array(
				'label'   => $label,
				'checked' => $checked,
			);

			$options[] = $option;
		}

		$field_options['options'] = $options;

		return $field_options;
	}

	function field_options_for_text_input( $question ) {
		return array( 'size' => 'large' );
	}

	function field_options_for_paragraph( $question ) {
		return array( 'size' => 'large' );
	}

	function field_options_for_radio_button( $question ) {
		return $this->field_options_for_checkbox( $question );
	}

	function field_options_for_dropdown( $question ) {
		return $this->field_options_for_checkbox( $question );
	}

	function field_options_for_date( $question ) {
		return array();
	}

	function field_options_for_email( $question ) {
		return array();
	}

	function field_options_for_address( $question ) {
		return array();
	}

	function survey_entries_from_responses( $responses ) {
		$survey_entries = array();
		$exclude_survey_entries = $this->get_site_option( 'exclude_survey_entries' );

		if ( empty( $responses ) || $exclude_survey_entries ) {
			return $survey_entries;
		}

		$total = count( $responses );
		$progress_bar = new \cli\progress\Bar( "  Importing $total Survey Responses", $total );

		foreach ( $responses as $response ) {
			$survey_entry     = $this->survey_entry_from_response( $response );
			$survey_entries[] = $survey_entry;

			$progress_bar->tick();
		}

		$progress_bar->finish();

		return $survey_entries;
	}

	function survey_entry_from_response( $response ) {
		$survey_entry                 = array();
		$survey_entry['member_id']    = $this->import_string( $response['MemberID'] );
		$survey_entry['member_name']  = $this->import_string( $response['MemberID'] ); // TODO: lookup member name by id
		$survey_entry['member_email'] = $this->import_string( $response['EmailAddress'] );
		$survey_entry['created_on']   = $this->import_string( $response['UTCCompletionDate'] );

		$answers                 = $this->survey_answers_from_response( $response );
		$survey_entry['answers'] = $answers;

		return $survey_entry;
	}

	function answers_from_response( $response ) {
		return $response->Answer;
	}

	function survey_answers_from_response( $response ) {
		$answers = $this->answers_from_response( $response );
		$survey_answers = array();

		foreach ( $answers as $answer ) {
			$question_id  = $this->import_string( $answer['QuestionID'] );
			$answer_value = $this->import_string( $answer['AnswerValue'] );
			$field_id     = 'form_field_c' . $question_id;

			if ( array_key_exists( $field_id, $survey_answers ) ) {
				// converting to array for multiple answers to allow question
				$survey_answers[ $field_id ]   = array( $survey_answers[ $field_id ] );
				$survey_answers[ $field_id ][] = $answer_value;
			} else {
				$survey_answers[ $field_id ] = $answer_value;
			}
		}

		return $survey_answers;
	}

}
