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
	$force = get_option( '987theshark_public' );
	$force = filter_var( $force, FILTER_VALIDATE_BOOLEAN );

	if ( ! $force && ! is_user_logged_in() ) {
		status_header( 404 );
		exit;
	}
}

if ( time() < 1545800400 /* 2018-12-26 00:00:00 Easter */ ) {
	add_action( 'template_redirect', 'wpbb2_hide_frontend' );
}

function wpbb2_register_settings( $group, $page ) {
	$section_id = 'wpbb2_settings';

	add_settings_section( $section_id, '987theshark.com', '__return_false', $page );
	add_settings_field( '987theshark_public', 'Force Public', 'wpbb2_render_public_setting_field', $page, $section_id );
	register_setting( $group, '987theshark_public', 'intval' );
}
add_action( 'bbgi_register_settings', 'wpbb2_register_settings', 10, 2 );
add_action( 'beasley-register-settings', 'wpbb2_register_settings', 10, 2 );

function wpbb2_render_public_setting_field() {
	$value = get_option( '987theshark_public' );
	$value = filter_var( $value, FILTER_VALIDATE_BOOLEAN );

	echo '<label>';
		echo '<input type="checkbox" name="987theshark_public" value="1"', checked( $value, true, false ), '>';
		echo 'Force the site to be public.';
	echo '</label>';
}
