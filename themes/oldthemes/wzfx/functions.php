<?php
/**
 * WZFX functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package WZFX
 * @since 0.1.0
 */

/**
 * Enqueue scripts and styles for front-end.
 *
 * @since 0.1.0
 */
function wzfx_scripts_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

	wp_dequeue_style( 'greatermedia' );
	wp_deregister_style( 'greatermedia' );
	wp_enqueue_style( 'wzfx', get_stylesheet_directory_uri() . "/assets/css/wzfx{$postfix}.css", array(), GREATERMEDIA_VERSION );
}
add_action( 'wp_enqueue_scripts', 'wzfx_scripts_styles', 20 );
