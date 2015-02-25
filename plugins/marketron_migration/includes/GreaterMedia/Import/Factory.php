<?php

namespace GreaterMedia\Import;

class Factory {

	public $types_map = array(
		'feed' => '\GreaterMedia\Import\Feed',
	);

	public $container;
	public $instances = array();

	function __construct( $container ) {
		$this->container = $container;
	}

	function build( $tool_name ) {
		if ( ! array_key_exists( $tool_name, $this->instances ) ) {
			$type                          = $this->types_map[ $tool_name ];
			$instance                      = new $type( $this->container );
			$this->instances[ $tool_name ] = $instance;
		}

		return $this->instances[ $tool_name ];
	}

}
