<?php
/**
 * WMGK functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package WMGK
 * @since   0.1.1
 */

// Useful global constants
/*
 * Add this constant to wp-config and set value to "dev" to trigger time() as the cache buster on css/js that use this,
 * instead of the version - useful for dev, especially when cloudflare or other cdn's are involved
 */
if ( defined( 'GMR_WMGK_ENV' ) && 'dev' == GMR_WMGK_ENV ) {
	// So that things like cloudflare don't hold on to our css during dev
	define( 'WMGK_VERSION', time() );
} else {
	define( 'WMGK_VERSION', '1.5.5' ); /* Version bump by Steve 4/4/2016 @ 1:00 p.m. EST */
}

/**
 * Set up theme defaults and register supported WordPress features.
 *
 * @uses  load_theme_textdomain() For translation/localization support.
 *
 * @since 0.1.0
 */
function wmgk_setup() {
	/**
	 * Makes WMGK available for translation.
	 *
	 * Translations can be added to the /lang directory.
	 * If you're building a theme based on WMGK, use a find and replace
	 * to change 'wmgk' to the name of your theme in all template files.
	 */
	load_theme_textdomain( 'wmgk', get_template_directory() . '/languages' );
}

add_action( 'after_setup_theme', 'wmgk_setup' );

/**
 * Filter the Simpli-Fi script and make it async
 *
 * @param $tag
 * @param $handle
 * @param $src
 *
 * @return mixed|void
 */
function wmgk_async_script( $tag, $handle, $src ) {
    if ( 'simpli-fi' !== $handle ) :
      return $tag;
    endif;

    return str_replace( '<script', '<script async ', $tag );
}

add_filter( 'script_loader_tag', 'wmgk_async_script', 10, 3 );

/**
 * Enqueue scripts and styles for front-end.
 *
 * @since 0.1.0
 */
function wmgk_scripts_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

	/**
	 * We are dequeueing and deregistering the parent theme's style sheets.
	 * The purpose for this is we are importing the parent's sass files into the child's sass files so that we can
	 * override variables and take advantage of the parent's mixin's, functions, placeholders, bourbon, and bourbon
	 * neat.
	 */
	wp_dequeue_style( 'greatermedia' );
	wp_deregister_style( 'greatermedia' );
	wp_enqueue_style(
		'wmgk',
		get_stylesheet_directory_uri() . "/assets/css/wmgk{$postfix}.css",
		array(
			'google-fonts'
		),
		WMGK_VERSION
	);
	/* DISABLING DUE TO SIMPLIFI NETWORK ISSUE - WILL ENABLE ONCE FIXED - STEVE MEYERS - 11/13/15 */
	/*wp_enqueue_script(
		'simpli-fi',
		'http://i.simpli.fi/dpx.js?cid=23419&action=100&segment=classicrockmgk&m=1&sifi_tuid=7536',
		array(),
		null,
		true
	);*/
}

add_action( 'wp_enqueue_scripts', 'wmgk_scripts_styles', 20 );
