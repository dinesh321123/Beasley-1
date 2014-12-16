<?php
/*
Plugin Name: Greater Media Galleries
Description: Albums & Galleries
Version: 1.0
Author: 10up
Author URI: http://10up.com
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

define( 'GREATER_MEDIA_GALLERIES_URL', plugin_dir_url( __FILE__ ) );
define( 'GREATER_MEDIA_GALLERIES_PATH', dirname( __FILE__ ) );

include __DIR__ . '/includes/post-types.php';
include __DIR__ . '/includes/rendering.php';
