<?php

add_action( 'after_setup_theme', 'ee_setup_theme' );
add_action( 'init', 'ee_register_nav_menus' );
add_action( 'wp_head', 'wp_enqueue_scripts', 2 );
add_action( 'wp_head', '_wp_render_title_tag', 2 );
add_filter( 'body_class', 'ee_login_body_class' );

remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wp_enqueue_scripts', 1 );
remove_action( 'wp_head', '_wp_render_title_tag', 1 );
remove_action( 'do_pings', 'do_all_pings' );

add_filter( 'pre_get_posts','ee_update_main_query' );

if ( ! function_exists( 'ee_setup_theme' ) ) :
	function ee_setup_theme() {
		add_theme_support( 'custom-logo' );
		add_theme_support( 'title-tag' );

		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'post-formats', array( 'gallery', 'link', 'image', 'video', 'audio' ) );

		add_theme_support( 'html5', array( 'search-form' ) );

		add_theme_support( 'secondstreet' );
		add_theme_support( 'homepage-curation' );
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

if ( ! function_exists( 'ee_update_main_query' ) ) :
	function ee_update_main_query( $query ) {
		if ( ! $query->is_main_query() ) {
			return $query;
		}

		if ( $query->is_search() ) {
			$query->set( 'posts_per_page', 12 );
		} elseif ( $query->is_tag() ) {
			$post_type = $query->get( 'post_type' );
			if ( ! is_array( $post_type ) ) {
				$post_type = array( $post_type );
			}

			$post_type[] = 'gmr_gallery';
			$post_type[] = 'gmr_album';
			$post_type[] = 'contest';

			$query->set( 'post_type', $post_type );
		}

		return $query;
	}
endif;

if ( ! function_exists( 'ee_login_body_class' ) ) :
	function ee_login_body_class( $classes ) {

		if ( 'disabled' === get_option( 'ee_login', '' ) ) {
			$classes[] = 'hidden-user-nav';
		}

		return $classes;
	}
endif;


/**
 * Adds Embedly global script to each page to ensure no broken embeds.
 */
if ( ! function_exists( 'add_embedly_global_script' ) ) :
	function add_embedly_global_script() {
		if ( class_exists( 'WP_Embedly' ) ) {
			?>

			<script>
			(function(w, d){
			var id='embedly-platform', n = 'script';
			if (!d.getElementById(id)){
				w.embedly = w.embedly || function() {(w.embedly.q = w.embedly.q || []).push(arguments);};
				var e = d.createElement(n); e.id = id; e.async=1;
				e.src = ('https:' === document.location.protocol ? 'https' : 'http') + '://cdn.embedly.com/widgets/platform.js';
				var s = d.getElementsByTagName(n)[0];
				s.parentNode.insertBefore(e, s);
			}
			})(window, document);
			</script>

			<?php
		}
	}
endif;
add_action( 'wp_head' , 'add_embedly_global_script' );