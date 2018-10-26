<?php

require_once __DIR__ . '/includes/experience-engine.php';

function ee_setup_theme() {
	add_theme_support( 'custom-logo' );
	add_theme_support( 'title-tag' );

	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'post-formats', array( 'gallery', 'link', 'image', 'video', 'audio' ) );

	add_theme_support( 'html5', array( 'search-form' ) );

	add_theme_support( 'secondstreet' );
	add_theme_support( 'firebase' );
}

add_action( 'after_setup_theme', 'ee_setup_theme' );

function ee_register_nav_menus() {
	register_nav_menus( array(
		'primary-nav' => 'Primary Navigation',
		'about-nav'   => 'Footer: About Menu',
		'connect-nav' => 'Footer: Connect Menu',
	) );
}

add_action( 'init', 'ee_register_nav_menus' );

function ee_enqueue_front_scripts() {
	$base = untrailingslashit( get_template_directory_uri() );

	wp_enqueue_style( 'ee-app', "{$base}/bundle/app.css", null, null );

	wp_register_script( 'td-sdk', '//sdk.listenlive.co/web/2.9/td-sdk.min.js', null, null, true );
	wp_register_script( 'ee-app-vendors', "{$base}/bundle/vendors-app.js", null, null, true );
	wp_enqueue_script( 'ee-app', "{$base}/bundle/app.js", array( 'td-sdk', 'ee-app-vendors' ), null, true );

	wp_localize_script( 'ee-app', 'bbgiconfig', array(
		'firebase'    => apply_filters( 'firebase_settings', array() ),
		'livePlayer' => array(
			'streams' => function_exists( 'gmr_streams_get_public_streams' ) ? gmr_streams_get_public_streams() : array(),
		),
	) );
}

add_action( 'wp_enqueue_scripts', 'ee_enqueue_front_scripts' );

function font_loader() { ?>
	<script type="text/javascript">
		var WebFontConfig = {
			google: { families: [
				'Libre+Franklin:300,400,500,600,700',
			] }
		};
	</script>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/webfont/1/webfont.js" async></script>

	<noscript>
		<link href="https://fonts.googleapis.com/css?family=Libre+Franklin:300,400,500,600,700" rel="stylesheet">
	</noscript><?php
}

add_action( 'wp_head', 'font_loader', 0 );
add_filter( 'wp_audio_shortcode_library', '__return_false' );
