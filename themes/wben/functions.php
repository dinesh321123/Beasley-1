<?php
/**
 * WBEN functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package WBEN
 * @since 0.1.0
 */

 // Useful global constants
define( 'WBEN_VERSION', '0.2.4' ); /* Version bump by Steve 3/8/2016 @ 11:30 a.m. EST */

/**
* Set up theme defaults and register supported WordPress features.
*
* @uses load_theme_textdomain() For translation/localization support.
*
* @since 0.1.0
*/
function wben_setup() {
/**
 * Makes WBEN available for translation.
 *
 * Translations can be added to the /lang directory.
 * If you're building a theme based on WBEN, use a find and replace
 * to change 'wben' to the name of your theme in all template files.
 */
load_theme_textdomain( 'wben', get_stylesheet_directory_uri() . '/languages' );
}

add_action( 'after_setup_theme', 'wben_setup' );

/**
* Filter the Simpli-Fi script and make it async
*
* @param $tag
* @param $handle
* @param $src
*
* @return mixed|void
*/
function wben_async_script( $tag, $handle, $src ) {
  if ( 'simpli-fi' !== $handle ) :
    return $tag;
  endif;

  return str_replace( '<script', '<script async ', $tag );
}

add_filter( 'script_loader_tag', 'wben_async_script', 10, 3 );

/**
* Enqueue scripts and styles for front-end.
*
* @since 0.1.0
*/
function wben_scripts_styles() {
  $postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

  wp_dequeue_style( 'greatermedia' );
  wp_deregister_style( 'greatermedia' );
  wp_enqueue_style( 'wben', get_stylesheet_directory_uri() . "/assets/css/wben{$postfix}.css", array(), WBEN_VERSION );
  /* DISABLING DUE TO SIMPLIFI NETWORK ISSUE - WILL ENABLE ONCE FIXED - STEVE MEYERS - 11/13/15 */
  /*wp_enqueue_script(
    'simpli-fi',
    'http://i.simpli.fi/dpx.js?cid=23418&action=100&segment=benfem&m=1&sifi_tuid=7534',
    array(),
    null,
    true
  );*/
}

add_action( 'wp_enqueue_scripts', 'wben_scripts_styles', 20 );

/**
* Add humans.txt to the <head> element.
*/
function wben_header_meta() {
  $humans = '<link type="text/plain" rel="author" href="' . get_stylesheet_directory_uri() . '/humans.txt" />';
  echo apply_filters( 'wben_humans', $humans );
}

add_action( 'wp_head', 'wben_header_meta' );
