<?php

/**
 * Checks if Geo Targetly is enabled. Defaults to false if option is
 * missing from database.
 */
function ee_is_geotargetly_enabled() {
	$enabled = get_option( 'ee_geotargetly_enabled', 'false' );
	$enabled = filter_var( $enabled, FILTER_VALIDATE_BOOLEAN );

	return $enabled;
}

/**
 * Outputs Geo Targetly Embed Code if,
 *
 * 1. Geo Targetly Enabled in Station Settings
 * 2. Geo Targetly Embed Code provided in Station Settings
 *
 * We must output this even if the requested route does not have
 * geotargetly because we need to load the geotargetly global JS
 * function for subsequent page loads.
 */
function ee_geotargetly_if() {
	if ( ee_is_geotargetly_enabled() ) {
		$embed_code = get_option( 'ee_geotargetly_embed_code' );

		if ( ! empty( $embed_code ) ) {
			// Assumes that the Geotargetly Embed Code is adjusted to use
			// the window.geotargetly global
			echo "\n\n";
			echo $embed_code; // XSS Ok - Geo Targetly Embed Code is trusted
			echo "\n\n";

			if ( ee_current_page_needs_geotargetly() ) {
				echo "\n\n";
				echo "<script>geotargetly ? geotargetly( document,'script','style','head' ) : 0;</script>";
				echo "\n\n";
			}
		}

	}
}

/**
 * Checks if the current page needs Geo Targetly. Currently limited to
 * the D&C Show & related pages.
 */
function ee_current_page_needs_geotargetly() {
	global $wp;
	$request = trailingslashit( '/' . $wp->request );

	return ee_is_geotargetly_enabled() &&
		(
			(
				/* Post tagged with D&C category */
				is_single() && in_category( 'dave-chuck-the-freak' )
			) ||
			(
				/* D&C Show Page */
				is_single() && get_query_var( 'show' ) === 'dave-and-chuck'
			) ||
			(
				/* D&C Category Archive */
				is_archive() && is_category( 'dave-chuck-the-freak' )
			) ||
			(
				/* Sub Pages of D&C Show Page */
				stripos( $request, '/shows/dave-and-chuck/' ) !== false
			)
		);
}

add_action( 'wp_head', 'ee_geotargetly_if', 0 );
