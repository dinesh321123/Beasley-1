<?php
/*
Plugin Name: CPT Segment Navigation
Description: Custom Segment navigation with display toggler and ordering type selection
Version: 0.0.1
Author: Surjit Vala (SV)
Author URI: https://bbgi.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

define( 'SEGMENT_NAVIGATION_VERSION', '0.0.1' );
define( 'SEGMENT_NAVIGATION_URL', plugin_dir_url( __FILE__ ) );

include __DIR__ . '/includes/segment-navigation-metaboxes.php';

register_activation_hook( __FILE__, 'segment_navigation_activated' );
register_deactivation_hook( __FILE__, 'segment_navigation_deactivated' );

function segment_navigation_activated() {
}

function segment_navigation_deactivated() {
}
