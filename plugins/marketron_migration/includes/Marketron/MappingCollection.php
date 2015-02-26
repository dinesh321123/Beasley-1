<?php

namespace Marketron;

class MappingCollection {

	public $mappings = array();
	public $shows = array();

	function load( $mapping_file ) {
		if ( file_exists( $mapping_file ) ) {
			$file           = fopen( $mapping_file, 'r' );
			$this->parse( $file );
			$this->load_shows();
		} else {
			\WP_CLI::error( "Mapping file not found: $mapping_file" );
		}
	}

	function load_shows() {
		foreach ( $this->mappings as $mapping ) {
			if ( $mapping->wordpress_show_name ) {
				$show = get_page_by_title( $mapping->wordpress_show_name, ARRAY_A, 'show' );
				if ( is_null( $show ) ) {
					$this->create_show( $mapping->wordpress_show_name );
				}
			}

			if ( $mapping->wordpress_podcast_name ) {
				$podcast = get_page_by_title( $mapping->wordpress_podcast_name, ARRAY_A, 'podcast' );
				if ( is_null( $podcast ) ) {
					$podcast_id = $this->create_podcast( $mapping->wordpress_podcast_name );
				} else {
					$podcast_id = $podcast['ID'];
				}

				$show_taxonomy = get_term_by( 'name', $mapping->wordpress_show_name, '_shows', ARRAY_A );
				if ( ! is_wp_error( $show_taxonomy ) ) {
					$result = wp_set_object_terms( $podcast_id, array( intval( $show_taxonomy['term_id'] ) ), '_shows', true );
				}
			}
		}
	}

	function create_show( $show_name ) {
		$post = array(
			'post_type'     => 'show',
			'post_status'   => 'publish',
			'post_title'    => $show_name,
			'post_content'  => '',
		);

		$post_id = wp_insert_post( $post );
		\WP_CLI::log( "Created Show: $show_name - $post_id" );

		update_post_meta( $post_id, 'show_homepage', '1' );
		update_post_meta( $post_id, 'show_homepage_galleries', '1' );
		update_post_meta( $post_id, 'show_homepage_podcasts', '1' );
		update_post_meta( $post_id, 'show_homepage_videos', '1' );

		return $post_id;
	}

	function create_podcast( $podcast_name ) {
		$post = array(
			'post_type'     => 'podcast',
			'post_status'   => 'publish',
			'post_title'    => $podcast_name,
			'post_content'  => '',
		);

		$post_id = wp_insert_post( $post );
		\WP_CLI::log( "Created Podcast: $podcast_name - $post_id" );

		return $post_id;
	}

	function parse( $file ) {
		$fields         = $this->read_line( $file );
		$this->mappings = array();

		while ( $fields !== false ) {
			$mapping = $this->parse_fields( $fields );
			if ( $mapping !== false ) {
				$marketron_id = strval( $mapping->marketron_id );
				$this->mappings[ $marketron_id ] = $mapping;

				if ( $mapping->wordpress_author_name ) {
					$this->shows[ $mapping->wordpress_author_name ] = $marketron_id;
				}
			}

			$fields = $this->read_line( $file );
		}

		//var_dump( $this->mappings );
	}

	function parse_fields( $fields ) {
		//error_log( print_r( $fields, true ) );
		$marketron_id = intval( $fields[2] );

		if ( $marketron_id !== 0 ) {
			if ( ! $this->has_mapping( $marketron_id ) ) {
				$mapping = new Mapping();
				$mapping->marketron_tool_name = $this->parse_marketron_tool_name( $fields[0] );
				$mapping->marketron_name = $fields[1];
				$mapping->marketron_id = $marketron_id;
			} else {
				$mapping = $this->get_mapping( $marketron_id );
			}

			$flag_field = trim( $fields[3] );

			if ( $flag_field !== 'DO NOT IMPORT' ) {
				$mapping->can_import = true;

				$audio_flag_field = strtolower( trim( $fields[3] ) );
				$target_post_type = $this->parse_post_type_name( $fields[4] );

				if ( $audio_flag_field === 'yes' ) {
					//error_log( print_r( $fields ) );
					$mapping->audio_present_post_type = $target_post_type;
				} else if ( $audio_flag_field === 'no' ) {
					$mapping->audio_absent_post_type = $target_post_type;
				} else if ( $audio_flag_field === 'either' ) {
					$mapping->audio_present_post_type = $target_post_type;
					$mapping->audio_absent_post_type = $target_post_type;
				}

				$mapping->wordpress_author_name  = trim( $fields[5] );
				$mapping->wordpress_show_name    = trim( $fields[6] );
				$mapping->wordpress_category     = trim( $fields[7] );
				$mapping->wordpress_podcast_name = trim( $fields[8] );
			} else {
				$mapping->can_import = false;
			}

			return $mapping;
		} else {
			return false;
		}
	}

	function parse_marketron_tool_name( $name ) {
		$name = strtolower( trim( $name ) );

		switch ( $name ) {
			case 'blogs':
				return 'blog';

			case 'channels':
				return 'channel';

			case 'feeds':
				return 'feed';

			case 'podcasts':
				return 'podcast';

			default:
				\WP_CLI::error( "Unknown Marketron Tool Name - $name" );
		}
	}

	function parse_post_type_name( $name ) {
		$name = strtolower( trim( $name ) );

		switch ( $name ) {
			case 'post':
				return 'post';

			case 'podcast episode':
				return 'podcast_episode';

			default:
				\WP_CLI::error( "Unknown WordPress Target Post Type - $name" );
		}
	}

	function read_line( $file ) {
		return fgetcsv( $file, 0, ',', '"' );
	}

	function can_import( $marketron_id ) {
		if ( $this->has_mapping( $marketron_id ) ) {
			$mapping = $this->get_mapping( $marketron_id );
			return $mapping->can_import;
		} else {
			// can_import is based on blacklisting of types
			// anything else is a post or a custom post type
			return true;
		}
	}

	function get_post_type_for( $marketron_id, $has_attachment ) {
		if ( $this->has_mapping( $marketron_id ) ) {
			$mapping = $this->get_mapping( $marketron_id );

			if ( $has_attachment ) {
				return $mapping->audio_present_post_type;
			} else {
				return $mapping->audio_absent_post_type;
			}
		} else {
			// if no custom mapping is provided default to post
			// or the custom post type tool/importer handles this directly
			// Eg:- Contest does not ask for the target post type
			return 'post';
		}
	}

	function has_mapping( $marketron_id ) {
		return array_key_exists( strval( $marketron_id ), $this->mappings );
	}

	function get_mapping( $marketron_id ) {
		return $this->mappings[ $marketron_id ];
	}

	function has_show( $show_author ) {
		$show_author = trim( $show_author );
		return array_key_exists( $show_author, $this->shows );
	}

	function get_show_mapping( $show_author ) {
		$marketron_id = $this->shows[ $show_author ];
		return $this->get_mapping( $marketron_id );
	}

}
