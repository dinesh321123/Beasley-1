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
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ), 1 );
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
	 * Enqueue scripts
	 */
	public static function enqueue_scripts() {
		$script_debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
		$postfix = $script_debug ? '' : '.min';

		wp_register_script( 'liveplayer', '//sdk.listenlive.co/web/v/2.9.26-234/td-sdk.min.js', null, null, true );
		wp_register_script( 'gmlp-js', GMLIVEPLAYER_URL . "assets/js/live-player{$postfix}.js", array( 'jquery', 'liveplayer', 'underscore', 'classlist-polyfill', 'pjax', 'wp-mediaelement', 'cookies-js' ), GMLIVEPLAYER_VERSION, true );
		wp_localize_script( 'gmlp-js', 'gmr', array(
			'debug'      => $script_debug,
			'logged_in'  => false,
			'callsign'   => function_exists( 'gmr_streams_get_primary_stream_callsign') ? gmr_streams_get_primary_stream_callsign() : '',
			'streamUrl'  => function_exists( 'gmr_streams_get_primary_stream_vast_url') ? gmr_streams_get_primary_stream_vast_url() : '',
			'wpLoggedIn' => is_user_logged_in(),
			'homeUrl'    => home_url( '/' ),
			'popup_url'  => home_url( self::$endpoint_slug ),
			'is_popup'   => self::$is_loading_popup,
			'intervals'  => array(
				'live_streaming' => absint( get_option( 'gmr_live_streaming_interval', 1 ) ),
				'inline_audio'   => absint( get_option( 'gmr_inline_audio_interval', 1 ) ),
			),
		) );
	}

	public static function register_settings() {
		$interval_callback = array( __CLASS__, 'render_interval_settings' );

		add_settings_section( 'greatermedia_live_player', 'Live Player', array( __CLASS__, 'render_settings_description' ), 'media' );

		add_settings_field( 'gmr_live_streaming_interval', 'Live Streaming Interval', $interval_callback, 'media', 'greatermedia_live_player', array( 'name' => 'gmr_live_streaming_interval' ) );
		add_settings_field( 'gmr_inline_audio_interval', 'Inline Audio Interval', $interval_callback, 'media', 'greatermedia_live_player', array( 'name' => 'gmr_inline_audio_interval' ) );

		register_setting( 'media', 'gmr_live_streaming_interval', 'intval' );
		register_setting( 'media', 'gmr_inline_audio_interval', 'intval' );
	}

	public static function render_settings_description() {
		?><p>
			Use following settings to setup live player events tracking intervals. Intervals will be used to track live player activity. Each interval is in minutes; setting it to &quot;0&quot; (zero) will disable that event recoding for the site.
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

}
