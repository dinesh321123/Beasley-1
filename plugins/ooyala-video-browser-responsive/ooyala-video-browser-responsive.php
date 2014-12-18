<?php

/*
Plugin Name: Ooyala Video - Responsive player
Description: Enhances the Ooyala Video plugin to make videos work better on responsive web sites
Version: 1.0
*/

define( 'OOYALA_RESPONSIVE_VERSION', '0.0.1' );
define( 'OOYALA_RESPONSIVE_URL', plugin_dir_url( __FILE__ ) );
define( 'OOYALA_RESPONSIVE_PATH', dirname( __FILE__ ) . '/' );

add_action( 'admin_enqueue_scripts', 'ooyala_responsive_admin_enqueue_scripts' );
function ooyala_responsive_admin_enqueue_scripts() {
	wp_enqueue_script(
		'ooyala-video-browser-responsive',
		OOYALA_RESPONSIVE_URL . '/ooyala-video-browser-responsive.js',
		array(),
		false,
		true
	);
}

add_action( 'init', 'ooyala_responsive_init', 20 );
function ooyala_responsive_init() {
	add_shortcode( 'ooyala', 'ooyala_responsive_shortcode' );
}

function ooyala_responsive_shortcode( $atts ) {

//	$instance = Ooyala_Video::init();
//	$rendered_player = $instance->shortcode($atts);
//	$rendered_player = str_replace("width='500'", "width='100%'", $rendered_player);
//	$rendered_player = str_replace('width=500', 'width=960', $rendered_player);
//	print esc_html($rendered_player);
//	return $rendered_player;

	$options = get_option( 'ooyala' );
	extract( shortcode_atts( apply_filters( 'ooyala_default_query_args', array(
			'width'         => '',
			'code'          => '',
			'autoplay'      => '',
			'callback'      => 'recieveOoyalaEvent',
			'wmode'         => 'opaque',
			'player_id'     => $options['player_id'],
			'platform'      => 'html5-fallback',
			'wrapper_class' => 'ooyala-video-wrapper',
		) ), $atts
	) );
	if ( empty( $width ) ) {
		$width = $options['video_width'];
	}
	if ( empty( $width ) ) {
		$width = $GLOBALS['content_width'];
	}
	if ( empty( $width ) ) {
		$width = 500;
	}

	$width           = (int) $width;
	$height          = floor( $width * 9 / 16 );
	$autoplay        = (bool) $autoplay ? '1' : '0';
	$sanitized_embed = sanitize_key( $code );
	$wmode           = in_array( $wmode, array(
		'window',
		'transparent',
		'opaque',
		'gpu',
		'direct'
	) ) ? $wmode : 'opaque';
	$wrapper_class   = sanitize_key( $wrapper_class );
	// V2 Callback
	$callback = preg_match( '/[^\w]/', $callback ) ? '' : sanitize_text_field( $callback ); // // sanitize a bit because we don't want nasty things
	// Check if platform is one of the accepted. If not, set to html5-fallback
	$platform = in_array( $platform, array(
		'flash',
		'flash-only',
		'html5-fallback',
		'html5-priority'
	) ) ? $platform : 'html5-fallback';

	if ( empty( $code ) ) {
		if ( isset( $atts[0] ) ) {
			$code = $atts[0];
		} else {
			return '<!--Error: Ooyala shortcode is missing the code attribute -->';
		}
	}

	if ( preg_match( "/[^a-z^A-Z^0-9^\-^\_]/i", $code ) ) {
		return '<!--Error: Ooyala shortcode attribute contains illegal characters -->';
	}

	// @TODO defaulting to the Ooyala sales video & it's associated playerBrandingId
	if ( empty( $player_id ) || 'null' === $player_id ) {
		$player_id = 'dcb79e2098c94889a1b9f2af6280b45d';
	}

	$output = '';
	$output .= '<script src="http://player.ooyala.com/v3/' . esc_attr( $player_id ) . '?platform=' . $platform . '"></script>';
	$output .= <<<HTML

<div id='playerwrapper1' class="ooyala-player-wrapper" style='max-width:800px;max-height:600px;' data-ooyala-video="{$code}"></div>

<script>
jQuery(function() {
	window.ooyalaResponsiveVideoPlayers = [];
	jQuery('.ooyala-player-wrapper').each(function() {
		ooyalaResponsiveVideoPlayers.push(OO.Player.create(this.id, this.dataset.ooyalaVideo,
		{
		}));

	});
});
</script>
HTML;

	return $output;
}