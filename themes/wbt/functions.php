<?php
/**
 * WBT functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package WBT
 * @since 0.1.0
 */

 // Useful global constants
define( 'WBT_VERSION', '0.2.2' ); /* Version bump by Steve 7/25/2016 @ 4:30 p.m. EST */

 /**
  * Set up theme defaults and register supported WordPress features.
  *
  * @uses load_theme_textdomain() For translation/localization support.
  *
  * @since 0.1.0
  */
 function wbt_setup() {
	/**
	 * Makes WBT available for translation.
	 *
	 * Translations can be added to the /lang directory.
	 * If you're building a theme based on WBT, use a find and replace
	 * to change 'wbt' to the name of your theme in all template files.
	 */
	load_theme_textdomain( 'wbt', get_stylesheet_directory_uri() . '/languages' );
  add_image_size( 'gm-article-thumbnail-wbt',     		1175,   776,   array( 'center', 'top' ) ); // thumbnails used for articles

 }
 add_action( 'after_setup_theme', 'wbt_setup' );


 /**
  * Enqueue scripts and styles for front-end.
  *
  * @since 0.1.0
  */
 function wbt_scripts_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

	global $wp_styles;

	wp_dequeue_style( 'greatermedia' );
	wp_deregister_style( 'greatermedia' );
	wp_enqueue_style( 'wbt', get_stylesheet_directory_uri() . "/assets/css/wbt{$postfix}.css", array(), WBT_VERSION );
	wp_enqueue_style( 'wbt-ie', get_stylesheet_directory_uri() . "/assets/css/wbt_ie.css", array('wbt'), WBT_VERSION );
	$wp_styles->add_data( 'wbt-ie', 'conditional', 'lte IE 9' );
	wp_enqueue_style( 'wbt-font', "http://fonts.googleapis.com/css?family=Lato", array(), WBT_VERSION );
	wp_enqueue_script(
        'wbt',
        get_stylesheet_directory_uri() . "/assets/js/wbt{$postfix}.js",
        array(),
        WBT_VERSION,
        true
    );
    wp_enqueue_script(
	    'steel-media',
	    'http://ib.adnxs.com/seg?add=3513887&t=1',
	    array(),
	    null,
	    true
		);
    wp_enqueue_script( 'handlebars', get_stylesheet_directory_uri() . '/assets/js/vendor/handlebars-v3.0.3.js', array( 'jquery' ) );
    wp_enqueue_script( 'googlemaps', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false', array() );

 }
 add_action( 'wp_enqueue_scripts', 'wbt_scripts_styles', 20 );

 /**
  * Add humans.txt to the <head> element.
  */
 function wbt_header_meta() {
	$humans = '<link type="text/plain" rel="author" href="' . get_stylesheet_directory_uri() . '/humans.txt" />';

	echo apply_filters( 'wbt_humans', $humans );
 }
 add_action( 'wp_head', 'wbt_header_meta' );
