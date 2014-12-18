<?php

/*
Plugin Name: Ooyala Video - Responsive player
Description: Enhances the Ooyala Video plugin to make videos work better on responsive web sites
Version: 1.0
*/

define( 'OOYALA_RESPONSIVE_VERSION', '0.0.1' );
define( 'OOYALA_RESPONSIVE_URL', plugin_dir_url( __FILE__ ) );
define( 'OOYALA_RESPONSIVE_PATH', dirname( __FILE__ ) . '/' );

$ooyala_players = array();

add_action( 'admin_enqueue_scripts', 'ooyala_responsive_admin_enqueue_scripts' );
function ooyala_responsive_admin_enqueue_scripts() {

	wp_enqueue_script(
		'ooyala-video-browser-responsive',
		OOYALA_RESPONSIVE_URL . '/ooyala-video-browser-responsive-admin.js',
		array(),
		OOYALA_RESPONSIVE_VERSION,
		true
	);

}

add_action( 'wp_enqueue_scripts', 'ooyala_responsive_wp_enqueue_styles' );
function ooyala_responsive_wp_enqueue_styles() {

	wp_enqueue_style(
		'ooyala-video-browser-responsive',
		OOYALA_RESPONSIVE_URL . '/ooyala-video-browser-responsive.css',
		array(),
		OOYALA_RESPONSIVE_VERSION
	);

}

add_action( 'init', 'ooyala_responsive_init', 20 );
function ooyala_responsive_init() {
	add_shortcode( 'ooyala', 'ooyala_responsive_shortcode' );
}

function ooyala_responsive_shortcode( $atts ) {

	static $player_shortcode_index;
	if ( ! isset( $player_shortcode_index ) ) {
		$player_shortcode_index = 1;
	}

	$options = get_option( 'ooyala' );

	$shortcode_atts = shortcode_atts( apply_filters( 'ooyala_default_query_args', array(
		'width'         => '',
		'code'          => '',
		'autoplay'      => '',
		'callback'      => 'recieveOoyalaEvent',
		'wmode'         => 'opaque',
		'player_id'     => $options['player_id'],
		'platform'      => 'html5-fallback',
		'wrapper_class' => 'ooyala-video-wrapper',
	) ), $atts
	);

	$code = $shortcode_atts['code'];
	$player_id = $shortcode_atts['player_id'];
	$platform = $shortcode_atts['platform'];

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

	// If there isn't a valid player ID, return nothing
	if ( empty( $player_id ) || 'null' === $player_id ) {
		return '<!--Error: Ooyala options are missing the player ID -->';
	}

	$div_id = "playerwrapper{$player_shortcode_index}";

	$output = '';
	$output .= '<div id="' . esc_attr( $div_id ) . '" class="video-container"></div>';

	$player_shortcode_index += 1;
	$GLOBALS['ooyala_players'][] = array(
		'div_id'       => $div_id,
		'player_id'    => $player_id,
		'platform'     => $platform,
		'ooyala_video' => $code,
	);

	return $output;

}

add_action( 'wp_print_footer_scripts', 'ooyala_print_footer_scripts', 20 );
function ooyala_print_footer_scripts() {

	foreach ( $GLOBALS['ooyala_players'] as $ooyala_player ) {

		$player_id = isset( $ooyala_player['player_id'] ) ? $ooyala_player['player_id'] : '';
		$platform  = isset( $ooyala_player['platform'] ) ? $ooyala_player['platform'] : '';

		echo '<script src="http://player.ooyala.com/v3/' . esc_attr( $player_id ) . '?platform=' . esc_attr( $platform ) . '"></script>';

	}

	include OOYALA_RESPONSIVE_PATH . '/ooyala-video-browser-responsive-footer-scripts.tpl.php';

}