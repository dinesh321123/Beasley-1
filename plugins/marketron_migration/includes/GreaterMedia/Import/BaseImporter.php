<?php

namespace GreaterMedia\Import;

class BaseImporter {

	public $container;

	function __construct( $container ) {
		$this->container = $container;
	}

	function get_config() {
		return $this->container->config;
	}

	function get_site_option( $name ) {
		return $this->get_config()->get_site_option( $name );
	}

	function get_tool_name() {
		return 'base_tool';
	}

	function get_tool() {
		return $this->container->tool_factory->build(
			$this->get_tool_name()
		);
	}

	function get_entity( $name ) {
		return $this->container->entity_factory->build( $name );
	}

	function get_table( $name ) {
		return $this->container->table_factory->build( $name );
	}

	function get_mappings() {
		return $this->container->mappings;
	}

	function can_import( $blog_id ) {
		return $this->get_mappings()->can_import( $blog_id );
	}

	function can_import_marketron_name( $name, $tool_name = null ) {
		return $this->get_mappings()->can_import_marketron_name( $name, $tool_name );
	}

	function import() {
		$tool    = $this->get_tool();
		$sources = $tool->sources;

		foreach ( $sources as $source ) {
			$this->import_source( $source );
		}
	}

	function import_source( $source ) {

	}

	function import_string( $element ) {
		$string = (string) $element;
		return trim( $string );
	}

	function import_bool( $element ) {
		$string = $this->import_string( $element );
		return filter_var( $string, FILTER_VALIDATE_BOOLEAN );
	}

	function to_datetime( $timestamp ) {
		return $this->container->entity_factory->get_entity( 'post' )->to_datetime( $timestamp );
	}

	function can_destroy() {
		return true;
	}

	function destroy() {
		$this->container = null;
		unset( $this->container );
	}

	function can_import_by_time( $post ) {
		$created_on      = $post['created_on'];
		$created_on_time = strtotime( $created_on );
		$time_limit      = $this->get_config()->get_time_limit();

		if ( $time_limit === false ) {
			return true;
		}

		$time_limit_time = strtotime( $time_limit );

		if ( $created_on_time >= $time_limit_time  ) {
			return true;
		} else {
			//\WP_CLI::log( 'Skipped on Time: ' . $post['created_on'] );
			return false;
		}
	}

}
