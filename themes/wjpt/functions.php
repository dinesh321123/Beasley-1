<?php
/**
 * WJPT functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package WJPT
 * @since 0.1.0
 */

$version = '0.1.1';

// If .version.php file exists, the content of this file (timestamp) is added to the $version value set above
if ( file_exists( __DIR__ . '/../.version.php' ) ) {
	$suffix  = intval( file_get_contents( __DIR__ . '/../.version.php' ) );
	$version = $version . "." . $suffix;
}

 // Useful global constants
define( 'WJPT_VERSION', $version );

 /**
  * Set up theme defaults and register supported WordPress features.
  *
  * @uses load_theme_textdomain() For translation/localization support.
  *
  * @since 0.1.0
  */
 function wjpt_setup() {
	/**
	 * Makes WJPT available for translation.
	 *
	 * Translations can be added to the /lang directory.
	 * If you're building a theme based on WJPT, use a find and replace
	 * to change 'wjpt' to the name of your theme in all template files.
	 */
	load_theme_textdomain( 'wjpt', get_stylesheet_directory_uri() . '/languages' );
 }
 add_action( 'after_setup_theme', 'wjpt_setup' );

 /**
  * Enqueue scripts and styles for front-end.
  *
  * @since 0.1.0
  */
 function wjpt_scripts_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

	wp_dequeue_style( 'greatermedia' );
	wp_deregister_style( 'greatermedia' );
	wp_enqueue_style( 'wjpt', get_stylesheet_directory_uri() . "/assets/css/wjpt{$postfix}.css", array(), WJPT_VERSION );
 }
 add_action( 'wp_enqueue_scripts', 'wjpt_scripts_styles', 20 );

 /**
  * Add humans.txt to the <head> element.
  */
 function wjpt_header_meta() {
	$humans = '<link type="text/plain" rel="author" href="' . get_stylesheet_directory_uri() . '/humans.txt" />';

	echo apply_filters( 'wjpt_humans', $humans );
 }
 add_action( 'wp_head', 'wjpt_header_meta' );
