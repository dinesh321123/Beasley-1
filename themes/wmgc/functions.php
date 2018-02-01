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

/**
 * Enqueue scripts and styles for front-end.
 *
 * @since 0.1.0
 */
function wmgc_scripts_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_script( 'livefyre', '//cdn.livefyre.com/Livefyre.js', null, null, true );
	wp_enqueue_script( 'wmgc', get_stylesheet_directory_uri() . "/assets/js/wmgc{$postfix}.js", array( 'livefyre' ), GREATERMEDIA_VERSION, true );

	/**
	 * We are dequeueing and deregistering the parent theme's style sheets.
	 * The purpose for this is we are importing the parent's sass files into the child's sass files so that we can
	 * override variables and take advantage of the parent's mixin's, functions, placeholders, bourbon, and bourbon
	 * neat.
	 */
	wp_dequeue_style( 'greatermedia' );
	wp_deregister_style( 'greatermedia' );
	wp_enqueue_style( 'wmgc', get_stylesheet_directory_uri() . "/assets/css/wmgc{$postfix}.css", array( 'dashicons', 'google-fonts' ), GREATERMEDIA_VERSION );
}
add_action( 'wp_enqueue_scripts', 'wmgc_scripts_styles', 20 );

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
