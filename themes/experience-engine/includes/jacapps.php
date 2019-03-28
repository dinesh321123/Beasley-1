<?php

add_action( 'wp_loaded', 'ee_setup_jacapps' );

if ( ! function_exists( 'ee_is_jacapps' ) ) :
	function ee_is_jacapps() {
		static $jacapps_pos = null;

		if ( $jacapps_pos === null ) {
			$jacapps_pos = stripos( $_SERVER['HTTP_USER_AGENT'], 'jacapps' );
		}

		return false !== $jacapps_pos;
	}
endif;

if ( ! function_exists( 'ee_setup_jacapps' ) ) :
	function ee_setup_jacapps() {
		if ( ! ee_is_jacapps() ) {
			return;
		}

		add_action( 'wp_print_scripts', 'ee_jacapps_enqueue_scripts', 99 );

		add_filter( 'body_class', 'ee_jacapps_body_class' );
		add_filter( 'omny_embed_key', 'ee_update_jacapps_omny_key' );
		add_filter( 'secondstreet_embed_html', 'ee_update_jacapps_secondstreet_html', 10, 2 );

		remove_filter( 'omny_embed_html', 'ee_update_omny_embed' );
	}
endif;

if ( function_exists( 'vary_cache_on_function' ) ) :
	// batcache variant
	vary_cache_on_function( 'return (bool) preg_match("/jacapps/i", $_SERVER["HTTP_USER_AGENT"]);' );
endif;

if ( ! function_exists( 'ee_jacapps_body_class' ) ) :
	function ee_jacapps_body_class( $classes ) {
		$classes[] = 'jacapps';
		return $classes;
	}
endif;

if ( ! function_exists( 'ee_jacapps_enqueue_scripts' ) ) :
	function ee_jacapps_enqueue_scripts() {
		wp_dequeue_script( 'ee-app' );
		wp_enqueue_script( 'iframe-resizer' );
		wp_enqueue_script( 'embedly-player.js' );
	}
endif;

if ( ! function_exists( 'ee_update_jacapps_omny_key' ) ) :
	function ee_update_jacapps_omny_key( $key ) {
		return $key . ':jacapps';
	}
endif;

if ( ! function_exists( 'ee_update_jacapps_secondstreet_html' ) ) :
	function ee_update_jacapps_secondstreet_html( $embed, $atts ) {
		$url = 'https://embed-' . rawurlencode( $atts['op_id'] ) . '.secondstreetapp.com/Scripts/dist/embed.js';
		return '<script src="' . esc_url( $url ) . '" data-ss-embed="promotion" data-opguid="' . esc_attr( $atts['op_guid'] ) . '" data-routing="' . esc_attr( $atts['routing'] ) . '"></script>';
	}
endif;
