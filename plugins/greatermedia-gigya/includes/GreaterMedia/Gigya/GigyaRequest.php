<?php

namespace GreaterMedia\Gigya;

class GigyaRequest extends \GSRequest {

	public function __construct( $api_key, $secret_key, $method ) {
		$settings   = $this->get_member_query_settings();
		$api_key    = $settings['gigya_api_key'];
		$secret_key = $settings['gigya_secret_key'];

		parent::__construct( $api_key, $secret_key, $method );
	}

	public function get_member_query_settings() {
		$defaults = array(
			'gigya_api_key'    => '',
			'gigya_secret_key' => '',
		);

		$defaults = json_encode( $defaults );
		$settings = get_option( 'member_query_settings', $defaults );
		$settings = json_decode( $settings, true );

		return $settings;
	}

}
