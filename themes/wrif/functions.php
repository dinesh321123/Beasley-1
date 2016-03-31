<?php
/**
 * WRIF functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package WRIF
 * @since 0.1.0
 */

 // Useful global constants
define( 'WRIF_VERSION', '0.2.3' ); /* Version bump by Steve 3/31/16 @ 9:15 a.m. EST */

 /**
  * Set up theme defaults and register supported WordPress features.
  *
  * @uses load_theme_textdomain() For translation/localization support.
  *
  * @since 0.1.0
  */
 function wrif_setup() {
	/**
	 * Makes WRIF available for translation.
	 *
	 * Translations can be added to the /lang directory.
	 * If you're building a theme based on WRIF, use a find and replace
	 * to change 'wrif' to the name of your theme in all template files.
	 */
	load_theme_textdomain( 'wrif', get_stylesheet_directory_uri() . '/languages' );
 }
 add_action( 'after_setup_theme', 'wrif_setup' );

 /**
  * Enqueue scripts and styles for front-end.
  *
  * @since 0.1.0
  */
 function wrif_scripts_styles() {$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
    wp_register_style('google-fonts-wrif','//fonts.googleapis.com/css?family=Oswald:400,300,700',array(),null);
	wp_dequeue_style( 'greatermedia' );
	wp_deregister_style( 'greatermedia' );
	wp_enqueue_style( 'wrif', get_stylesheet_directory_uri() . "/assets/css/wrif{$postfix}.css", array( 'google-fonts-wrif' ), WRIF_VERSION );
	wp_enqueue_script(
		'wrif',
		get_stylesheet_directory_uri() . "/assets/js/wrif{$postfix}.js",
		array(),
		WRIF_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'wrif_scripts_styles', 20 );



 /**
  * Add humans.txt to the <head> element.
  */
 function wrif_header_meta() {
	$humans = '<link type="text/plain" rel="author" href="' . get_stylesheet_directory_uri() . '/humans.txt" />';

	echo apply_filters( 'wrif_humans', $humans );
 }
 add_action( 'wp_head', 'wrif_header_meta' );

/**
* Add Chartbeat to site.
*/
function wrif_chartbeat_header() {
	$content = '<script type="text/javascript">var _sf_startpt=(new Date()).getTime()</script>';

	echo apply_filters( 'wrif_chartbeat_header', $content );
}
add_action( 'wp_head', 'wrif_chartbeat_header' );

function wrif_chartbeat_footer() {
	$content = '<script type="text/javascript">var cbjspath = "static.chartbeat.com/js/chartbeat.js?uid=2332&domain=wrif.com";var cbjsprotocol = (("https:" == document.location.protocol) ? "/web/20150305155406/https://s3.amazonaws.com/" : "http://"); document.write(unescape("%3Cscript src=\'"+cbjsprotocol+cbjspath+"\' type=\'text/javascript\'%3E%3C/script%3E"))</script>';

	echo apply_filters( 'wrif_chartbeat_footer', $content );
}
add_action( 'wp_footer', 'wrif_chartbeat_footer' );
