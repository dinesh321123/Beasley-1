<?php

/* function wpdocs_deregister_section( $wp_customize ) {
	print_r($wp_customize); exit;
	$wp_customize->remove_section( 'colors' );
}
add_action( 'admin_init', 'admininitfunciton' );
function admininitfunciton(){
	add_action( 'customize_register', 'wpdocs_deregister_section', 999 );
}
function themeslug_customize_register( $wp_customize ) {
	// Do stuff with $wp_customize, the WP_Customize_Manager object.
	echo "Working"; exit;
}
add_action( 'customize_register', 'themeslug_customize_register' );
*/
add_action( 'after_setup_theme', 'ee_setup_theme' );
add_action( 'init', 'ee_register_nav_menus' );
add_action( 'wp_head', 'wp_enqueue_scripts', 2 );
add_action( 'wp_head', '_wp_render_title_tag', 2 );

remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wp_enqueue_scripts', 1 );
remove_action( 'wp_head', '_wp_render_title_tag', 1 );
remove_action( 'do_pings', 'do_all_pings' );

add_filter( 'body_class', 'ee_login_body_class' );
add_filter( 'pre_get_posts','ee_update_main_query' );
add_filter( 'bbgi_supported_featured_image_layouts', 'ee_supported_featured_image_layouts' );

if ( ! function_exists( 'ee_setup_theme' ) ) :
	function ee_setup_theme() {
		add_theme_support( 'custom-logo' );
		add_theme_support( 'title-tag' );

		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'post-formats', array( 'gallery', 'link', 'image', 'video', 'audio' ) );

		add_theme_support( 'html5', array( 'search-form' ) );

		add_theme_support( 'secondstreet' );
		add_theme_support( 'homepage-curation' );

		add_post_type_support( 'post', 'timed-content' );
		add_post_type_support( 'page', 'timed-content' );
		add_post_type_support( 'gmr_gallery', 'timed-content' );
		add_post_type_support( 'listicle_cpt', 'timed-content' );
		add_post_type_support( 'gmr_album', 'timed-content' );
		add_post_type_support( 'episode', 'timed-content' );
		add_post_type_support( 'tribe_events', 'timed-content' );
		add_post_type_support( 'contest', 'timed-content' );

		add_post_type_support( 'post', 'flexible-feature-image' );
		add_post_type_support( 'page', 'flexible-feature-image' );
		add_post_type_support( 'tribe_events', 'flexible-feature-image' );
		add_post_type_support( 'contest', 'flexible-feature-image' );
		add_post_type_support( 'gmr_gallery', 'flexible-feature-image' );
		add_post_type_support( 'listicle_cpt', 'flexible-feature-image' );
		add_post_type_support( 'affiliate_marketing', 'flexible-feature-image' );
	}
endif;

if ( ! function_exists( 'ee_register_nav_menus' ) ) :
	function ee_register_nav_menus() {
		register_nav_menus( array(
			'primary-nav' => 'Primary Navigation',
			'about-nav'   => 'Footer: About Menu',
			'connect-nav' => 'Footer: Connect Menu',
			'listen-live-nav' => 'Listen Live: On Air now',
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
		} elseif ( $query->is_tag() || $query->is_category() ) {
			$post_type = $query->get( 'post_type' );
			if ( ! is_array( $post_type ) ) {
				$post_type = array( $post_type );
			}

			$post_types_array = get_post_types( '', 'names' );
			$exclude_post_type = array('show', 'gmr_homepage', 'gmr_mobile_homepage', 'page', 'nav_menu_item', 'user_request', 'acf-field-group', 'acf-field', 'cmm-redirect', 'attachment', 'revision', 'custom_css', 'customize_changeset', 'oembed_cache', 'live-stream', 'songs', 'redirect_rule' );
			foreach ( $post_types_array as $posttype ) {
				if( !in_array( $posttype, $exclude_post_type ) )
				{
					$post_type[] = $posttype;
				}
			}

			$query->set( 'post_type', $post_type );
		}

		if( $query->is_archive() && $query->is_category() ) {
			$query->set( 'posts_per_page', 24 );
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

if ( ! function_exists( 'ee_supported_featured_image_layouts' ) ) :
	function ee_supported_featured_image_layouts() {
		return array( 'top', 'inline' );
	}
endif;

/**
 * Helper function to get the post id from options or transient cache
 *
 * @param $query_arg
 *
 * @return int post id if found
 */
function get_post_with_keyword( $query_arg ) {
	$query_arg = strtolower( $query_arg );
	if( class_exists('GreaterMedia_Keyword_Admin') ) {
		$saved_keyword = GreaterMedia_Keyword_Admin::get_keyword_options( GreaterMedia_Keyword_Admin::$plugin_slug . '_option_name' );
		$saved_keyword = GreaterMedia_Keyword_Admin::array_map_r( 'sanitize_text_field', $saved_keyword );

		if( $query_arg != '' && array_key_exists( $query_arg, $saved_keyword ) ) {
			return $saved_keyword[$query_arg]['post_id'];
		}
	}
	return 0;
}
