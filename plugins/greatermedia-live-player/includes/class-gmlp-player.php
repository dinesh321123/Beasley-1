<?php


/**
 * Class GMLP_Player
 */
class GMLP_Player {

	public static function init() {

		add_action( 'wp_footer', array( __CLASS__, 'load_js' ), 50 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ), 50 );
		add_action( 'gm_live_player', array( __CLASS__, 'render_player' ) );
		add_action( 'radio_callsign', array( __CLASS__, 'get_radio_callsign' ) );
	}

	/**
	 * Helper function to call the Radio Callsign
	 */
	public static function get_radio_callsign() {

		$radio_callsign = get_option( 'gmlp_radio_callsign', '' );

		echo sanitize_text_field( $radio_callsign );

	}

	/**
	 * Render the player for the front end
	 */
	public static function render_player() {

		?>
		<div class="live-stream__player">
			<div class="live-stream__controls">
				<div id="playButton" class="live-stream__btn--play" data-action="play-live"></div>
				<div id="pauseButton" class="live-stream__btn--pause"></div>
				<div id="resumeButton" class="live-stream__btn--resume"></div>
			</div>
			<div id="live-stream__container" class="live-stream__container">
				<div id="td_container" class="live-stream__container--player"></div>
			</div>

		</div>

	<?php

	}

	/**
	 * Enqueue scripts
	 */
	public static function enqueue_scripts() {

		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

		if ( function_exists( 'gmr_streams_get_primary_stream_callsign') ) {
			$callsign = gmr_streams_get_primary_stream_callsign();
		}
		if ( function_exists( 'gmr_streams_get_primary_stream_vast_url') ) {
			$vast_url = gmr_streams_get_primary_stream_vast_url();
		}

		wp_register_script( 'load-jquery', GMLIVEPLAYER_URL . 'assets/js/src/jquery.load.js', array(), GMLIVEPLAYER_VERSION, true );
		wp_enqueue_script( 'tdplayer', GMLIVEPLAYER_URL . "assets/js/tdplayer{$postfix}.js", array( 'load-jquery' ), '2.5', true );
		wp_localize_script( 'tdplayer', 'gmr', array( 'logged_in' => is_gigya_user_logged_in(), 'callsign' => $callsign, 'streamUrl' => $vast_url, 'wpLoggedIn' => is_user_logged_in() ) );
		wp_enqueue_script( 'jquery-ui-button');
		wp_enqueue_script( 'gmlp-js', GMLIVEPLAYER_URL . "assets/js/greater_media_live_player{$postfix}.js", array( 'jquery' ), GMLIVEPLAYER_VERSION, true );
		wp_localize_script( 'gmlp-js', 'gmlp', array( 'logged_in' => is_user_logged_in() ) );
	}

	/**
	 * this script has to be loaded as Async and as shown
	 *
	 * @todo find a way to add this to wp_enqueue_script. This seemed to be interesting - http://wordpress.stackexchange.com/questions/38319/how-to-add-defer-defer-tag-in-plugin-javascripts/38335#38335
	 *       but causes `data-dojo-config` to load after the src, which then causes the script to fail and the TD Player API will not fully load
	 */
	public static function load_js() {

		echo '<script data-dojo-config="onReady:window.tdPlayerApiReady, async: 1, tlmSiblingOfDojo: 0, deps:[\'tdapi/run\']" src="//api.listenlive.co/tdplayerapi/2.5/dojo/dojo.js"></script>';

	}

}

GMLP_Player::init();