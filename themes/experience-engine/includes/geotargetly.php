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
 * 2. Current Page Needs Geo Targetly
 * 3. Geo Targetly Embed Code provided in Station Settings
 */
function ee_geotargetly_if() {
	if ( ee_is_geotargetly_enabled() ) {
		$embed_code = get_option( 'ee_geotargetly_embed_code' );

		if ( ! empty( $embed_code ) ) {
			echo "\n\n";
			echo $embed_code; // XSS Ok - Geo Targetly Embed Code is trusted
			echo "\n\n";
		}
	}
}

add_action( 'wp_head', 'ee_geotargetly_if', 0 );
