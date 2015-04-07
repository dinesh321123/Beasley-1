<?php

namespace GreaterMedia\Import\Repair;

class EmbeddedFormRepairer {

	public $container;

	function update_cids( $source ) {
		\WP_CLI::log( "Loading CIDs $source ..." );
		$json = file_get_contents( $source );
		$json = json_decode( $json, true );

		$contests = $json['contests'];
		$surveys  = $json['surveys'];

		$this->update_cids_in_contests( $contests );
		$this->update_cids_in_surveys( $surveys );
	}

	function update_cids_in_contests( $contests ) {
		$active_contests_args = array(
			'post_type'   => 'contest',
			'post_status' => 'publish',
			'fields'      => array( 'ID', 'post_name', 'post_date_gmt' ),
			'nopaging'    => true,
		);

		$active_contests = new \WP_Query( $active_contests_args );
		$active_contests = $active_contests->get_posts();

		foreach ( $active_contests as $contest ) {
			$post_name    = $contest->post_name;
			$date_created = $contest->post_date_gmt;

			$key = $post_name . ' ' . strtotime( $date_created );

			if ( array_key_exists( $key, $contests ) ) {
				$embedded_form = get_post_meta( $contest->ID, 'embedded_form', true );
				$cid_map       = $contests[ $key ];

				if ( is_string( $embedded_form ) ) {
					$embedded_form = json_decode( $embedded_form, true );
				}

				if ( ! empty( $embedded_form ) ) {
					$changed = false;
					foreach ( $embedded_form as $index => $item ) {
						$cid_map_item                     = $cid_map[ $index ];
						if ( $item['cid'] !== $cid_map_item['expected'] ) {
							$embedded_form[ $index ][ 'cid' ] = $cid_map_item['expected'];
							$changed = true;
						}
					}

					if ( $changed ) {
						$embedded_form = json_encode( $embedded_form, true );
						update_post_meta( $contest->ID, 'embedded_form', $embedded_form );
						\WP_CLI::success( "Fixed CID in Contest - $key: " . $contest->ID );
					}
				}
			}
		}
	}

	function update_cids_in_surveys( $surveys ) {
		$active_surveys_args = array(
			'post_type'   => 'survey',
			'post_status' => 'publish',
			'fields'      => array( 'ID', 'post_name', 'post_date_gmt' ),
			'nopaging'    => true,
		);

		$active_surveys = new \WP_Query( $active_surveys_args );
		$active_surveys = $active_surveys->get_posts();

		foreach ( $active_surveys as $survey ) {
			$post_name    = $survey->post_name;
			$date_created = $survey->post_date_gmt;

			$key = $post_name . ' ' . strtotime( $date_created );

			if ( array_key_exists( $key, $surveys ) ) {
				$embedded_form = get_post_meta( $survey->ID, 'survey_embedded_form', true );
				$cid_map       = $surveys[ $key ];

				if ( is_string( $embedded_form ) ) {
					$embedded_form = json_decode( $embedded_form, true );
				}

				if ( ! empty( $embedded_form ) ) {
					$changed = false;

					foreach ( $embedded_form as $index => $item ) {
						$cid_map_item                     = $cid_map[ $index ];
						if ( $item['cid'] !== $cid_map_item['expected'] ) {
							$embedded_form[ $index ][ 'cid' ] = $cid_map_item['expected'];
							$changed = true;
						}
					}

					if ( $changed ) {
						$embedded_form = json_encode( $embedded_form, true );
						update_post_meta( $survey->ID, 'survey_embedded_form', $embedded_form );
						\WP_CLI::success( "Fixed CID in Survey - $key: " . $survey->ID );
					}
				}
			}
		}
	}

	function repair( $dest ) {
		$contest_cids = $this->build_contest_cids();
		$survey_cids  = $this->build_survey_cids();
		$json = array(
			'contests' => $contest_cids,
			'surveys'  => $survey_cids,
		);

		$json = json_encode( $json, JSON_PRETTY_PRINT );
		file_put_contents( $dest, $json );

		\WP_CLI::success( "CIDs saved to $dest" );

		return $json;
	}

