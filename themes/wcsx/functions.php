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

 // Useful global constants
define( 'WCSX_VERSION', '0.3.3' ); /* Version bump by Steve 3/8/2016 @ 11:30 a.m. EST */

 /**
  * Set up theme defaults and register supported WordPress features.
  *
  * @uses load_theme_textdomain() For translation/localization support.
  *
  * @since 0.1.0
  */
 function wcsx_setup() {
	/**
	 * Makes wcsx available for translation.
	 *
	 * Translations can be added to the /lang directory.
	 * If you're building a theme based on wcsx, use a find and replace
	 * to change 'wcsx' to the name of your theme in all template files.
	 */
	load_theme_textdomain( 'wcsx', get_stylesheet_directory_uri() . '/languages' );
 }
 add_action( 'after_setup_theme', 'wcsx_setup' );

 /**
  * Enqueue scripts and styles for front-end.
  *
  * @since 0.1.1
  */
 function wcsx_scripts_styles() {
  $postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
  wp_register_style('google-fonts-wcsx','//fonts.googleapis.com/css?family=Oswald:400,300,700',array(),null);
  wp_dequeue_style( 'greatermedia' );
  wp_deregister_style( 'greatermedia' );
  wp_enqueue_style( 'wcsx', get_stylesheet_directory_uri() . "/assets/css/wcsx{$postfix}.css", array( 'google-fonts-wcsx' ), WCSX_VERSION );
  wp_enqueue_script(
    'wcsx',
    get_stylesheet_directory_uri() . "/assets/js/wcsx{$postfix}.js",
    array(),
    WCSX_VERSION,
    true
  );
}
 add_action( 'wp_enqueue_scripts', 'wcsx_scripts_styles', 20 );

 /**
  * Add humans.txt to the <head> element.
  */
 function wcsx_header_meta() {
	$humans = '<link type="text/plain" rel="author" href="' . get_stylesheet_directory_uri() . '/humans.txt" />';

	echo apply_filters( 'wcsx_humans', $humans );
 }
 add_action( 'wp_head', 'wcsx_header_meta' );

 /**
  * Add Chartbeat to site.
  */
 function wcsx_chartbeat_header() {
  $content = '<script type="text/javascript">var _sf_startpt=(new Date()).getTime()</script>';

  echo apply_filters( 'wcsx_chartbeat_header', $content );
 }
 add_action( 'wp_head', 'wcsx_chartbeat_header' );

function wcsx_chartbeat_footer() {
  $content = '<script type="text/javascript">var cbjspath = "static.chartbeat.com/js/chartbeat.js?uid=2332&domain=wcsx.com";var cbjsprotocol = (("https:" == document.location.protocol) ? "/web/20150302054219/https://s3.amazonaws.com/" : "http://"); document.write(unescape("%3Cscript src=\'"+cbjsprotocol+cbjspath+"\' type=\'text/javascript\'%3E%3C/script%3E"))</script>';

  echo apply_filters( 'wcsx_chartbeat_footer', $content );
}
add_action( 'wp_footer', 'wcsx_chartbeat_footer' );
