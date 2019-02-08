<?php
/**
 * FoxSports functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package FoxSports
 * @since 0.1.0
 */

/**
 * Enqueue scripts and styles for front-end.
 *
 * @since 0.1.0
 */
function foxsports_scripts_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

	wp_dequeue_style( 'greatermedia' );
	wp_deregister_style( 'greatermedia' );
	wp_enqueue_style( 'foxsports', get_stylesheet_directory_uri() . "/assets/css/foxsports{$postfix}.css", array(), GREATERMEDIA_VERSION );
}
add_action( 'wp_enqueue_scripts', 'foxsports_scripts_styles', 20 );

function foxsports_hide_frontend() {
	$force = get_option( 'foxsportsradiocharlotte_public' );
	$force = filter_var( $force, FILTER_VALIDATE_BOOLEAN );

	if ( ! $force && ! is_user_logged_in() ) {
		status_header( 404 );
		exit;
	}
}

if ( time() < 1549256400 /* 2019-02-04 00:00:00 Eastern Time */ ) {
	add_action( 'template_redirect', 'foxsports_hide_frontend' );
}

function foxsports_register_settings( $group, $page ) {
	$section_id = 'foxsports_settings';

	add_settings_section( $section_id, 'foxsportsradiocharlotte.com', '__return_false', $page );
	add_settings_field( 'foxsportsradiocharlotte_public', 'Force Public', 'foxsports_render_public_setting_field', $page, $section_id );
	register_setting( $group, 'foxsportsradiocharlotte_public', 'intval' );
}
add_action( 'bbgi_register_settings', 'foxsports_register_settings', 10, 2 );
add_action( 'bbgi_register_settings', 'foxsports_register_settings', 10, 2 );

function foxsports_render_public_setting_field() {
	$value = get_option( 'foxsportsradiocharlotte_public' );
	$value = filter_var( $value, FILTER_VALIDATE_BOOLEAN );

	echo '<label>';
		echo '<input type="checkbox" name="foxsportsradiocharlotte_public" value="1"', checked( $value, true, false ), '>';
		echo 'Force the site to be public.';
	echo '</label>';
}
