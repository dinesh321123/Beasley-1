<?php

/**
 * Adds rocketloader attribute which will be used by CloudFlare to load script asyncrhoniously.
 *
 * @link https://support.cloudflare.com/hc/en-us/articles/200168056-What-does-Rocket-Loader-do-
 *
 * @global WP_Scripts $wp_scripts
 * @param string $tag Script tag.
 * @param string $handle Script handle.
 * @return string Updated script tag if it contains "rocketloader" flag, otherwise initial tag.
 */
function add_rocketloader_for_script( $tag, $handle ) {
	global $wp_scripts;

	$rocketloaded = $wp_scripts->get_data( $handle, 'rocketloader' );
	if ( filter_var( $rocketloaded, FILTER_VALIDATE_BOOLEAN ) ) {
		$tag = str_replace( '<script ', '<script data-cfasync="true" ', $tag );
	}

	return $tag;
}
add_filter( 'script_loader_tag', 'add_rocketloader_for_script', 10, 2 );

/**
 * Marks script to be loaded with rocketloader attribute.
 *
 * @see add_rocketloader_for_script()
 *
 * @global WP_Scripts $wp_scripts
 * @param string $handle The script handle.
 */
function wp_rocketloader_script( $handle ) {
	global $wp_scripts;
	$wp_scripts->add_data( $handle, 'rocketloader', true );
}

/**
 * Unmarks script to be loaded with rocketloader attribute.
 *
 * @see add_rocketloader_for_script()
 *
 * @global WP_Scripts $wp_scripts
 * @param string $handle The script handle.
 */
function wp_derocketloader_script( $handle ) {
	global $wp_scripts;
	$wp_scripts->add_data( $handle, 'rocketloader', false );
}