<?php

add_action( 'after_setup_theme', 'ee_setup_theme' );
add_action( 'init', 'ee_register_nav_menus' );

remove_action( 'wp_head', 'wp_generator' );
remove_action( 'do_pings', 'do_all_pings' );

if ( ! function_exists( 'ee_setup_theme' ) ) :
	function ee_setup_theme() {
		add_theme_support( 'custom-logo' );
		add_theme_support( 'title-tag' );

		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'post-formats', array( 'gallery', 'link', 'image', 'video', 'audio' ) );

		add_theme_support( 'html5', array( 'search-form' ) );

		add_theme_support( 'secondstreet' );
	}
endif;

if ( ! function_exists( 'ee_register_nav_menus' ) ) :
	function ee_register_nav_menus() {
		register_nav_menus( array(
			'primary-nav' => 'Primary Navigation',
			'about-nav'   => 'Footer: About Menu',
			'connect-nav' => 'Footer: Connect Menu',
		) );
	}
endif;
