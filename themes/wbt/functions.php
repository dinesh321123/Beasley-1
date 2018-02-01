<?php
/**
 * WBT functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package WBT
 * @since 0.1.0
 */

/**
 * Set up theme defaults and register supported WordPress features.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 *
 * @since 0.1.0
 */
function wbt_setup() {
	add_image_size( 'gm-article-thumbnail-wbt', 1175, 776, array( 'center', 'top' ) ); // thumbnails used for articles
}
add_action( 'after_setup_theme', 'wbt_setup' );

/**
 * Enqueue scripts and styles for front-end.
 *
 * @since 0.1.0
 */
function wbt_scripts_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

	global $wp_styles;

	wp_dequeue_style( 'greatermedia' );
	wp_deregister_style( 'greatermedia' );
	wp_enqueue_style( 'wbt', get_stylesheet_directory_uri() . "/assets/css/wbt{$postfix}.css", array(), GREATERMEDIA_VERSION );
	wp_enqueue_style( 'wbt-ie', get_stylesheet_directory_uri() . "/assets/css/wbt_ie.css", array( 'wbt' ), GREATERMEDIA_VERSION );
	$wp_styles->add_data( 'wbt-ie', 'conditional', 'lte IE 9' );
	wp_enqueue_style( 'wbt-font', "https://fonts.googleapis.com/css?family=Lato", array(), GREATERMEDIA_VERSION );
	wp_enqueue_script( 'livefyre', '//cdn.livefyre.com/Livefyre.js', null, null, true );
	wp_enqueue_script( 'wbt', get_stylesheet_directory_uri() . "/assets/js/wbt{$postfix}.js", array( 'jquery', 'livefyre' ), GREATERMEDIA_VERSION, true );
	wp_enqueue_script( 'steel-media', 'https://ib.adnxs.com/seg?add=3513887&t=1', array(), null, true );
	wp_enqueue_script( 'handlebars', get_stylesheet_directory_uri() . '/assets/js/vendor/handlebars-v3.0.3.js', array( 'jquery' ) );
	wp_enqueue_script( 'quantcast', get_stylesheet_directory_uri() . '/assets/js/vendor/quantcast.js', array(), true );
	wp_enqueue_script( 'cxense', get_stylesheet_directory_uri() . '/assets/js/vendor/cxense.js', array(), false );
	wp_enqueue_script( 'googlemaps', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false', array() );
}
add_action( 'wp_enqueue_scripts', 'wbt_scripts_styles', 20 );

function add_featured_image_in_rss() {
	$featured_image = get_post_thumbnail_id();
	if ( $featured_image ) {
		$featured_image = current( wp_get_attachment_image_src( $featured_image, 'post-thumbnail' ) );
	}

	if ( ! empty( $featured_image ) ) {
		echo "\t" . '<enclosure url="' . esc_url( $featured_image ) . '" />' . "\n";
	}
}
add_action( 'rss2_item', 'add_featured_image_in_rss' );
