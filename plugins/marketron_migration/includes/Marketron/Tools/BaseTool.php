<?php

namespace Marketron\Tools;

class BaseTool {

	public $container;

	function __construct( $container ) {
		$this->container  = $container;
		$this->downloader = $container->downloader;
		$this->config     = $container->config;
	}

	function get_name() {
		return 'base_tool';
	}

	function get_data_filename() {
		return 'base_tool.XML';
	}

	function get_cmd_option( $name ) {
		return $this->container->opts[ $name ];
	}

	function can_import( $marketron_id ) {
		return $this->container->mapping_collection->can_import( $marketron_id );
	}

	function get_post_type_for( $marketron_id, $has_attachment ) {
		return $this->container->mapping_collection->get_post_type_for(
			$marketron_id, $has_attachment
		);
	}

	function get_data_filepath() {
		$migration_cache_dir = $this->get_cmd_option( 'migration_cache_dir' );
		$xml_dir             = $migration_cache_dir . '/marketron_export';
		$filename            = $this->get_data_filename();
		$filename            = preg_replace( '/.(XML|xml)$/', '_formatted.xml', $filename );

		return $xml_dir . '/' . $filename;
	}

	function load() {
		$name = $this->get_name();
		$file = $this->get_data_filepath();

		if ( file_exists( $file ) ) {
			\WP_CLI::log( "Loading Tool Data: $name ..." );
			$xml_element = simplexml_load_file( $file );

			if ( $xml_element !== false ) {
				$this->parse( $xml_element );
			} else {
				\WP_CLI::error( "Invalid XML for Tool($name) - $file" );
			}
		} else {
			\WP_CLI::error( "Failed to import $name - $file"  );
		}
	}

	function parse( $xml_element ) {
		// abstract
	}

	function parse_fields( $element, $fields ) {
		$record     = array();
		$attributes = $element->attributes();

		foreach ( $fields as $field_name ) {
			if ( isset( $attributes[ $field_name ] ) ) {
				$value = $this->parse_value( $field_name, $attributes );
				$record[ $field_name ] = $value;
			}
		}

		return $record;
	}

	function parse_value( $field_name, $attributes ) {
		$value = $attributes[ $field_name ];
		$value = strval( $value );
		$value = trim( $value );

		if ( preg_match( '/DateTime$/', $field_name ) ) {
			$value = new \DateTime( $value );
		} else if ( preg_match( '/Filepath$/', $field_name ) ) {
			$value = $this->parse_filepath( $value );
		}

		return $value;
	}

	function parse_filepath( $filepath ) {
		$filename = str_replace( '\\', '/', $filepath );
		$filename = urldecode( $filename ); // for filenames with spaces
		$filename = str_replace( ' ', '%20', $filename );
		$filename = str_replace( '&amp;', '&', $filename );
		$filename = str_replace( '&mdash;', '—', $filename );

		return $filename;
	}

	function parse_collection( $parser_func, $elements ) {
		$items = array();
		$callable = array( $this, $parser_func );

		foreach ( $elements as $element ) {
			$item = call_user_func( $callable, $element );

			if ( $item !== false ) {
				$items[] = $item;
			}
		}

		return $items;
	}

}
