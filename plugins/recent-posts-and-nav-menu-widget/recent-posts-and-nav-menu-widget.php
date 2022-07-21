<?php
/*
Plugin Name: Max Mega Menu - Add on widget
Description: Show recent post as per selected category and naw menu into widget
Version: 1.0.0
Author: Rupesh Jorkar (RJ)
Author URI: https://bbgi.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

define( 'RPMW_VERSION', '1.0.0' );
define( 'RPMW_URL', plugin_dir_url( __FILE__ ) );
define( 'RPMW_PATH', dirname( __FILE__ ) );
define( 'RPMW_TEXT_DOMAIN', 'rpmw_textdomain' );

include __DIR__ . '/includes/recent-posts-widget.php';
include __DIR__ . '/includes/nav-menu-widget.php';
