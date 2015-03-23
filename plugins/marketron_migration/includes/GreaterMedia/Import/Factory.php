<?php

namespace GreaterMedia\Import;

class Factory {

	public $types_map = array(
		'feed' => 'GreaterMedia\Import\Feed',
		'venue' => 'GreaterMedia\Import\Venue',
		'event_calendar' => 'GreaterMedia\Import\EventCalendar',
		'event_manager' => 'GreaterMedia\Import\EventManager',
		'blog' => 'GreaterMedia\Import\Blog',
		'photo_album_v2' => 'GreaterMedia\Import\PhotoAlbumV2',
		'showcase' => 'GreaterMedia\Import\Showcase',
		'channel' => 'GreaterMedia\Import\Channel',
		'video_channel' => 'GreaterMedia\Import\VideoChannel',
		'podcast' => 'GreaterMedia\Import\Podcast',
		'survey' => 'GreaterMedia\Import\Survey',
		'livestream' => 'GreaterMedia\Import\LiveStream',
		'contest' => 'GreaterMedia\Import\Contest',
		'affinity_club' => 'GreaterMedia\Import\AffinityClub',
	);

	public $container;
	public $instances = array();

	function build( $tool_name ) {
		if ( ! array_key_exists( $tool_name, $this->instances ) ) {
			$type                          = $this->types_map[ $tool_name ];
			$instance                      = new $type( $this->container );
			$instance->container           = $this->container;
			$this->instances[ $tool_name ] = $instance;
		}

		return $this->instances[ $tool_name ];
	}

}
