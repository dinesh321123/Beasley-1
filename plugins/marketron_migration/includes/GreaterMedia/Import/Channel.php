<?php

namespace GreaterMedia\Import;

class Channel extends BaseImporter {

	function get_tool_name() {
		return 'channel';
	}

	function import_source( $source ) {
		$channels     = $this->channels_from_source( $source );

		foreach ( $channels as $channel ) {
			$channel_id = $this->import_string( $channel['ChannelID'] );
			$channel_name = $this->import_string( $channel['ChannelTitle'] );

			if ( $this->can_import( $channel_id ) ) {
				$this->import_channel( $channel );
			} else {
				\WP_CLI::log( "    Excluded Channel: $channel_name" );
			}
		}
	}

	function import_channel( $channel ) {
		$channel_name = $this->import_string( $channel['ChannelTitle'] );
		$channel_id   = $this->import_string( $channel['ChannelID'] );

		$mapping      = $this->container->mappings->get_mapping_by_name( $channel_name, 'channel' );
		$stories      = $this->stories_from_channel( $channel );
		$total        = count( $stories );
		$msg          = "Importing $total Stories from Channel - $channel_name";
		$progress_bar = new \WordPress\Utils\ProgressBar( $msg, $total );
		$entity       = $this->get_entity( 'blog' );

		$categories = $this->categories_from_channel( $channel );

		foreach ( $stories as $story ) {
			$blog               = $this->blog_from_story( $story );
			$blog['categories'] = $categories;

			if ( ! empty( $mapping ) && ! empty( $mapping->wordpress_show_name ) ) {
				$blog['shows'] = array( $mapping->wordpress_show_name );

				if ( ! empty( $mapping->wordpress_categories ) ) {
					foreach ( $mapping->wordpress_categories as $wordpress_category ) {
						$blog['categories'][] = $wordpress_category;
					}
				}
			}

			$entity->add( $blog );

			$progress_bar->tick();
		}

		$progress_bar->finish();
	}

	function channels_from_source( $source ) {
		return $source->Channel;
	}

	function stories_from_channel( $channel ) {
		return $channel->Story;
	}

	function categories_from_channel( $channel ) {
		$channel_name = $this->import_string( $channel['ChannelTitle'] );
		return array( $channel_name );
	}

	function blog_from_story( $story ) {
		$blog_title     = ucwords( $this->import_string( $story['Headline'] ) );
		$blog_content   = $this->import_string( $story->StoryText );
		$featured_image = $this->featured_image_from_story( $story );

		$blog = array(
			'post_title'   => $blog_title,
			'post_content' => $blog_content,
			'created_on'   => $this->import_string( $story['StoryDate'] ),
			'marketron_id' => $this->import_string( $story['StoryID'] ),
		);

		if ( ! empty( $featured_image ) ) {
			$blog['featured_image'] = $featured_image;
		}

		return $blog;
	}

	function featured_image_from_story( $story ) {
		$featured_image = $this->import_string( $story['HeadlineImageFilename'] );
		return $featured_image;
	}

}
