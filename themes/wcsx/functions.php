<?php
/**
 * wcsx functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package wcsx
 * @since 0.1.1
 */

/**
 * Enqueue scripts and styles for front-end.
 *
 * @since 0.1.1
 */
function wcsx_scripts_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
	wp_register_style( 'google-fonts-wcsx', '//fonts.googleapis.com/css?family=Oswald:400,300,700', array(), null );
	wp_dequeue_style( 'greatermedia' );
	wp_deregister_style( 'greatermedia' );
	wp_enqueue_style( 'wcsx', get_stylesheet_directory_uri() . "/assets/css/wcsx{$postfix}.css", array( 'google-fonts-wcsx' ), GREATERMEDIA_VERSION );
	wp_enqueue_script( 'livefyre', '//cdn.livefyre.com/Livefyre.js', null, null, true );
	wp_enqueue_script( 'wcsx', get_stylesheet_directory_uri() . "/assets/js/wcsx{$postfix}.js", array( 'livefyre' ), GREATERMEDIA_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'wcsx_scripts_styles', 20 );

/**
 * Add Chartbeat to site.
 */
function wcsx_chartbeat_header() {
	$content = '<script type="text/javascript">var _sf_startpt=(new Date()).getTime()</script>';

	echo apply_filters( 'wcsx_chartbeat_header', $content );
}
add_action( 'wp_head', 'wcsx_chartbeat_header' );

function wcsx_chartbeat_footer() {
	$content = '<script type="text/javascript">var cbjspath = "static.chartbeat.com/js/chartbeat.js?uid=2332&domain=wcsx.com";var cbjsprotocol = (("https:" == document.location.protocol) ? "https://" : "http://"); document.write(unescape("%3Cscript src=\'"+cbjsprotocol+cbjspath+"\' type=\'text/javascript\'%3E%3C/script%3E"))</script>';

	echo apply_filters( 'wcsx_chartbeat_footer', $content );
}
add_action( 'wp_footer', 'wcsx_chartbeat_footer' );
