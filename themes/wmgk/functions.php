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
	define( 'WMGK_VERSION', '1.0.19' );
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
 * Enqueue scripts and styles for front-end.
 *
 * @since 0.1.0
 */
function wmgk_scripts_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

	/*
	 * Commented out because wmgk.js is an empty file.
	 * The JS file and this statement remain so that future child-theme-specific JS can be added
	 * with ease and best practices. Until then, saving the http request.
	 */
//	wp_enqueue_script(
//		'wmgk',
//		get_stylesheet_directory_uri() . "/assets/js/wmgk{$postfix}.js",
//		array(),
//		WMGK_VERSION,
//		true
//	);


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
}

add_action( 'wp_enqueue_scripts', 'wmgk_scripts_styles', 20 );
