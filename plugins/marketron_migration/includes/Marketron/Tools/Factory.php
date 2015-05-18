<?php

namespace Marketron\Tools;

class Factory {

	public $container;
	public $tools = array();
	public $tools_type_map = array(
		/* WARNING: These 3 tools have implicit load order. */
		'affinity_club'  => 'Marketron\Tools\AffinityClub',
		'survey'   => 'Marketron\Tools\Survey',
		'contest'  => 'Marketron\Tools\Contest',

		'feed'           => 'Marketron\Tools\Feed',
		'blog'           => 'Marketron\Tools\Blog',
		'showcase'       => 'Marketron\Tools\Showcase',
		'venue'          => 'Marketron\Tools\Venue',
		'event_calendar' => 'Marketron\Tools\EventCalendar',
		'photo_album_v2' => 'Marketron\Tools\PhotoAlbumV2',

		'channel'       => 'Marketron\Tools\Channel',
		'video_channel' => 'Marketron\Tools\VideoChannel',
		'event_manager' => 'Marketron\Tools\EventManager',

		'podcast'  => 'Marketron\Tools\Podcast',
		//'schedule' => 'Marketron\Tools\Schedule',
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

	function get_tool_names() {
		return array_keys( $this->tools_type_map );
	}

	function destroy() {
		foreach ( $this->tools as $tool_name => $tool ) {
			if ( $tool->can_destroy() ) {
				$tool->destroy();
				$this->tools[ $tool_name ] = null;
				unset( $this->tools[ $tool_name ] );
			}
		}

		$this->tools = null;
		unset( $this->tools );

		$this->container = null;
		unset( $this->container );
	}

}
