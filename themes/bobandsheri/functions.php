<?php
/**
 * BOBANDSHERI functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package BOBANDSHERI
 * @since 0.1.0
 */

/**
 * Enqueue scripts and styles for front-end.
 *
 * @since 0.1.0
 */
function bobandsheri_scripts_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

	wp_dequeue_style( 'greatermedia' );
	wp_deregister_style( 'greatermedia' );
	wp_enqueue_style( 'bobandsheri', get_stylesheet_directory_uri() . "/assets/css/bobandsheri{$postfix}.css", array(), GREATERMEDIA_VERSION );
	wp_enqueue_style( 'bobandsheri_font', "https://fonts.googleapis.com/css?family=Work+Sans", array(), GREATERMEDIA_VERSION );
	wp_enqueue_script( 'livefyre', '//cdn.livefyre.com/Livefyre.js', null, null, true );
	wp_enqueue_script(
        'bobandsheri',
        get_stylesheet_directory_uri() . "/assets/js/bobandsheri{$postfix}.js",
        array( 'livefyre' ),
        WBT_VERSION,
        true
	);
	wp_enqueue_script(
	    'steel-media',
	    'https://secure.adnxs.com/seg?add=3581739&t=1',
	    array(),
	    null,
	    true
	);
	wp_enqueue_script( 'quantcast', get_stylesheet_directory_uri() . '/assets/js/vendor/quantcast.js', array(), true );
	wp_enqueue_script( 'cxense', get_stylesheet_directory_uri() . '/assets/js/vendor/cxense.js', array(), false );
}
add_action( 'wp_enqueue_scripts', 'bobandsheri_scripts_styles', 20 );

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