<?php

namespace Marketron;

class MappingCollection {

	public $container;
	public $mappings           = array();
	public $shows              = array();
	public $author_names       = array();
	public $podcasts           = array();
	public $show_is_collection = false;
	public $shows_map          = array();

	function load() {
		$config       = $this->container->config;
		$mapping_file = $config->get_mapping_file();

		\WP_CLI::log( "Loading Mapping File: $mapping_file" );

		if ( file_exists( $mapping_file ) ) {
			$file           = fopen( $mapping_file, 'r' );
			$this->parse( $file );
			//$this->load_shows();
		} else {
			\WP_CLI::error( "Mapping file not found: $mapping_file" );
		}
	}

	function import() {
		$this->import_authors();
		$this->import_categories();
		$this->import_shows();
		$this->import_podcasts();
		$this->import_tags();
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

	// DEPRECATED
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

	// DEPRECATED
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
			} else if ( $fields[0] === 'Marketron Tool' ){
				if ( strpos( trim( $fields[6] ), 'Collection' ) !== false ) {
					$this->show_is_collection = true;
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

			if ( strpos( $flag_field, 'DO NOT IMPORT' ) === false ) {
				$mapping->can_import = true;

				$audio_flag_field = strtolower( trim( $fields[3] ) );
				$target_post_type = $this->parse_post_type_name( $fields[4] );

				if ( strpos( $audio_flag_field, 'yes' ) === 0 ) {
					//error_log( print_r( $fields ) );
					$mapping->audio_present_post_type = $target_post_type;
				} else if ( strpos( $audio_flag_field, 'no' ) === 0 ) {
					$mapping->audio_absent_post_type = $target_post_type;
				} else if ( strpos( $audio_flag_field, 'either' ) === 0 ) {
					$mapping->audio_present_post_type = $target_post_type;
					$mapping->audio_absent_post_type = $target_post_type;
				}

				$mapping->wordpress_author_name  = trim( $fields[5] );
				$mapping->wordpress_show_name    = $this->sanitize_mapping( $fields[6] );
				$mapping->wordpress_category     = trim( $fields[7] );
				$mapping->wordpress_podcast_name = $this->sanitize_mapping( $fields[8] );
			} else {
				$mapping->can_import = false;
			}

			return $mapping;
		} else {
			return false;
		}
	}

	function sanitize_mapping( $text ) {
		$text = trim( $text );

		switch ( $text ) {
			case 'NA':
			case 'N/A':
				$text = null;
				break;

			default:
		}

		return $text;
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
		if ( array_key_exists( strval( $marketron_id ), $this->mappings ) ) {
			return $this->mappings[ $marketron_id ];
		} else {
			return null;
		}
	}

	function has_show( $show_author ) {
		$show_author = trim( $show_author );
		return array_key_exists( $show_author, $this->shows );
	}

	function get_show_mapping( $show_author ) {
		$marketron_id = $this->shows[ $show_author ];
		return $this->get_mapping( $marketron_id );
	}

	function has_podcast( $podcast_name ) {
		$podcast_name = trim( $podcast_name );
		return array_key_exists( $podcast_name, $this->podcasts );
	}

	function get_podcast( $podcast_name ) {
		$podcast_name = trim( $podcast_name );

		foreach ( $this->mappings as $mapping ) {
			if ( ! empty( $mappings->wordpress_podcast_name ) ) {
				if ( $mappings->wordpress_podcast_name === $podcast_name ) {
					return $mapping;
				}
			}
		}

		return null;
	}

	function get_author_names() {
		$author_names = array();

		foreach ( $this->mappings as $mapping ) {
			$author_name = trim( $mapping->wordpress_author_name );

			if ( ! empty( $author_name ) ) {
				$author_names[] = $author_name;
			}
		}

		return array_unique( $author_names );
	}

	function import_authors() {
		$authors = $this->get_author_names();
		$entity  = $this->container->entity_factory->build( 'author' );

		foreach ( $authors as $author_name ) {
			$author = array(
				'display_name' => $author_name,
			);

			$entity->add( $author );
		}

		$this->author_names = $authors;
	}

	function get_category_names() {
		$categories = array();

		foreach ( $this->mappings as $mapping ) {
			$category = trim( $mapping->wordpress_category );

			if ( ! empty( $category ) ) {
				$categories[] = $category;
			}
		}

		return $categories;
	}

	function import_categories() {
		$categories = $this->get_category_names();
		$entity  = $this->container->entity_factory->build( 'category' );

		foreach ( $categories as $category_name ) {
			$entity->add( $category_name );
		}
	}

	function import_shows() {
		$entity = $this->get_entity( 'show' );
		$shows_map = array();

		foreach ( $this->mappings as $mapping ) {
			$show_name = $mapping->wordpress_show_name;
			if ( ! empty( $show_name ) && ! isset( $shows_map[ $show_name ] ) ) {
				$show = array(
					'show_name'   => $show_name,
					'show_author' => $mapping->wordpress_author_name,
				);

				if ( ! $this->show_is_collection ) {
					$entity->add( $show );
				}
				$shows_map[ $show_name ] = true;
			}
		}

		$this->shows_map = $shows_map;
	}

