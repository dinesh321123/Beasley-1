<?php

namespace GreaterMedia\Import;

class Factory {

	public $types_map = array(
		'feed' => 'GreaterMedia\Import\Feed',
		'venue' => 'GreaterMedia\Import\Venue',
		'event_calendar' => 'GreaterMedia\Import\EventCalendar',
		'blog' => 'GreaterMedia\Import\Blog',
		'photo_album_v2' => 'GreaterMedia\Import\PhotoAlbumV2',
		'showcase' => 'GreaterMedia\Import\Showcase',
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
