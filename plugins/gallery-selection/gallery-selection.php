<?php
/*
Plugin Name: Gallery Selection
Plugin URI:
Description: Import Existing galleries in the post as short code
Version: 0.0.1
Author: Surjit Vala (SV)
Author URI: https://bbgi.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}


define( 'GALLERY_SELECTION_VERSION', '1.0.0' );
define( 'GALLERY_SELECTION_URL', plugin_dir_url( __FILE__ ) );

include __DIR__ . '/includes/select-gallery.php';

register_activation_hook( __FILE__, 'select_gallery_activated' );
register_deactivation_hook( __FILE__, 'select_gallery_deactivated' );

function select_gallery_activated() {
}

function select_gallery_deactivated() {
}
