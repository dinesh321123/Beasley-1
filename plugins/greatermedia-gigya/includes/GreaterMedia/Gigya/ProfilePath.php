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

	function path_for( $action_name, $params = array() ) {
		$path  = "/{$this->endpoint}/{$action_name}";

		if ( ( $action_name === 'login' || $action_name === 'logout' ) && ! array_key_exists( 'dest', $params ) ) {
			global $wp;
			$params['dest'] = '/' . trim( $wp->request, '/' );
		}

		if ( empty( $params ) ) {
			return $path;
		} else {
			return $path . '?' . http_build_query( $params );
		}
	}

}
