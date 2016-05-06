<?php
/**
 * WMGC functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package WMGC
 * @since   0.1.0
 */

// Useful global constants
/*
 * Add this constant to wp-config and set value to "dev" to trigger time() as the cache buster on css/js that use this,
 * instead of the version - useful for dev, especially when cloudflare or other cdn's are involved
 */
if ( defined( 'WMGC_ENV' ) && 'dev' == WMGC_ENV ) {
	// So that things like cloudflare don't hold on to our css during dev
	define( 'WMGC_VERSION', time() );
} else {
	define( 'WMGC_VERSION', '0.3.4' ); /* Version bump by Steve 3/8/2016 @ 11:30 a.m. EST */
}

/**
 * Set up theme defaults and register supported WordPress features.
 *
 * @uses  load_theme_textdomain() For translation/localization support.
 *
 * @since 0.1.0
 */
function wmgc_setup() {
	/**
	 * Makes WMGC available for translation.
	 *
	 * Translations can be added to the /lang directory.
	 * If you're building a theme based on WMGC, use a find and replace
	 * to change 'wmgc' to the name of your theme in all template files.
	 */
	load_theme_textdomain( 'wmgc', get_template_directory() . '/languages' );
}

add_action( 'after_setup_theme', 'wmgc_setup' );

/**
 * Enqueue scripts and styles for front-end.
 *
 * @since 0.1.0
 */
function wmgc_scripts_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_script(
		'wmgc',
		get_stylesheet_directory_uri() . "/assets/js/wmgc{$postfix}.js",
		array(),
		WMGC_VERSION,
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
		'wmgc',
		get_stylesheet_directory_uri() . "/assets/css/wmgc{$postfix}.css",
		array(
			'dashicons',
			'google-fonts'
		),
		WMGC_VERSION
	);
}

add_action( 'wp_enqueue_scripts', 'wmgc_scripts_styles', 20 );

/**
 * Add humans.txt to the <head> element.
 */
function wmgc_header_meta() {
	$humans = '<link type="text/plain" rel="author" href="' . get_template_directory_uri() . '/humans.txt" />';

	echo apply_filters( 'wmgc_humans', $humans );
}

add_action( 'wp_head', 'wmgc_header_meta' );

/**
* Add Chartbeat to site.
*/
function wmgc_chartbeat_header() {
	$content = '<script type="text/javascript">var _sf_startpt=(new Date()).getTime()</script>';

	echo apply_filters( 'wmgc_chartbeat_header', $content );
}
add_action( 'wp_head', 'wmgc_chartbeat_header' );

function wmgc_chartbeat_footer() {
	$content = '<script type="text/javascript">var _sf_async_config={uid:2332,domain:"detroitsports1051.com",useCanonical:true};(function(){function loadChartbeat() {window._sf_endpt=(new Date()).getTime();var e = document.createElement(\’script\’);e.setAttribute(\’language\’, \’javascript\’);e.setAttribute(\’type\’, \’text/javascript\’);e.setAttribute(\’src\’, \’//static.chartbeat.com/js/chartbeat.js\');document.body.appendChild(e);}var oldonload = window.onload;window.onload = (typeof window.onload != \’function\’) ?loadChartbeat : function() { oldonload(); loadChartbeat(); };})();</script>';
	echo apply_filters( 'wmgc_chartbeat_footer', $content );
}
add_action( 'wp_footer', 'wmgc_chartbeat_footer' );
