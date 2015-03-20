<?php

namespace GreaterMedia\Import;

class VideoChannel extends BaseImporter {

	function get_tool_name() {
		return 'video_channel';
	}

	function import_source( $source ) {
		$channels     = $this->channels_from_source( $source );

		foreach ( $channels as $channel ) {
			$channel_name = $this->import_string( $channel['VideoChannelName'] );
			$this->import_channel( $channel );
		}
	}

	function import_channel( $channel ) {
		$channel_name = $this->import_string( $channel['VideoChannelName'] );
		\WP_CLI::log( "Importing Channel: $channel_name" );

		$posts        = $this->posts_from_channel( $channel );
		$total        = count( $posts );
		$msg          = "Importing $total posts from VideoChannel";
		$progress_bar = new \cli\progress\Bar( $msg, $total );
		$entity       = $this->get_entity( 'blog' );

		$categories = $this->categories_from_channel( $channel );

		foreach ( $posts as $post ) {
			$blog               = $this->blog_from_post( $post );
			$blog['categories'] = $categories;

			$entity->add( $blog );

			$progress_bar->tick();
		}

		$progress_bar->finish();
	}

	function channels_from_source( $source ) {
		return $source->VideoChannel;
	}

	function posts_from_channel( $channel ) {
		return $channel->VideoPost;
	}

	function categories_from_channel( $channel ) {
		$channel_name = $this->import_string( $channel['VideoChannelName'] );
		return array( $channel_name );
	}

	function blog_from_post( $post ) {
		$blog_title     = htmlentities( $this->import_string( $post['PostTitle'] ) );
		$blog_content   = $this->content_from_post( $post );
		$featured_image = $this->featured_image_from_post( $post );

		$blog = array(
			'post_title' => $blog_title,
			'post_content' => $blog_content['body'],
			'post_format' => $blog_content['post_format'],
			'created_on' => $this->import_string( $post['DateCreated'] ),
			'modified_on' => $this->import_string( $post['DateModified'] ),
		);

		if ( ! empty( $featured_image ) ) {
			$blog['featured_image'] = $featured_image;
		}

		return $blog;
	}

	function featured_image_from_post( $post ) {
		$featured_image = $this->import_string( $post['HeadlineImageFilename'] );
		return $featured_image;
	}

	function content_from_post( $post ) {
		$content      = $this->import_string( $post['PostText'] );
		$embed_code   = $this->import_string( $post['EmbededTag'] );
		$video_oembed = $this->video_from_embed_code( $embed_code );

		if ( $video_oembed !== false ) {
			$post_format = 'video';
			$content .= '<br/>' . $video_oembed;
		} else {
			$post_format = 'standard';
		}

		return array(
			'body' => $content,
			'post_format' => $post_format,
		);
	}

	function video_from_embed_code( $embed_code ) {
		$pattern = '[http://www\.youtube\.com/v/(\w+)]';
		preg_match( $pattern, $embed_code, $matches );

		if ( ! empty( $matches ) && count( $matches ) >= 1 ) {
			$video_id = $matches[1];
			return 'https://www.youtube.com/watch?v=' . $video_id;
		} else {
			return false;
		}
	}

}
