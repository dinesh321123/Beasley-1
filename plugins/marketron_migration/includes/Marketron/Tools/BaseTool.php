<?php

namespace Marketron\Tools;

/*
 * TODO: Simplify this into a single class
 *
 * Original design called for multiple tool implementations
 * but the only thing that actually needed customization was the tool name
 * and path.
 *
 * Folding this back into one class will simplify..
 */
class BaseTool {

	public $container;
	public $sources = array();

	function get_name() {
		return 'base_tool';
	}

	function get_importer( $name ) {
		return $this->container->importer_factory->build( $name );
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

	function load( $auto_import = true ) {
		$tool_name  = $this->get_name();
		$data_files = $this->get_data_files();

		foreach ( $data_files as $data_file ) {
			if ( file_exists( $data_file ) ) {
				$short_name = basename( $data_file );
				\WP_CLI::log( "Loading Data for Marketron Tool: $tool_name ..." );
				$xml_doc = @simplexml_load_file( $data_file );

				if ( $xml_doc !== false ) {
					$this->parse( $xml_doc );

					$importer = $this->get_importer( $tool_name );

					if ( $auto_import ) {
						$importer->import_source( $xml_doc );
					}

					if ( $this->container->opts['repair'] ) {
						$this->sources[] = $xml_doc;
					}
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

	function can_destroy() {
		return true;
	}

	function destroy() {
		$this->container = null;
		unset( $this->container );

		$this->sources = null;
		unset( $this->sources );
	}

}
