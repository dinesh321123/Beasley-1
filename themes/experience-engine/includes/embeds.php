<?php

add_action( 'beasley_after_body', 'ee_setup_embed_filters' );

if ( ! function_exists( 'ee_setup_embed_filters' ) ) :
	function ee_setup_embed_filters() {
		add_filter( 'embed_oembed_html', 'ee_update_embed_oembed_html', 10, 4 );
		add_filter( 'fvideos_video_html', 'ee_update_fvideos_video_html', 10, 2 );
	}
endif;

if ( ! function_exists( 'ee_update_embed_oembed_html' ) ) :
	function ee_update_embed_oembed_html( $html, $url, $attr, $post_ID ) {
		$data = wp_cache_get( $url, 'ee:oembed' );
		if ( empty( $data ) ) {
			$data = _wp_oembed_get_object()->get_data( $url );
			wp_cache_set( $url, $data, 'ee:oembed' );
		}

		if ( $data->provider_name == 'Facebook' && preg_match( '#[^\'"]+connect.facebook.net[^\'"]+#i', $html, $matches ) ) {
			$fb_connect = filter_var( $matches[0], FILTER_VALIDATE_URL );
			if ( $fb_connect ) {
				$html = preg_replace( '#<script.*?>.*?</script>#i', '<script src="' . esc_attr( $fb_connect ) . '"></script>', $html );
			}
		} elseif ( $data->provider_name == 'YouTube' ) {
			$html = ee_oembed_youtube_html( $data );
		}

		// we need to make sure that wpautop filter doesn't mess everything up here, so we need to inject embed code later
		$placeholder = '<div><!-- ' . sha1( $html ) . ' --></div>';
		$replace_filter = function( $content ) use ( $placeholder, $html ) {
			return str_replace( $placeholder, $html, $content );
		};

		add_filter( 'the_content', $replace_filter, 150 );

		return $placeholder;
	}
endif;

if ( ! function_exists( 'ee_update_fvideos_video_html' ) ) :
	function ee_update_fvideos_video_html( $html, $data ) {
		return ee_oembed_youtube_html( $data );
	}
endif;

if ( ! function_exists( 'ee_oembed_youtube_html' ) ) :
	function ee_oembed_youtube_html( $data ) {
		$data = (object) $data;

		return sprintf(
			'<div class="youtube" data-title="%s" data-thumbnail="%s" data-html="%s"></div>',
			esc_attr( $data->title ),
			esc_attr( $data->thumbnail_url ),
			esc_attr( $data->html )
		);
	}
endif;
