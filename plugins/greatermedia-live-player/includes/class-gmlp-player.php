<?php


/**
 * Class GMLP_Player
 */
class GMLP_Player {

	public static function init() {

		add_action( 'wp_footer', array( __CLASS__, 'load_js' ), 50 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ), 50 );
		add_action( 'wp_footer', array( __CLASS__, 'render_player' ) );
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
		<div class="gmlp-nav">

			<button class="gmlp-nav-toggle"></button>

			<div class="gmlp-menu">

				<div class="container">
					<div id="gmlp-actions">
						<div id="playButton" class="gmlp-audio-button play" data-station="<?php do_action( 'radio_callsign' ); ?>"></div>
						<button id="pauseButton" class="gmlp-audio-button" data-station="<?php do_action( 'radio_callsign' ); ?>"><i class="fa fa-pause"></i></button>
						<button id="resumeButton" class="gmlp-audio-button" data-station="<?php do_action( 'radio_callsign' ); ?>"><i class="fa fa-play-circle-o"></i></button>
					</div>

					<div id="gmlp-live">
						<div id="nowPlaying">
							<div id="trackInfo">
							</div>
							<div id="npeInfo"></div>
						</div>
					</div>

					<!-- Player placeholder -->
					<div id="td_container"></div>

				</div>

			</div>

		</div>

	<?php

	}

	/**
	 * Enqueue scripts
	 */
	public static function enqueue_scripts() {

		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_script( 'pjax', GMLIVEPLAYER_URL . 'assets/js/vendor/pjax-standalone.min.js', array(), '0.1.3', false );
		wp_enqueue_script( 'gmlp-js', GMLIVEPLAYER_URL . "assets/js/greater_media_live_player{$postfix}.js", array( 'jquery' ), GMLIVEPLAYER_VERSION, true );
		wp_enqueue_script( 'jquery-cookie', GMLIVEPLAYER_URL . 'assets/js/src/jquery.cookie.js', array(), GMLIVEPLAYER_VERSION, false );
		wp_enqueue_script( 'load-jquery', GMLIVEPLAYER_URL . 'assets/js/src/jquery.load.js', array(), GMLIVEPLAYER_VERSION, true );
		wp_enqueue_script( 'tdplayer', GMLIVEPLAYER_URL . 'assets/js/vendor/td-player/tdplayer.js', array( 'jquery' ), '2.5', true );
		wp_enqueue_script( 'tdplayer-api', GMLIVEPLAYER_URL . 'assets/js/vendor/td-player/tdplayer-api.js', array(), '2.5', true );
		wp_enqueue_style( 'gmlp-styles', GMLIVEPLAYER_URL . "assets/css/greater_media_live_player{$postfix}.css", array(), GMLIVEPLAYER_VERSION );

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