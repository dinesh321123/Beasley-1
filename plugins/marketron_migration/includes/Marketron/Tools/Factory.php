<?php

namespace Marketron\Tools;

class Factory {

	public $container;
	public $tools = array();
	public $tools_type_map = array(
		'feed'          => '\Marketron\Tools\Feed',
		'affinity_club' => '\Marketron\Tools\AffinityClub',
	);

	function __construct( $container ) {
		$this->container = $container;
	}

	function build( $tool_name ) {
		if ( ! array_key_exists( $tool_name, $this->tools ) ) {
			$tool_type                 = $this->tools_type_map[ $tool_name ];
			$tool                      = new $tool_type( $this->container );
			$this->tools[ $tool_name ] = $tool;
		}

		return $this->tools[ $tool_name ];
	}

}
