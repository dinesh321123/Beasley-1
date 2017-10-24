<?php

add_filter( 'wp_get_attachment_url', function( $url, $post_id ) {
	if ( defined( 'BEASLEY_IMAGE_DOMAIN' ) && BEASLEY_IMAGE_DOMAIN ) {
		$post = get_post( $post_id );

		if ( strpos( $post->post_mime_type, 'image' ) !== false ) {
			$parts = parse_url( $url );

			$url = str_replace( $parts['host'], BEASLEY_IMAGE_DOMAIN, $url );
		}
	}

	return $url;
}, 1, 2 );