	function import_podcasts() {
		$entity       = $this->get_entity( 'podcast' );
		$podcasts_map = array();

		foreach ( $this->mappings as $mapping ) {
			$podcast_name = trim( $mapping->wordpress_podcast_name );

			if ( ! empty( $podcast_name ) && ! isset( $podcasts_map[ $podcast_name ] ) ) {
				$podcast = array(
					'podcast_name' => $podcast_name,
					'podcast_author' => $mapping->wordpress_author_name,
					'podcast_show' => $mapping->wordpress_show_name,
				);

				$entity->add( $podcast );
				$podcasts_map[ $podcast_name ] = $mapping->wordpress_show_name;
			}
		}

		$this->podcasts = $podcasts_map;
	}

	function import_tags() {
		$tags_file  = $this->container->config->get_tags_file();
		if ( ! file_exists( $tags_file ) ) {
			\WP_CLI::warning( "Skipping Tags Import: $tags_file" );
			return;
		}

		$file       = fopen( $tags_file, 'r' );
		$fields     = fgetcsv( $file, 0, ',', '"' );
		$total_tags = count( file( $tags_file ) ) - 1;
		$notify     = new \WordPress\Utils\ProgressBar( "Importing $total_tags Tags ", $total_tags );
		$entity     = $this->get_entity( 'tag' );

		while ( $fields !== false ) {
			if ( is_numeric( $fields[0] ) ) {
				$tag_name = $fields[1];
				$entity->add( $tag_name );
			}

			$fields = fgetcsv( $file, 0, ',', '"' );
			$notify->tick();
		}

		$notify->finish();
	}

	function get_entity( $name ) {
		return $this->container->entity_factory->build( $name );
	}

	function get_table( $name ) {
		return $this->container->table_factory->build( $name );
	}

	function has_author( $name ) {
		return in_array( $name, $this->author_names );
	}

	function get_show_for_author( $name ) {
		foreach ( $this->mappings as $mapping ) {
			if ( $mapping->wordpress_author_name === $name ) {
				return $mapping->wordpress_show_name;
			}
		}

		return null;
	}

	function get_show_from_categories( $categories ) {
		foreach ( $categories as $category ) {
			foreach ( $this->mappings as $mapping ) {
				if ( ! empty( $mapping->wordpress_category ) ) {
					if ( strpos( $category, $mapping->wordpress_category ) !== false ) {
						return $mapping->wordpress_show_name;
					}
				}
			}
		}

		return null;
	}

	function get_show_from_author_names( $author_names ) {
		foreach ( $author_names as $author_name ) {
			foreach ( $this->mappings as $mapping ) {
				if ( ! empty( $mapping->wordpress_author_name ) ) {
					if ( strpos( $author_name, $mapping->wordpress_author_name ) !== false ) {
						return $mapping->wordpress_show_name;
					}
				}
			}
		}

		return null;
	}

	function get_matched_authors( $string ) {
		$matches = array();

		foreach ( $this->author_names as $author ) {
			if ( strpos( $string, $author ) !== false ) {
				$matches[] = $author;
			} else if ( strpos( $author, ' ' ) !== false ) {
				$sub_authors = explode( ' ', $author );
				$sub_authors = array_map( 'trim', $sub_authors );

				foreach ( $sub_authors as $sub_author ) {
					if ( strpos( $string, $sub_author . ' ' ) !== false ) {
						$matches[] = $author;
					}
				}
			}
		}

		return $matches;
	}

	function get_podcast_for_show( $show_name ) {
		foreach ( $this->podcasts as $podcast_name => $podcast_show_name ) {
			if ( $show_name === $podcast_show_name ) {
				return $podcast_name;
			}
		}

		return null;
	}

	function get_podcast_from_marketron_name( $marketron_name ) {
		foreach ( $this->mappings as $mapping ) {
			if ( $mapping->marketron_name === $marketron_name ) {
				return $mapping->wordpress_podcast_name;
			}
		}

		return null;
	}

	function has_collection( $collection ) {
		if ( $this->show_is_collection ) {
			return array_key_exists( $collection, $this->shows_map );
		} else {
			return false;
		}
	}

	function get_collection_from_marketron_name( $marketron_name ) {
		if ( $this->show_is_collection ) {
			foreach ( $this->mappings as $mapping ) {
				if ( $mapping->marketron_name === $marketron_name ) {
					return $mapping->wordpress_show_name;
				}
			}

			return null;
		} else {
			return null;
		}
	}

	function get_category_from_marketron_name( $marketron_name ) {
		if ( $this->show_is_collection ) {
			foreach ( $this->mappings as $mapping ) {
				if ( $mapping->marketron_name === $marketron_name ) {
					return $mapping->wordpress_category;
				}
			}

			return null;
		} else {
			return null;
		}
	}

}
