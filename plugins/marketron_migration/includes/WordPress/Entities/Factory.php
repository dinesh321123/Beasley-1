<?php

namespace WordPress\Entities;

class Factory {

	public $container;
	public $instances = array();
	public $types     = array(
		'tag'             => 'WordPress\Entities\Tag',
		'category'        => 'WordPress\Entities\Category',
		'post_format'     => 'WordPress\Entities\PostFormat',
		'show_taxonomy'   => 'WordPress\Entities\ShowTaxonomy',

		'user'            => 'WordPress\Entities\User',
		'author'          => 'WordPress\Entities\Author',

		'post'            => 'WordPress\Entities\Post',
		'attachment'      => 'WordPress\Entities\Attachment',
		'legacy_redirect' => 'WordPress\Entities\LegacyRedirect',

		'venue' => 'WordPress\Entities\Venue',
		'event_category' => 'WordPress\Entities\EventCategory',
		'event' => 'WordPress\Entities\Event',
		'show' => 'WordPress\Entities\Show',
		'podcast' => 'WordPress\Entities\Podcast',
		'podcast_episode' => 'WordPress\Entities\PodcastEpisode',
		'series_taxonomy' => 'WordPress\Entities\SeriesTaxonomy',
		'blog' => 'WordPress\Entities\Blog',
		'gallery' => 'WordPress\Entities\Gallery',
		'album' => 'WordPress\Entities\Album',
		'survey' => 'WordPress\Entities\Survey',
		'survey_entry' => 'WordPress\Entities\SurveyEntry',
		'live_stream' => 'WordPress\Entities\LiveStream',
		'contest' => 'WordPress\Entities\Contest',
		'contest_entry' => 'WordPress\Entities\Contest',
	);


	function build( $name ) {
		return $this->get_entity( $name );
	}

	function get_type( $name ) {
		if ( array_key_exists( $name, $this->types ) ) {
			return $this->types[ $name ];
		} else {
			\WP_CLI::error( "Invalid Entity Type - $name" );
		}
	}

	function get_entity( $name ) {
		if ( ! array_key_exists( $name, $this->instances ) ) {
			$type = $this->get_type( $name );
			$this->instances[ $name ] = $instance = new $type();
			$instance->container = $this->container;
		}

		return $this->instances[ $name ];
	}

}
