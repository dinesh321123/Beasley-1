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
 * @since   0.1.0
 */

// Useful global constants
define( 'WMGK_VERSION', '0.1.0' );

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

	wp_enqueue_script(
		'wmgk',
		get_stylesheet_directory_uri() . "/assets/js/wmgk{$postfix}.js",
		array(),
		WMGK_VERSION,
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
		'wmgk',
		get_stylesheet_directory_uri() . "/assets/css/wmgk{$postfix}.css",
		array(
			'dashicons',
			'open-sans',
			'droid-sans',
			'font-awesome'
		),
		WMGK_VERSION
	);
}

add_action( 'wp_enqueue_scripts', 'wmgk_scripts_styles', 20 );

/**
 * Add humans.txt to the <head> element.
 */
function wmgk_header_meta() {
	$humans = '<link type="text/plain" rel="author" href="' . get_template_directory_uri() . '/humans.txt" />';

	echo apply_filters( 'wmgk_humans', $humans );
}

add_action( 'wp_head', 'wmgk_header_meta' );