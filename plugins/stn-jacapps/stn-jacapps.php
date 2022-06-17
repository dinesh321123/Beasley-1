<?php
/**
 * Plugin Name: STN Player Jacapps
 * Description: Fix for Stn Video Player not showing in mobile view 
 * Version: 0.0.1
 * Author: Surjit Vala (SV)
 * Author URI: https://bbgi.com/
**/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

define( 'STN_VIDEO_JACAPPS_VERSION', '0.0.1' );
define( 'STN_VIDEO_JACAPPS_URL', plugin_dir_url( __FILE__ ) );

include __DIR__ . '/includes/show-stn-jacapps.php';