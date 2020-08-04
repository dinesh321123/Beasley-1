<?php
/**
 * Sets up settings page and shortcode for Second Street
 */

namespace Bbgi\Integration;

class FeatureVideo extends \Bbgi\Module {
	public function stn_barker_callback() {
		$stn_barker_id = get_option( 'stn_barker_id', '' );
		return sprintf( '<div class="stnbarker" data-fk="%s"></div>', $stn_barker_id ) ;
	}

	public function stn_incontent_callback() {
		$stn_inarticle_id = get_option( 'stn_inarticle_id', '' );
		return sprintf( '<div class="stnplayer" data-fk="%s"></div>', $stn_inarticle_id );
	}

	public function verizon_incontent_callback() {
		$verizon_pid = get_option( 'verizon_pid', '' );
		return sprintf( '<div class="verizonplayer" data-pid="%s"></div>', $verizon_pid );
	}

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		$feature_video_provider = get_option( 'feature_video_provider', 'none' );

		if ( $feature_video_provider === 'stn' ) {
			add_filter( 'barker_filter', $this('stn_barker_callback') ); // Where $priority is default 10, $accepted_args is default 1.
			add_filter( 'incontentvideo_filter', $this('stn_incontent_callback') ); // Where $priority is default 10, $accepted_args is default 1.
		}

		if ( $feature_video_provider === 'verizon' ) {
			add_filter( 'incontentvideo_filter', $this('verizon_incontent_callback') ); // Where $priority is default 10, $accepted_args is default 1.
		}


	}




}
