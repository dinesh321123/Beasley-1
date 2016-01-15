<?php

/*
* Force URLs in srcset attributes into HTTPS scheme.
*/
function ssl_srcset( $sources ) {
	if (!is_ssl()) {
		return $sources;
	}

	if ( ! is_array( $sources ) ) {
		return $sources;
	}

	foreach ( $sources as &$source ) {
		if ( isset( $source['url'] ) ) {
			$source['url'] = set_url_scheme( $source['url'], 'https' );
		}
	}

	return $sources;
}

add_filter( 'wp_calculate_image_srcset', 'ssl_srcset' );