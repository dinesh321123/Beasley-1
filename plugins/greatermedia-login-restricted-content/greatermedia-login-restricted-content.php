<?php

/*
Plugin Name: Greater Media Login-Restricted Content
Description: Login Restricted Content Plugin
Version: 1.0
Author: 10up
Author URI: http://10up.com
Text Domain: greatermedia-login-restricted-content
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

define( 'GREATER_MEDIA_LOGIN_RESTRICTED_CONTENT_PATH', dirname( __FILE__ ) );
define( 'GREATER_MEDIA_LOGIN_RESTRICTED_CONTENT_URL', plugins_url( trailingslashit( basename( dirname( __FILE__ ) ) ) ) );

// This is a requirement
if ( class_exists( 'VisualShortcode' ) ) {
	include trailingslashit( GREATER_MEDIA_LOGIN_RESTRICTED_CONTENT_PATH ) . 'inc/class-greatermedia-login-restricted-content.php';
}