	function build_contest_cids() {
		$cids          = array();
		$tool          = $this->get_tool( 'contest' );
		$importer      = $this->get_importer( 'contest' );
		$sources       = $tool->sources;
		$survey_entity = $this->get_entity( 'survey' );

		foreach ( $sources as $source ) {
			$contests     = $importer->contests_from_source( $source );
			$total        = count( $contests );
			$msg          = "Building $total Contest CIDs";
			$progress_bar = new \WordPress\Utils\ProgressBar( $msg, $total );

			foreach ( $contests as $contest ) {
				$contest_item       = $this->contest_entity_from_contest( $importer, $contest );

				if ( ! empty( $contest_item['contest_form'] ) ) {
					$contest_form = $contest_item['contest_form'];
					$post_name   = $contest_item['post_name'];
					$created_on  = $contest_item['created_on'];
					$modified_on = $contest_item['modified_on'];
					$key         = $post_name . ' ' . strtotime( $created_on );

					if ( ! array_key_exists( $key, $cids ) ) {
						$cids[ $key ] = array();
					}

					foreach ( $contest_form as $contest_form_item ) {
						$cid          = $contest_form_item['cid'];
						$expected_cid = $contest_form_item['expected_cid'];

						$cids[ $key ][] = array( 'cid' => $cid, 'expected' => $expected_cid );
					}
				}

				$progress_bar->tick();
			}

			$progress_bar->finish();
		}

		return $cids;
	}

	function contest_entity_from_contest( $importer, $contest ) {
		$contest_name    = $importer->import_string( $contest['ContestName'] );
		$contest_id      = $importer->import_string( $contest['ContestID'] );
		$featured_image  = $importer->import_string( $contest['ImageFilename'] );
		$contest_entries = $importer->contest_entries_from_contest( $contest, $contest_id );
		$contest_shows   = $importer->contest_shows_from_contest( $contest );

		$post                         = array();
		$post['contest_id']           = $contest_id;
		$post['contest_type']         = $importer->contest_type_from_contest( $contest );
		$post['contest_title']        = $importer->title_from_contest( $contest );
		$post['created_on']           = $importer->import_string( $contest['DateCreated'] );
		$post['modified_on']          = $importer->import_string( $contest['DateModified'] );
		$post['contest_start']        = $importer->import_string( $contest['StartDate'] );
		$post['contest_end']          = $importer->import_string( $contest['EndDate'] );
		$post['contest_single_entry'] = $importer->single_entry_from_contest( $contest );
		$post['contest_members_only'] = $importer->members_only_from_contest( $contest );
		$post['contest_survey']       = $importer->survey_from_contest( $contest );
		$post['post_content']         = $importer->content_from_contest( $contest );
		$post['contest_confirmation'] = $importer->confirmation_from_contest( $contest );
		$post['contest_entries']      = $contest_entries;
		$post['contest_shows']        = $contest_shows;
		$post['categories']           = $importer->categories_from_contest( $contest );
		$post['post_name']            = sanitize_title( htmlentities( $post['contest_title'] ) );

		if ( ! empty( $featured_image ) ) {
			$post['featured_image'] = $featured_image;
		}

		if ( ! empty( $post['contest_survey'] ) ) {
			$entity = $this->get_entity( 'survey' );
			$post['contest_form'] = $entity->get_survey_form( $post['contest_survey'] );
		}

		return $post;
	}

	function build_survey_cids() {
		$cids = array();
		$tool     = $this->get_tool( 'survey' );
		$importer = $this->get_importer( 'survey' );
		$sources  = $tool->sources;

		foreach ( $sources as $source ) {
			$surveys = $importer->surveys_from_source( $source );

			foreach ( $surveys as $survey ) {
				$survey_item       = $importer->survey_entity_from_survey( $survey );

				if ( ! empty( $survey_item['survey_form'] ) ) {
					$survey_form = $survey_item['survey_form'];
					$post_name   = $survey_item['post_name'];
					$created_on  = $survey_item['created_on'];
					$modified_on = $survey_item['modified_on'];
					$key         = $post_name . ' ' . strtotime( $created_on );

					if ( ! array_key_exists( $key, $cids ) ) {
						$cids[ $key ] = array();
					}

					foreach ( $survey_form as $survey_form_item ) {
						$cid          = $survey_form_item['cid'];
						$expected_cid = $survey_form_item['expected_cid'];

						$cids[ $key ][] = array( 'cid' => $cid, 'expected' => $expected_cid );
					}
				}
			}
		}

		return $cids;
	}

	function get_tool( $name ) {
		return $this->container->tool_factory->build( $name );
	}

	function get_importer( $name ) {
		return $this->container->importer_factory->build( $name );
	}

	function get_entity( $name ) {
		return $this->container->entity_factory->build( $name );
	}

}
