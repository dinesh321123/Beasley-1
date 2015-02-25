<?php

namespace Marketron;

class MappingCollection {

	public $mappings = array();

	function load( $mapping_file ) {
		if ( file_exists( $mapping_file ) ) {
			$file           = fopen( $mapping_file, 'r' );
			$this->parse( $file );
		} else {
			\WP_CLI::error( "Mapping file not found: $mapping_file" );
		}
	}

	function parse( $file ) {
		$fields         = $this->read_line( $file );
		$this->mappings = array();

		while ( $fields !== false ) {
			$mapping = $this->parse_fields( $fields );
			if ( $mapping !== false ) {
				$marketron_id = strval( $mapping->marketron_id );
				$this->mappings[ $marketron_id ] = $mapping;
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

}
