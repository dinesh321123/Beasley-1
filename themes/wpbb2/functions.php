<?php
/**
 * WPBB functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package WPBB
 * @since 0.1.0
 */

/**
 * Enqueue scripts and styles for front-end.
 *
 * @since 0.1.0
 */
function wpbb2_scripts_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

	wp_dequeue_style( 'greatermedia' );
	wp_deregister_style( 'greatermedia' );
	wp_enqueue_style( 'wpbb', get_stylesheet_directory_uri() . "/assets/css/wpbb{$postfix}.css", array(), GREATERMEDIA_VERSION );
}
add_action( 'wp_enqueue_scripts', 'wpbb2_scripts_styles', 20 );

function wpbb2_hide_frontend() {
	if ( ! is_user_logged_in() ) {
		status_header( 404 );
		exit;
	}
}

if ( time() < 1545800400 /* 2018-12-26 00:00:00 Easter */ ) {
	add_action( 'template_redirect', 'wpbb2_hide_frontend' );
}
