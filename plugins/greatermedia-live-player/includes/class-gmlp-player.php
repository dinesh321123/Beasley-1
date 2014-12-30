<?php


/**
 * Class GMLP_Player
 */
class GMLP_Player {
	/**
	 * Popup endpoint name for player.
	 * If you change the name then also rename the template file in theme
	 * template-{$endpoint_slug}.php
	 * @var string
	 */
	public static $endpoint_slug ='listen-live';
	public static $is_loading_popup = false;
	public static function init() {

		add_action( 'wp_footer', array( __CLASS__, 'load_js' ), 50 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ), 50 );
		add_action( 'gm_live_player', array( __CLASS__, 'render_player' ) );
		add_action( 'radio_callsign', array( __CLASS__, 'get_radio_callsign' ) );

		//EP_ROOT
		add_action( 'init', array( __CLASS__, 'add_endpoint' ) );
		add_action( 'template_redirect', array( __CLASS__, 'template_redirect' ) );
	}

	public static function add_endpoint() {
		add_rewrite_endpoint( self::$endpoint_slug , EP_ROOT );
	}

	public static function template_redirect() {
		global $wp_query;

		// if this is not a request for json or it's not a singular object then bail
		if ( !isset( $wp_query->query_vars[ self::$endpoint_slug ] ) )
			return;
		//Load live stream popup player
		if ( '' !== locate_template( 'template-' . self::$endpoint_slug . '.php' ) ) {
			// yep, load the page template
			do_action( 'gmlp_player_popup_template' );
			self::$is_loading_popup = true;
			locate_template( 'template-' . self::$endpoint_slug . '.php', true );
			exit;
		} else {
			/**
			 * @todo/@fixme add default template to load
			 */
		}
	}

	function deactivate() {
		// flush rules on deactivate as well so they're not left hanging around uselessly
		flush_rewrite_rules();
	}

	function activate() {
		// ensure our endpoint is added before flushing rewrite rules
		self::add_endpoint();
		// flush rewrite rules - only do this on activation as anything more frequent is bad!
		flush_rewrite_rules();
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

		$home_url = home_url( '/' );
		wp_enqueue_script( 'cookies-js' );
		wp_register_script( 'load-jquery', GMLIVEPLAYER_URL . 'assets/js/src/jquery.load.js', array(), GMLIVEPLAYER_VERSION, true );
		wp_enqueue_script( 'tdplayer', GMLIVEPLAYER_URL . "assets/js/tdplayer{$postfix}.js", array( 'load-jquery', 'wp-mediaelement', 'underscore', 'pjax', 'classlist-polyfill' ), '2.5.1', true );
		wp_localize_script( 'tdplayer', 'gmr', array( 'logged_in' => is_gigya_user_logged_in(), 'callsign' => $callsign, 'streamUrl' => $vast_url, 'wpLoggedIn' => is_user_logged_in(), 'homeUrl' => $home_url ) );
		wp_enqueue_script( 'jquery-ui-button');
		wp_enqueue_script( 'gmlp-js', GMLIVEPLAYER_URL . "assets/js/greater_media_live_player{$postfix}.js", array( 'jquery', 'wp-mediaelement', 'cookies-js','tdplayer' ), GMLIVEPLAYER_VERSION, true );
		wp_localize_script( 'tdplayer', 'gmlp', array( 'logged_in' => is_user_logged_in(), 'popup_url' => home_url( self::$endpoint_slug ), 'is_popup' => self::$is_loading_popup ) );
	}

	/**
	 * this script has to be loaded as Async and as shown
	 *
	 * @todo find a way to add this to wp_enqueue_script. This seemed to be interesting - http://wordpress.stackexchange.com/questions/38319/how-to-add-defer-defer-tag-in-plugin-javascripts/38335#38335
	 *       but causes `data-dojo-config` to load after the src, which then causes the script to fail and the TD Player API will not fully load
	 */
	public static function load_js() {
		echo '<script>
            var tdApiBaseUrl = \'http://playercore.preprod01.streamtheworld.net/tdplayerapi/2.5/\';
        </script>';

		echo '<script data-dojo-config="onReady:window.tdPlayerApiReady, async: 1, tlmSiblingOfDojo: 0, deps:[\'tdapi/run\']" src="//api.listenlive.co/tdplayerapi/2.5/dojo/dojo.js"></script>';

	}

}

GMLP_Player::init();