<?php

add_filter( 'embed_oembed_html', 'ee_update_embed_oembed_html', 10, 4 );

if ( ! function_exists( 'ee_update_embed_oembed_html' ) ) :
	function ee_update_embed_oembed_html( $html, $url, $attr, $post_ID ) {
		$host = parse_url( $url, PHP_URL_HOST );
		if ( stripos( $host, 'facebook.com' ) !== false ) {
			if ( preg_match( '#[^\'"]+connect.facebook.net[^\'"]+#i', $html, $matches ) ) {
				$fb_connect = filter_var( $matches[0], FILTER_VALIDATE_URL );
				if ( $fb_connect ) {
					$html = preg_replace( '#<script.*?>.*?</script>#i', '<script src="' . esc_attr( $fb_connect ) . '"></script>', $html );
				}
			}
		}

		return $html;
	}
endif;
