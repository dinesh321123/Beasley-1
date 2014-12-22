<?php

namespace GreaterMedia\LiveFyre\Ajax;

use GreaterMedia\Gigya\Ajax\AjaxHandler;
use Livefyre\Livefyre;

class ChangeLiveFyreSettings extends AjaxHandler {

	function get_action() {
		return 'change_livefyre_settings';
	}

	function run( $params ) {
		$settings = $params['settings'];
		$verified = $this->verify_settings( $settings );

		if ( $verified ) {
			$this->save_settings( $settings );
			return true;
		} else {
			return false;
		}
	}

	function verify_settings( $settings ) {
		$network_name = $settings['network_name'];
		$network_key  = $settings['network_key'];
		$site_id      = $settings['site_id'];
		$site_key     = $settings['site_key'];

		$network    = Livefyre::getNetwork( $network_name, $network_key );
		$site       = $network->getSite( $site_id, $site_key );
		$collection = $site->buildCommentsCollection( 'test-title', '000001', 'http://wmgk.com/test/000001' );

		return true;
	}

	function save_settings( $settings ) {
		$json = json_encode( $settings );
		update_option( 'livefyre_settings', $json );
	}

}
