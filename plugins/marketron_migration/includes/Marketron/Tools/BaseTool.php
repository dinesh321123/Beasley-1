<?php

namespace Marketron\Tools;

class BaseTool {

	public $container;
	public $sources = array();

	function get_name() {
		return 'base_tool';
	}

	function get_data_filename() {
		return 'base_tool.XML';
	}

	function get_config() {
		return $this->container->config;
	}

	function get_cmd_option( $name ) {
		return $this->container->opts[ $name ];
	}

	function get_site_option( $name ) {
		return $this->get_config()->get_site_option( $name );
	}

	function get_site_dir() {
		return $this->get_config()->get_site_dir();
	}

	function can_import( $marketron_id ) {
		return $this->container->mapping_collection->can_import( $marketron_id );
	}

	function get_post_type_for( $marketron_id, $has_attachment ) {
		return $this->container->mapping_collection->get_post_type_for(
			$marketron_id, $has_attachment
		);
	}

	function get_formatted_data_filename() {
		$filename = $this->get_data_filename();
		return preg_replace( '/.(XML|xml)$/', '_formatted.xml', $filename );
	}

	function get_data_files() {
		$files    = array();
		$filename = $this->get_formatted_data_filename();

		foreach ( $this->get_data_file_dirs() as $dir ) {
			$files[] = $dir . '/' . $filename;
		}

		return $files;
	}

	function get_data_file_dirs() {
		return $this->get_config()->get_data_file_dirs_for_tool( $this->get_name() );
	}

	function load() {
		$tool_name  = $this->get_name();
		$data_files = $this->get_data_files();

		foreach ( $data_files as $data_file ) {
			if ( file_exists( $data_file ) ) {
				\WP_CLI::log( "Loading Data for Marketron $tool_name ( $data_file ) ..." );
				$xml_doc = @simplexml_load_file( $data_file );

				if ( $xml_doc !== false ) {
					$this->parse( $xml_doc );
					$this->sources[] = $xml_doc;
				} else {
					\WP_CLI::warning( "Invalid XML for Tool($tool_name) - $data_file" );
				}
			} else {
				\WP_CLI::error( "Failed to import $tool_name - $data_file"  );
			}
		}
	}

	function parse( $xml_doc ) {
		// do optional custom parsing here
	}

	/*
	function parse_fields( $element, $fields ) {
		$record     = array();
		$attributes = $element->attributes();

		foreach ( $fields as $field_name ) {
			if ( isset( $attributes[ $field_name ] ) ) {
				$value = $this->parse_value( $field_name, $attributes );
			} else {
				$value = null;
			}

			$record[ $field_name ] = $value;
		}

		return $record;
	}

	function parse_value( $field_name, $attributes ) {
		$value = $attributes[ $field_name ];
		$value = (string) $value;
		$value = trim( $value );

		if ( preg_match( '/DateTime$/', $field_name ) ) {
			$value = $value;
			//$value = new \DateTime( $value );
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
*/

}
