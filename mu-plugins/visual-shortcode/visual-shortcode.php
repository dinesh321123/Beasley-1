<?php

/*
Plugin Name: Visual Shortcode
Description: Parent classes and JavaScript for attractive shortcode rending using TinyMCE popups and views
Version: 1.0
Author: 10up
Author URI: http://10up.com
Text Domain: visual-shortcode
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

define( 'VISUAL_SHORTCODE_PATH', dirname( __FILE__ ) );
define( 'VISUAL_SHORTCODE_URL', plugin_dir_url( __FILE__ ) );

include trailingslashit( VISUAL_SHORTCODE_PATH ) . 'inc/class-visual-shortcode.php';
include trailingslashit( VISUAL_SHORTCODE_PATH ) . 'inc/dashicon-xref.php';
