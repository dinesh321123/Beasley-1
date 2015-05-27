<?php

namespace GreaterMedia\Import;

class AudioVideoPlayer extends BaseImporter {

	function get_tool_name() {
		return 'audio_video_player';
	}

	function import_source( $source ) {
		$players = $this->players_from_source( $source );
		$total   = count( $players );

		\WP_CLI::log( "Importing $total Audio Video Players ..." );

		foreach ( $players as $player ) {
			$this->import_player( $player );
		}
	}

	function import_player( $player ) {
		$player_name  = $this->import_string( $player['AudioVideoPlayerName'] );
		$mapping      = $this->container->mappings->get_mapping_by_name( $player_name, 'audio_video_player' );

		if ( is_null( $mapping ) ) {
			return;
		}

		$audio_items  = $this->audio_items_from_source( $player );
		$total        = count( $audio_items );
		$msg          = "Importing $total Audio Items";
		$progress_bar = new \WordPress\Utils\ProgressBar( $msg, $total );
		$entity       = $this->get_entity( 'podcast_episode' );

		foreach ( $audio_items as $audio_item ) {
			$episode = $this->episode_from_audio_item( $audio_item );

			if ( ! empty( $episode ) ) {
				$episode['episode_podcast'] = $mapping->wordpress_podcast_name;
				$entity->add( $episode );
			}

			$progress_bar->tick();
		}

		$progress_bar->finish();
	}

	function episode_from_audio_item( $item ) {
		$episode                   = array();
		$episode['episode_name']   = $this->import_string( $item['AudioTitle'] );
		$episode['episode_file']   = $this->import_string( $item['FileName'] );
		$episode['featured_image'] = $this->import_string( $item['Image'] );
		$episode['post_content']   = '';
		$episode['created_on']     = $this->import_string( $item['DateCreated'] );
		$episode['modified_on']    = $this->import_string( $item['DateModified'] );
		$episode['post_format']    = 'audio';

		return $episode;
	}

	function players_from_source( $source ) {
		return $source->AudioVideoPlayer;
	}

	function audio_items_from_source( $player ) {
		return $player->AudioItem;
	}

}
