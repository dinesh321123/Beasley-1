<?php
/**
 * WMJX functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package WMJX
 * @since 0.1.0
 */

 // Useful global constants
 define( 'WMJX_VERSION', '0.3.0' ); /* Version bump by Denis 4/29/2016 @ 2:30p.m. EST */

 /**
  * Set up theme defaults and register supported WordPress features.
  *
  * @uses load_theme_textdomain() For translation/localization support.
  *
  * @since 0.1.0
  */
 function wmjx_setup() {
	/**
	 * Makes WMJX available for translation.
	 *
	 * Translations can be added to the /lang directory.
	 * If you're building a theme based on WMJX, use a find and replace
	 * to change 'wmjx' to the name of your theme in all template files.
	 */
	load_theme_textdomain( 'wmjx', get_stylesheet_directory_uri() . '/languages' );
 }
 add_action( 'after_setup_theme', 'wmjx_setup' );

 /**
  * Enqueue scripts and styles for front-end.
  *
  * @since 0.1.0
  */
 function wmjx_scripts_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

	wp_dequeue_style( 'greatermedia' );
	wp_deregister_style( 'greatermedia' );
	wp_enqueue_style( 'wmjx', get_stylesheet_directory_uri() . "/assets/css/wmjx{$postfix}.css", array(), WMJX_VERSION );
 }
 add_action( 'wp_enqueue_scripts', 'wmjx_scripts_styles', 20 );

 /**
  * Add humans.txt to the <head> element.
  */
 function wmjx_header_meta() {
	$humans = '<link type="text/plain" rel="author" href="' . get_stylesheet_directory_uri() . '/humans.txt" />';

	echo apply_filters( 'wmjx_humans', $humans );
 }
 add_action( 'wp_head', 'wmjx_header_meta' );

 /**
  * Pinterest function
  */
function add_pinterest_meta_tag() {
  echo '<meta name="p:domain_verify" content="a7841aca042c739e84d1143a2f671ef8"/>';
}
add_action('wp_head', 'add_pinterest_meta_tag');