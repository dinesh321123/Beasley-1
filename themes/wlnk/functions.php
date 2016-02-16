<?php
/**
 * WLNK functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package WLNK
 * @since 0.1.0
 */

 // Useful global constants
define( 'WLNK_VERSION', '0.1.11' ); /* Version bump by Jonathan 2/9/2016 @ 8:34 a.m. EST */

 /**
  * Set up theme defaults and register supported WordPress features.
  *
  * @uses load_theme_textdomain() For translation/localization support.
  *
  * @since 0.1.0
  */
 function wlnk_setup() {
	/**
	 * Makes WLNK available for translation.
	 *
	 * Translations can be added to the /lang directory.
	 * If you're building a theme based on WLNK, use a find and replace
	 * to change 'wlnk' to the name of your theme in all template files.
	 */
	load_theme_textdomain( 'wlnk', get_stylesheet_directory_uri() . '/languages' );
 }
 add_action( 'after_setup_theme', 'wlnk_setup' );

 /**
  * Enqueue scripts and styles for front-end.
  *
  * @since 0.1.0
  */
 function wlnk_scripts_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

	global $wp_styles;

	wp_dequeue_style( 'greatermedia' );
	wp_deregister_style( 'greatermedia' );
	wp_enqueue_style( 'wlnk', get_stylesheet_directory_uri() . "/assets/css/wlnk{$postfix}.css", array(), WLNK_VERSION );
	wp_enqueue_style( 'wlnk_ie', get_stylesheet_directory_uri() . "/assets/css/wlnk_ie.css", array('wlnk'), WLNK_VERSION );
	$wp_styles->add_data( 'wlnk_ie', 'conditional', 'lte IE 9' );
	wp_enqueue_style( 'wlnk_font', "http://fonts.googleapis.com/css?family=Work+Sans", array(), WLNK_VERSION );
	wp_enqueue_script(
        'wlnk',
        get_stylesheet_directory_uri() . "/assets/js/wlnk{$postfix}.js",
        array(),
        WLNK_VERSION,
        true
    );
    wp_enqueue_script(
	    'steel-media',
	    'https://secure.adnxs.com/seg?add=3581727&t=1',
	    array(),
	    null,
	    true
		);
    wp_enqueue_script( 'handlebars', get_stylesheet_directory_uri() . '/assets/js/vendor/handlebars-v3.0.3.js', array( 'jquery' ) );
    wp_enqueue_script( 'googlemaps', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false', array() );
 }
 add_action( 'wp_enqueue_scripts', 'wlnk_scripts_styles', 20 );

 /**
  * Add humans.txt to the <head> element.
  */
 function wlnk_header_meta() {
	$humans = '<link type="text/plain" rel="author" href="' . get_stylesheet_directory_uri() . '/humans.txt" />';

	echo apply_filters( 'wlnk_humans', $humans );
 }
 add_action( 'wp_head', 'wlnk_header_meta' );
