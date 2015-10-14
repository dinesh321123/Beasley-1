<?php

namespace GreaterMedia\Import;

class Podcast extends BaseImporter {

	function get_tool_name() {
		return 'podcast';
	}

	function import_source( $source ) {
		$channels     = $this->channels_from_source( $source );
		$total        = count( $channels );
		$msg          = "Importing $total Podcasts";
		$progress_bar = new \WordPress\Utils\ProgressBar( $msg, $total );

		foreach ( $channels as $channel ) {
			if ( ! $this->container->mappings->can_import_marketron_name(
				(string) $channel['ChannelTitle'], 'podcast' ) ) {
				\WP_CLI::log( '    Excluded Podcast: ' . (string) $channel['ChannelTitle'] );
				continue;
			}
			$podcast = $this->podcast_from_channel( $channel );

			if ( ! empty( $podcast ) )  {
				$items = $this->items_from_channel( $channel );
				$this->import_podcast_episodes( $podcast, $items );
			}

			$progress_bar->tick();
		}

		$progress_bar->finish();
	}

	function channels_from_source( $source ) {
		return $source->Channel;
	}

	function items_from_channel( $channel ) {
		return $channel->Item;
	}

	function podcast_from_channel( $channel ) {
		$podcast_name        = $this->import_string( $channel['ChannelTitle'] );
		if ( ! $this->can_import_marketron_name( $podcast_name, 'podcast' ) ) {
			return null;
		}

		$mapped_podcast_name = $this->mapped_podcast_from_title( $podcast_name );
		$entity              = $this->get_entity( 'podcast' );

		if ( count( $channel->Item ) === 0 ) {
			return null;
		}

		//\WP_CLI::log( 'Importing Podcasts: ' . $podcast_name );

		if ( empty( $mapped_podcast_name ) ) {
			$existing_podcast = $entity->get_podcast_by_name( $podcast_name );
			if ( empty( $existing_podcast ) ) {
				$podcast = array(
					'podcast_name' => $podcast_name,
					'podcast_author' => 0,
					'created_on' => $this->import_string( $channel['UTCDateCreated'] ),
				);

				$podcast = $entity->add( $podcast );
			} else {
				$podcast = $existing_podcast;
			}
		} else {
			$podcast = $entity->get_podcast_by_name( $mapped_podcast_name );
		}

		return $podcast;
	}

	function import_podcast_episodes( $podcast, $items ) {
		$total        = count( $items );
		//$msg          = "Importing $total items from Podcast";
		//$progress_bar = new \WordPress\Utils\ProgressBar( $msg, $total );
		$entity       = $this->get_entity( 'podcast_episode' );

		foreach ( $items as $item ) {
			$episode                    = $this->episode_from_item( $item );
			$episode['episode_podcast'] = $podcast;
			$episode['post_author']     = $podcast['post_author'];

			$entity->add( $episode );
			//$progress_bar->tick();
		}

		//$progress_bar->finish();
	}

	function episode_from_item( $item ) {
		$episode                 = array();
		$episode['episode_name'] = $this->import_string( $item['ItemTitle'] );
		$episode['episode_file'] = $this->import_string( $item['MediaFilename'] );
		$episode['post_content'] = $this->import_string( $item['ItemDescription'] );
		$episode['created_on']   = $this->import_string( $item['UTCDateCreated'] );
		$episode['modified_on']  = $this->import_string( $item['UTCDateLastModified'] );
		$episode['post_format']  = 'audio';

		return $episode;
	}

	function mapped_podcast_from_title( $title ) {
		$mappings    = $this->container->mappings;
		$mapped_name = $mappings->get_podcast_from_marketron_name( $title );

		if ( ! empty( $mapped_name ) ) {
			return $mapped_name;
		} else {
			return null;
		}
	}

}
