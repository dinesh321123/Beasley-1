<?php

namespace WordPress\Entities;

class PodcastEpisode extends Post {

	function get_post_type() {
		return 'episode';
	}

	function add( &$fields ) {
		$episode_file    = $fields['episode_file'];
		$episode_name    = $fields['episode_name'];
		$episode_podcast = $fields['episode_podcast'];

		if ( is_string( $episode_podcast ) ) {
			$podcast_name = $episode_podcast;
		} else if ( ! empty( $episode_podcast['podcast_name'] ) ) {
			$podcast_name = $episode_podcast['podcast_name'];
		} else {
			$podcast_name = '';
		}

		$podcast_id = $this->get_podcast_id( $podcast_name );
		//error_log( "Get Podcast ID: $podcast_name " . $podcast_id );

		$fields['post_type']      = $this->get_post_type();
		$fields['post_parent']    = $podcast_id;
		$fields['post_title']     = $episode_name;

		if ( ! empty( $episode_file ) && $episode_file !== 'placeholder' ) {
			$fields['featured_audio'] = $episode_file;
		}

		$fields = parent::add( $fields );
		$episode_id = $fields['ID'];

		$series_entity = $this->get_entity( 'series_taxonomy' );
		$series_entity->add( $podcast_name, $episode_id );

		return $fields;
	}

	function get_podcast_id( $podcast_name ) {
		$podcast_name = trim( $podcast_name );
		$table = $this->get_table( 'posts' );

		if ( $table->has_row_with_field( 'post_title', $podcast_name ) ) {
			$row = $table->get_rows_with_field( 'post_title', $podcast_name );
			foreach ( $row as $record ) {
				if ( $record['post_type'] === 'podcast' ) {
					return $record['ID'];
				}
			}

			\WP_CLI::log( "Show for Podcast not found - $podcast_name" );
			return 0;
		} else {
			\WP_CLI::log( "Podcast not found - $podcast_name" );
			return 0;
		}
	}

}
