<?php

add_filter( 'embed_oembed_html', 'ee_update_embed_oembed_html', 10, 4 );

if ( ! function_exists( 'ee_update_embed_oembed_html' ) ) :
	function ee_update_embed_oembed_html( $html, $url, $attr, $post_ID ) {
		return $html;
	}
endif;
