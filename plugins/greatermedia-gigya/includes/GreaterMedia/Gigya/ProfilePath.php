<?php

namespace GreaterMedia\Gigya;

class ProfilePath {

	static $instance = null;
	static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new ProfilePath();
		}

		return self::$instance;
	}

	public $endpoint = 'members';

	function path_for( $action_name, $params = null ) {
		$path  = "/{$this->endpoint}/{$action_name}";

		if ( is_null( $params ) ) {
			return $path;
		} else {
			return $path . '?' . http_build_query( $params );
		}
	}

}
