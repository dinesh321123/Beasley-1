<?php
/**
 * WROR functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package WROR
 * @since 0.1.0
 */

 // Useful global constants
<<<<<<< HEAD
define( 'WROR_VERSION', '0.1.8' ); /* Version bump by Denis Prindeville 3/4/2016 @ 11:00 a.m. EST */
=======
define( 'WROR_VERSION', '0.1.8' ); /* Version bump by Denis Prindeville on 3/4/2016 @ 1:50 p.m. EST */
>>>>>>> feature/wror-css-changes

 /**
  * Set up theme defaults and register supported WordPress features.
  *
  * @uses load_theme_textdomain() For translation/localization support.
  *
  * @since 0.1.0
  */
 function wror_setup() {
	/**
	 * Makes WROR available for translation.
	 *
	 * Translations can be added to the /lang directory.
	 * If you're building a theme based on WROR, use a find and replace
	 * to change 'wror' to the name of your theme in all template files.
	 */
	load_theme_textdomain( 'wror', get_stylesheet_directory_uri() . '/languages' );
 }
 add_action( 'after_setup_theme', 'wror_setup' );

 /**
  * Enqueue scripts and styles for front-end.
  *
  * @since 0.1.0
  */
 function wror_scripts_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

	wp_dequeue_style( 'greatermedia' );
	wp_deregister_style( 'greatermedia' );
	wp_enqueue_style( 'wror', get_stylesheet_directory_uri() . "/assets/css/wror{$postfix}.css", array(), WROR_VERSION );
 }
 add_action( 'wp_enqueue_scripts', 'wror_scripts_styles', 20 );

 /**
  * Add humans.txt to the <head> element.
  */
 function wror_header_meta() {
	$humans = '<link type="text/plain" rel="author" href="' . get_stylesheet_directory_uri() . '/humans.txt" />';

	echo apply_filters( 'wror_humans', $humans );
 }
 add_action( 'wp_head', 'wror_header_meta' );
