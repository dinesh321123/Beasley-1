<?php

namespace GreaterMedia\Import;

class Podcast extends BaseImporter {

	function get_tool_name() {
		return 'podcast';
	}

	function import_source( $source ) {
		$channels = $this->channels_from_source( $source );

		foreach ( $channels as $channel ) {
			$podcast = $this->podcast_from_channel( $channel );
			$items   = $this->items_from_channel( $channel );

			$this->import_podcast_episodes( $podcast, $items );
		}
	}

	function channels_from_source( $source ) {
		return $source->Channel;
	}

	function items_from_channel( $channel ) {
		return $channel->Item;
	}

	function podcast_from_channel( $channel ) {
		$podcast_name = $this->import_string( $channel['ChannelTitle'] );
		\WP_CLI::log( 'Importing Podcasts: ' . $podcast_name );

		$entity       = $this->get_entity( 'podcast' );
		$podcast      = $entity->get_podcast_by_name( $podcast_name );

		if ( is_null( $podcast ) ) {
			$podcast = array(
				'podcast_name' => $podcast_name,
				'podcast_author' => 0,
			);

			$podcast = $entity->add( $podcast );
		}

		return $podcast;
	}

	function import_podcast_episodes( $podcast, $items ) {
		$total        = count( $items );
		$msg          = "Importing $total items from Podcast";
		$progress_bar = new \cli\progress\Bar( $msg, $total );
		$entity       = $this->get_entity( 'podcast_episode' );

		foreach ( $items as $item ) {
			$episode                    = $this->episode_from_item( $item );
			$episode['episode_podcast'] = $podcast;
			$episode['post_author']     = $podcast['post_author'];

			$entity->add( $episode );
			$progress_bar->tick();
		}

		$progress_bar->finish();
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

}
