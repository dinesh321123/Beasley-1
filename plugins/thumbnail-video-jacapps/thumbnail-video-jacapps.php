<?php
/**
 * Plugin Name: Video Thumbnail Jacapps
 * Description: Fix for video thumbnail not showing in mobile view 
 * Version: 0.0.1
 * Author: Surjit Vala (SV)
 * Author URI: https://bbgi.com/
**/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

define( 'TUMBNAIL_VIDEO_JACAPPS_VERSION', '0.0.1' );
define( 'TUMBNAIL_VIDEO_JACAPPS_URL', plugin_dir_url( __FILE__ ) );

include __DIR__ . '/includes/show-thumbnail-jacapps.php';