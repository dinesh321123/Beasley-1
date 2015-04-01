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

		add_action( 'admin_init', array( __CLASS__, 'register_settings' ), 9 );
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

	public static function deactivate() {
		// flush rules on deactivate as well so they're not left hanging around uselessly
		flush_rewrite_rules();
	}

	public static function activate() {
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
		?><div class="live-stream__player">
			<div class="live-stream__controls">
				<div id="playButton" class="live-stream__btn--play" data-action="play-live"></div>
				<div id="loadButton" class="live-stream__btn--loading"><i class="gmr-icon icon-spin icon-loading"></i></div>
				<div id="pauseButton" class="live-stream__btn--pause"></div>
				<div id="resumeButton" class="live-stream__btn--resume"></div>
			</div>

			<div id="live-stream__container" class="live-stream__container">
				<div id="td_container" class="live-stream__container--player"></div>
				<div class="pre-roll__notification"><?php _e( 'Live stream will be available after this brief ad from our sponsors', ' gmliveplayer' ); ?></div>
			</div>

			<div id="live-player--volume"></div>
		</div><?php
	}

	/**
	 * Enqueue scripts
	 */
	public static function enqueue_scripts() {
		$script_debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
		$postfix = $script_debug ? '' : '.min';

		if ( function_exists( 'gmr_streams_get_primary_stream_callsign') ) {
			$callsign = gmr_streams_get_primary_stream_callsign();
		}

		if ( function_exists( 'gmr_streams_get_primary_stream_vast_url') ) {
			$vast_url = gmr_streams_get_primary_stream_vast_url();
		}

		wp_register_script( 'nielsen-sdk', '//secure-us.imrworldwide.com/novms/js/2/ggcmb400.js', null, null );

		$optout = false;
		if ( function_exists( 'get_gigya_user_field' ) ) {
			$optout = filter_var( get_gigya_user_field( 'data.nielsen_optout' ), FILTER_VALIDATE_BOOLEAN );
		}

		if ( ! $optout ) {
			$apid = get_option( 'gmr_nielsen_sdk_apid' );
			if ( ! empty( $apid ) ) {
				wp_localize_script( 'nielsen-sdk', '_nolggGlobalParams', array(
					'apid'   => $apid,
					'apn'    => get_option( 'gmr_nielsen_sdk_apn', get_bloginfo( 'name' ) ),
					'sfcode' => get_option( 'gmr_nielsen_sdk_mode' ) ? 'drm' : 'uat-cert',
				) );
			}
		}

		$home_url = home_url( '/' );
		wp_enqueue_script( 'gmlp-js', GMLIVEPLAYER_URL . "assets/js/greater_media_live_player{$postfix}.js", array( 'jquery', 'underscore', 'classlist-polyfill', 'nielsen-sdk', 'pjax', 'wp-mediaelement', 'cookies-js' ), GMLIVEPLAYER_VERSION, true );
		wp_localize_script( 'gmlp-js', 'gmr', array(
			'debug'      => $script_debug,
			'logged_in'  => is_gigya_user_logged_in(),
			'callsign'   => $callsign,
			'streamUrl'  => $vast_url,
			'wpLoggedIn' => is_user_logged_in(),
			'homeUrl'    => $home_url,
			'popup_url'  => home_url( self::$endpoint_slug ),
			'is_popup'   => self::$is_loading_popup,
			'intervals'  => array(
				'live_streaming' => absint( get_option( 'gmr_live_streaming_interval', 1 ) ),
				'inline_audio'   => absint( get_option( 'gmr_inline_audio_interval', 1 ) ),
			),
		) );
	}

	/**
	 * this script has to be loaded as Async and as shown
	 *
	 * @todo find a way to add this to wp_enqueue_script. This seemed to be interesting - http://wordpress.stackexchange.com/questions/38319/how-to-add-defer-defer-tag-in-plugin-javascripts/38335#38335
	 *       but causes `data-dojo-config` to load after the src, which then causes the script to fail and the TD Player API will not fully load
	 */
	public static function load_js() {
		echo '<script>
            var tdApiBaseUrl = \'http://api.listenlive.co/tdplayerapi/2.5/\';
        </script>';

		echo '<script data-dojo-config="onReady:window.tdPlayerApiReady, async: 1, tlmSiblingOfDojo: 0, deps:[\'tdapi/run\']" src="//api.listenlive.co/tdplayerapi/2.5/dojo/dojo.js"></script>';

	}

	public static function register_settings() {
		$text_callback = array( __CLASS__, 'render_text_setting' );
		$interval_callback = array( __CLASS__, 'render_interval_settings' );

		add_settings_section( 'greatermedia_live_player', 'Live Player', array( __CLASS__, 'render_settings_description' ), 'media' );

		add_settings_field( 'gmr_nielsen_sdk_apid', 'Nielsen SDK App ID', $text_callback, 'media', 'greatermedia_live_player', array(
			'name' => 'gmr_nielsen_sdk_apid',
			'desc' => 'Enter Nielsen Browser SDK identifier for the application.',
		) );

		add_settings_field( 'gmr_nielsen_sdk_apn', 'Nielsen SDK App Name', $text_callback, 'media', 'greatermedia_live_player', array(
			'name'    => 'gmr_nielsen_sdk_apn',
			'desc'    => 'Enter a string value for describing your player (for example, prime-time channel browser player).',
			'default' => get_bloginfo( 'name' ),
		) );

		add_settings_field( 'gmr_nielsen_sdk_mode', 'Nielsen SDK Mode', array( __CLASS__, 'render_nielsen_sdk_mode_settings' ), 'media', 'greatermedia_live_player' );

		add_settings_field( 'gmr_live_streaming_interval', 'Live Streaming Interval', $interval_callback, 'media', 'greatermedia_live_player', array( 'name' => 'gmr_live_streaming_interval' ) );
		add_settings_field( 'gmr_inline_audio_interval', 'Inline Audio Interval', $interval_callback, 'media', 'greatermedia_live_player', array( 'name' => 'gmr_inline_audio_interval' ) );

		register_setting( 'media', 'gmr_nielsen_sdk_apid', 'trim' );
		register_setting( 'media', 'gmr_nielsen_sdk_apn', 'trim' );
		register_setting( 'media', 'gmr_nielsen_sdk_mode', 'boolval' );
		register_setting( 'media', 'gmr_live_streaming_interval', 'intval' );
		register_setting( 'media', 'gmr_inline_audio_interval', 'intval' );
	}

	public static function render_settings_description() {
		?><p>
			Use following settings to setup Nielsen Browser SDK and live player events tracking intervals. Intervals will be used to track live player activity. Each interval is in minutes; setting it to &quot;0&quot; (zero) will disable that event recoding for the site.
		</p><?php
	}

	public static function render_interval_settings( $args ) {
		$name = $args['name'];

		?><label>
			<input type="number" class="small-text" name="<?php echo esc_attr( $name ); ?>" value="<?php echo intval( get_option( $name, 1 ) ); ?>" min="0" step="1">
			mins
		</label><?php
	}

	public static function render_text_setting( $args ) {
		$name = $args['name'];
		$default = isset( $args['default'] ) ? $args['default'] : null;

		?><input type="text" class="regular-text" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( get_option( $name, $default ) ); ?>">
		<p class="description"><?php echo esc_html( $args['desc'] ); ?></p><?php
	}

	public static function render_nielsen_sdk_mode_settings() {
		?><select name="gmr_nielsen_sdk_mode">
			<option value="0">Test</option>
			<option value="1"<?php selected( get_option( 'gmr_nielsen_sdk_mode' ) ); ?>>Production</option>
		</select><?php
	}

}

GMLP_Player::init();
