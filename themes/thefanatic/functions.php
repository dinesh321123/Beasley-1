<?php
/**
 * THEFANATIC functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package THEFANATIC
 * @since   0.1.1
 */

// Useful global constants
/*
 * Add this constant to wp-config and set value to "dev" to trigger time() as the cache buster on css/js that use this,
 * instead of the version - useful for dev, especially when cloudflare or other cdn's are involved
 */
if ( defined( 'GMR_THEFANATIC_ENV' ) && 'dev' == GMR_THEFANATIC_ENV ) {
	// So that things like cloudflare don't hold on to our css during dev
	define( 'THEFANATIC_VERSION', time() );
} else {
	define( 'THEFANATIC_VERSION', '0.3.3' ); /* Version bump by Steve 5/10/2016 @ 11:00 a.m. EST */
}

/**
 * Set up theme defaults and register supported WordPress features.
 *
 * @uses  load_theme_textdomain() For translation/localization support.
 *
 * @since 0.1.0
 */
function thefanatic_setup() {
	/**
	 * Makes THEFANATIC available for translation.
	 *
	 * Translations can be added to the /lang directory.
	 * If you're building a theme based on THEFANATIC, use a find and replace
	 * to change 'thefanatic' to the name of your theme in all template files.
	 */
	load_theme_textdomain( 'thefanatic', get_template_directory() . '/languages' );
}

add_action( 'after_setup_theme', 'thefanatic_setup' );

/**
 * Filter the Simpli-Fi script and make it async
 *
 * @param $tag
 * @param $handle
 * @param $src
 *
 * @return mixed|void
 */
function wpen_async_script( $tag, $handle, $src ) {
  if ( 'simpli-fi' !== $handle ) :
    return $tag;
  endif;

  return str_replace( '<script', '<script async ', $tag );
}

add_filter( 'script_loader_tag', 'wpen_async_script', 10, 3 );

/**
 * Enqueue scripts and styles for front-end.
 *
 * @since 0.1.0
 */
function thefanatic_scripts_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_script(
		'thefanatic',
		get_stylesheet_directory_uri() . "/assets/js/thefanatic{$postfix}.js",
		array(),
		THEFANATIC_VERSION,
		true
	);
	/**
	 * We are dequeueing and deregistering the parent theme's style sheets.
	 * The purpose for this is we are importing the parent's sass files into the child's sass files so that we can
	 * override variables and take advantage of the parent's mixin's, functions, placeholders, bourbon, and bourbon
	 * neat.
	 */
	wp_dequeue_style( 'greatermedia' );
	wp_deregister_style( 'greatermedia' );
	wp_enqueue_style(
		'thefanatic',
		get_stylesheet_directory_uri() . "/assets/css/thefanatic{$postfix}.css",
		array(
			'dashicons',
			'google-fonts'
		),
		THEFANATIC_VERSION
	);
	/* DISABLING DUE TO SIMPLIFI NETWORK ISSUE - WILL ENABLE ONCE FIXED - STEVE MEYERS - 11/13/15 */
	/*wp_enqueue_script(
		'simpli-fi',
		'http://i.simpli.fi/dpx.js?cid=23420&action=100&segment=fanatic&m=1&sifi_tuid=7537',
		array(),
		null,
		true
	);*/
}

add_action( 'wp_enqueue_scripts', 'thefanatic_scripts_styles', 20 );

/**
 * Add humans.txt to the <head> element.
 */
function thefanatic_header_meta() {
	$humans = '<link type="text/plain" rel="author" href="' . get_template_directory_uri() . '/humans.txt" />';
	echo apply_filters( 'thefanatic_humans', $humans );
}

add_action( 'wp_head', 'thefanatic_header_meta' );
