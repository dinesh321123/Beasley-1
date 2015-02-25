<?php

namespace GreaterMedia\Import;

class BaseImporter {

	public $container;

	function __construct( $container ) {
		$this->container = $container;
	}

	function get_tool_name() {
		return 'base_tool';
	}

	function get_tool() {
		return $this->container->tool_factory->build(
			$this->get_tool_name()
		);
	}

	function import() {
	}

	function import_categories() {

	}

}
