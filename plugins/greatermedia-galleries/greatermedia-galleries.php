<?php
/*
Plugin Name: Greater Media Galleries
Description: Albums & Galleries
Version: 1.0.0
Author: 10up
Author URI: http://10up.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

define( 'GREATER_MEDIA_GALLERIES_VERSION', '1.0.1' );
define( 'GREATER_MEDIA_GALLERIES_URL', plugin_dir_url( __FILE__ ) );
define( 'GREATER_MEDIA_GALLERIES_PATH', dirname( __FILE__ ) );

include __DIR__ . '/includes/post-types.php';
include __DIR__ . '/includes/rendering.php';
include __DIR__ . '/includes/gallery-metaboxes.php';
include __DIR__ . '/includes/album-metaboxes.php';
include __DIR__ . '/includes/post-list.php';
include __DIR__ . '/includes/endpoints.php';

register_activation_hook( __FILE__, 'gmr_galleries_activated' );
register_deactivation_hook( __FILE__, 'gmr_galleries_deactivated' );

function gmr_galleries_activated() {
	\GreaterMediaGalleryCPT::gallery_cpt();
	\GreaterMediaGalleryCPT::album_cpt();

	load_capabilities( GreaterMediaGalleryCPT::GALLERY_POST_TYPE );
	load_capabilities( GreaterMediaGalleryCPT::ALBUM_POST_TYPE );
}

function gmr_galleries_deactivated() {
	unload_capabilities( GreaterMediaGalleryCPT::GALLERY_POST_TYPE );
	unload_capabilities( GreaterMediaGalleryCPT::ALBUM_POST_TYPE );
}
