<?php

namespace Marketron\Tools;

class Factory {

	public $container;
	public $tools = array();
	public $tools_type_map = array(
		'feed'           => 'Marketron\Tools\Feed',
		'venue'          => 'Marketron\Tools\Venue',
		'event_calendar' => 'Marketron\Tools\EventCalendar',
		'affinity_club'  => 'Marketron\Tools\AffinityClub',
		'blog'           => 'Marketron\Tools\Blog',
		'photo_album_v2'           => 'Marketron\Tools\PhotoAlbumV2',
		'showcase'           => 'Marketron\Tools\Showcase',
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
