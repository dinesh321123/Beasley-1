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

	function to_datetime( $timestamp ) {
		return $this->container->entity_factory->get_entity( 'post' )->to_datetime( $timestamp );
	}

}
