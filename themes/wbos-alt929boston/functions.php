<?php
/**
 * WBOS- ALT 929 BOSTON functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package WBOS - ALT929BOSTON
 * @since 0.1.0
 */

 // Useful global constants
define( 'WBOS_ALT929BOSTON_VERSION', '0.3.7' ); /* Version bump by Steve Meyers 7/11/2016 @ 1:00 p.m. EST */

 /**
  * Set up theme defaults and register supported WordPress features.
  *
  * @uses load_theme_textdomain() For translation/localization support.
  *
  * @since 0.1.0
  */
 function wbos_alt929boston_setup() {
	/**
	 * Makes WBOS - ALT929BOSTON available for translation.
	 *
	 * Translations can be added to the /lang directory.
	 * If you're building a theme based on WBOS - ALT929BOSTON, use a find and replace
	 * to change 'wbos_alt929boston' to the name of your theme in all template files.
	 */
	load_theme_textdomain( 'wbos_alt929boston', get_stylesheet_directory_uri() . '/languages' );
 }
 add_action( 'after_setup_theme', 'wbos_alt929boston_setup' );

 /**
  * Enqueue scripts and styles for front-end.
  *
  * @since 0.1.0
  */
 function wbos_alt929boston_scripts_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

	wp_dequeue_style( 'greatermedia' );
	wp_deregister_style( 'greatermedia' );
	wp_enqueue_style( 'wbos_alt929boston', get_stylesheet_directory_uri() . "/assets/css/wbos_alt929boston{$postfix}.css", array(), WBOS_ALT929BOSTON_VERSION );
 }
 add_action( 'wp_enqueue_scripts', 'wbos_alt929boston_scripts_styles', 20 );

 /**
  * Add humans.txt to the <head> element.
  */
 function wbos_alt929boston_header_meta() {
	$humans = '<link type="text/plain" rel="author" href="' . get_stylesheet_directory_uri() . '/humans.txt" />';

	echo apply_filters( 'wbos_alt929boston_humans', $humans );
 }
 add_action( 'wp_head', 'wbos_alt929boston_header_meta' );
