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
				<div id="playButton" class="live-stream__btn--play" data-station="<?php do_action( 'radio_callsign' ); ?>" data-action="play-live"></div>
				<div id="pauseButton" class="live-stream__btn--pause" data-station="<?php do_action( 'radio_callsign' ); ?>"></div>
				<div id="resumeButton" class="live-stream__btn--resume" data-station="<?php do_action( 'radio_callsign' ); ?>"></div>
			</div>

			<!-- Player placeholder -->
			<div id="td_container" class="live-stream__player--container"></div>
		</div>

	<?php

	}

	/**
	 * Enqueue scripts
	 */
	public static function enqueue_scripts() {

		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_script( 'pjax', GMLIVEPLAYER_URL . 'assets/js/vendor/jquery.pjax.js', array(), '1.9.2', false );
		wp_enqueue_script( 'jquery-cookie', GMLIVEPLAYER_URL . 'assets/js/src/jquery.cookie.js', array(), GMLIVEPLAYER_VERSION, false );
		wp_register_script( 'load-jquery', GMLIVEPLAYER_URL . 'assets/js/src/jquery.load.js', array(), GMLIVEPLAYER_VERSION, true );
		wp_enqueue_script( 'tdplayer', GMLIVEPLAYER_URL . 'assets/js/vendor/td-player/tdplayer.js', array( 'load-jquery' ), '2.5', true );
		//wp_enqueue_script( 'tdplayer-api', GMLIVEPLAYER_URL . 'assets/js/vendor/td-player/tdplayer-api.js', array(), '2.5', true );
		wp_enqueue_script( 'jquery-ui-button');
		wp_enqueue_script( 'gmlp-js', GMLIVEPLAYER_URL . "assets/js/greater_media_live_player{$postfix}.js", array( 'jquery' ), GMLIVEPLAYER_VERSION, true );
		//wp_enqueue_style( 'gmlp-styles', GMLIVEPLAYER_URL . "assets/css/greater_media_live_player{$postfix}.css", array(), GMLIVEPLAYER_VERSION );
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