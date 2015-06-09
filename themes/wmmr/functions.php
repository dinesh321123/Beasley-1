<?php
/**
 * WMMR functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package WMMR
 * @since 0.1.0
 */

 // Useful global constants
define( 'WMMR_VERSION', '0.1.4' );

 /**
  * Set up theme defaults and register supported WordPress features.
  *
  * @uses load_theme_textdomain() For translation/localization support.
  *
  * @since 0.1.0
  */
 function wmmr_setup() {
	/**
	 * Makes WMMR available for translation.
	 *
	 * Translations can be added to the /lang directory.
	 * If you're building a theme based on WMMR, use a find and replace
	 * to change 'wmmr' to the name of your theme in all template files.
	 */
	load_theme_textdomain( 'wmmr', get_stylesheet_directory_uri() . '/languages' );
 }
 add_action( 'after_setup_theme', 'wmmr_setup' );

 /**
  * Enqueue scripts and styles for front-end.
  *
  * @since 0.1.0
  */
 function wmmr_scripts_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

	wp_dequeue_style( 'greatermedia' );
	wp_deregister_style( 'greatermedia' );
	wp_enqueue_style( 'wmmr', get_stylesheet_directory_uri() . "/assets/css/wmmr{$postfix}.css", array(), WMMR_VERSION );
 }
 add_action( 'wp_enqueue_scripts', 'wmmr_scripts_styles', 20 );

 /**
  * Add humans.txt to the <head> element.
  */
 function wmmr_header_meta() {
	$humans = '<link type="text/plain" rel="author" href="' . get_stylesheet_directory_uri() . '/humans.txt" />';

	echo apply_filters( 'wmmr_humans', $humans );
 }
 add_action( 'wp_head', 'wmmr_header_meta' );
