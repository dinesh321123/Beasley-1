<?php

namespace Marketron\Tools;

class Factory {

	public $container;
	public $tools = array();
	public $tools_type_map = array(
		'affinity_club'  => 'Marketron\Tools\AffinityClub',
		'feed'           => 'Marketron\Tools\Feed',
		'blog'           => 'Marketron\Tools\Blog',
		'showcase'       => 'Marketron\Tools\Showcase',
		'venue'          => 'Marketron\Tools\Venue',
		'event_calendar' => 'Marketron\Tools\EventCalendar',
		'photo_album_v2' => 'Marketron\Tools\PhotoAlbumV2',

		'channel'       => 'Marketron\Tools\Channel',
		'video_channel' => 'Marketron\Tools\VideoChannel',
		'event_manager' => 'Marketron\Tools\EventManager',

		'concert'  => 'Marketron\Tools\Concert',
		'podcast'  => 'Marketron\Tools\Podcast',
		'survey'   => 'Marketron\Tools\Survey',
		'contest'  => 'Marketron\Tools\Contest',
		'schedule' => 'Marketron\Tools\Schedule',
		'podcast'  => 'Marketron\Tools\Podcast',
	);

	function build( $tool_name ) {
		if ( ! array_key_exists( $tool_name, $this->tools ) ) {
			$tool_type                 = $this->tools_type_map[ $tool_name ];
			$tool                      = new $tool_type( $this->container );
			$tool->container = $this->container;
			$this->tools[ $tool_name ] = $tool;
		}

		return $this->tools[ $tool_name ];
	}

}
