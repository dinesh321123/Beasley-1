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
		$post['post_content']         = $this->content_from_contest( $contest );
		$post['contest_confirmation'] = $this->confirmation_from_contest( $contest );
		$post['contest_entries']      = $contest_entries;
		$post['contest_shows']        = $contest_shows;
		$post['categories'] = $this->categories_from_contest( $contest );

		if ( ! empty( $featured_image ) ) {
			$post['featured_image'] = $featured_image;
		}

		$entity->add( $post );

		return $post;
	}

	function contest_type_from_contest( $contest ) {
		$giveaway_medium = $this->import_string( $contest['GiveawayMedium'] );
		$giveaway_medium = strtolower( $giveaway_medium );

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
		$title = ltrim( $title, '[ONLINE]' );
		$title = ltrim( $title, '[ONLINE*]' );
		$title = ltrim( $title, '[ON-AIR]' );
		$title = ltrim( $title, '[ON-AIR*]' );
		$title = ltrim( $title, ' ' );
		$title = ltrim( $title, '-' );
		$title = ltrim( $title, ' ' );

		return $title;
	}

	function content_from_contest( $contest ) {
		$content = $this->import_string( $contest->ContestText );

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

		$total        = count( $entries );
		$msg          = "  Importing $total Contest Entries";
		//$progress_bar = new \WordPress\Utils\ProgressBar( $msg, $total );

		foreach ( $entries as $entry ) {
			$contest_entry     = $this->contest_entry_from_entry( $entry, $contest_id );
			$contest_entries[] = $contest_entry;
			//$progress_bar->tick();
		}

		//$progress_bar->finish();

		return $contest_entries;
	}

	function categories_from_contest( $contest ) {
		return $this->contest_shows_from_contest( $contest );
	}

	function contest_entry_from_entry( $entry, $contest_id ) {
		$contest_entry               = array();
		$contest_entry['member_id']  = $this->import_string( $entry['MemberID'] );
		$contest_entry['answers']    = $this->answers_from_entry( $entry, $contest_id );
		$contest_entry['created_on'] = $this->import_string( $entry['UTCEntryDate'] );

		return $contest_entry;
	}

	function answers_from_entry( $entry, $contest_id ) {
		$member_id = $this->import_string( $entry['MemberID'] );

		if ( empty( $entry['UserSurveyID'] ) ) {
			// no linked survey, enter to win type of contest
			// TODO
			return array();
		} else {
			if ( empty( $member_id ) ) {
				return array();
			}

			$entity    = $this->get_entity( 'survey' );
			$survey_id = $entry['UserSurveyID'];

			if ( $entity->has_contest_entries( $contest_id, $member_id ) ) {
				$survey_entries = $entity->get_contest_entries( $contest_id, $member_id );
				$first_entry = $survey_entries[0];
				// KLUDGE: Save user multiple entries?
				return $first_entry['answers'];
			} else {
				return array();
			}
		}
	}

}
