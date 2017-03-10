<?php
/**
 * WBOS functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package WBOS
 * @since 0.1.0
 */

 // Useful global constants
define( 'WBOS_VERSION', '2.0.3' ); /* Version bump by Steve 03/10/2017 */

 /**
  * Set up theme defaults and register supported WordPress features.
  *
  * @uses load_theme_textdomain() For translation/localization support.
  *
  * @since 0.1.0
  */
 function wbos_setup() {
	/**
	 * Makes WBOS available for translation.
	 *
	 * Translations can be added to the /lang directory.
	 * If you're building a theme based on WBOS, use a find and replace
	 * to change 'wbos' to the name of your theme in all template files.
	 */
	load_theme_textdomain( 'wbos', get_stylesheet_directory_uri() . '/languages' );
 }
 add_action( 'after_setup_theme', 'wbos_setup' );

 /**
  * Enqueue scripts and styles for front-end.
  *
  * @since 0.1.0
  */
 function wbos_scripts_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
  wp_register_style('google-fonts-wbos','//fonts.googleapis.com/css?family=Bree+Serif',array(),null);
	wp_dequeue_style( 'greatermedia' );
	wp_deregister_style( 'greatermedia' );
	wp_enqueue_style( 'wbos', get_stylesheet_directory_uri() . "/assets/css/wbos{$postfix}.css", array('google-fonts-wbos'), WBOS_VERSION );
  wp_enqueue_script(
		'wbos',
		get_stylesheet_directory_uri() . "/assets/js/wbos{$postfix}.js",
		array(),
		WBOS_VERSION,
		true);


 }
 add_action( 'wp_enqueue_scripts', 'wbos_scripts_styles', 20 );

 /**
  * Add humans.txt to the <head> element.
  */
 function wbos_header_meta() {
	$humans = '<link type="text/plain" rel="author" href="' . get_stylesheet_directory_uri() . '/humans.txt" />';

	echo apply_filters( 'wbos_humans', $humans );
 }
 add_action( 'wp_head', 'wbos_header_meta' );
