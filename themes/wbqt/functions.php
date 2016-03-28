<?php
/**
 * WBQT functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package WBQT
 * @since 0.1.0
 */

 // Useful global constants

define( 'WBQT_VERSION', '0.3.3' ); /* Version bump by Denis Prindeville 3/28/2016 @ 1:00 p.m. EST */


 /**
  * Set up theme defaults and register supported WordPress features.
  *
  * @uses load_theme_textdomain() For translation/localization support.
  *
  * @since 0.1.0
  */
 function wbqt_setup() {
	/**
	 * Makes WBQT available for translation.
	 *
	 * Translations can be added to the /lang directory.
	 * If you're building a theme based on WBQT, use a find and replace
	 * to change 'wbqt' to the name of your theme in all template files.
	 */
	load_theme_textdomain( 'wbqt', get_stylesheet_directory_uri() . '/languages' );
 }
 add_action( 'after_setup_theme', 'wbqt_setup' );

 /**
  * Enqueue scripts and styles for front-end.
  *
  * @since 0.1.0
  */
 function wbqt_scripts_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

	wp_dequeue_style( 'greatermedia' );
	wp_deregister_style( 'greatermedia' );
	wp_enqueue_style( 'wbqt', get_stylesheet_directory_uri() . "/assets/css/wbqt{$postfix}.css", array(), WBQT_VERSION );
	wp_enqueue_script(
    'wbqt',
    get_stylesheet_directory_uri() . "/assets/js/wbqt{$postfix}.js",
    array(),
    WBQT_VERSION,
    true
  );
 }
 add_action( 'wp_enqueue_scripts', 'wbqt_scripts_styles', 20 );

 /**
  * Add humans.txt to the <head> element.
  */
 function wbqt_header_meta() {
	$humans = '<link type="text/plain" rel="author" href="' . get_stylesheet_directory_uri() . '/humans.txt" />';

	echo apply_filters( 'wbqt_humans', $humans );
 }
 add_action( 'wp_head', 'wbqt_header_meta' );
