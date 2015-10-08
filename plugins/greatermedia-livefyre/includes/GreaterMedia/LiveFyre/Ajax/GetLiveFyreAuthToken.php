<?php

namespace GreaterMedia\LiveFyre\Ajax;

use GreaterMedia\Gigya\Ajax\AjaxHandler;
use GreaterMedia\LiveFyre\TokenBuilder;

class GetLiveFyreAuthToken extends AjaxHandler {

	function get_action() {
		return 'get_livefyre_auth_token';
	}

	function is_public() {
		return true;
	}

	function run( $params ) {
		if ( is_gigya_user_logged_in() ) {
			$builder = new TokenBuilder( $this->get_livefyre_options() );
			return $builder->get_auth_token();
		} else {
			return '';
		}
	}

	function get_livefyre_options() {
		$options = get_option( 'livefyre_settings' );

		if ( $options !== false ) {
			$options = json_decode( $options, true );
		}

		return $options;
	}

}
