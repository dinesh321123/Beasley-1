<?php
/*
Plugin Name: BBGI - Public post preview
Description: Allow anonymous users to preview a post before it is published
Version: 0.0.1
Author: Rupesh Jorkar (RJ)
Author URI: https://bbgi.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

define( 'BBGI_PPP_VERSION', '0.0.1' );
define( 'BBGI_PPP_URL', plugin_dir_url( __FILE__ ) );
define( 'BBGI_PPP_PATH', dirname( __FILE__ ) );
define( 'BBGI_PPP_TEXT_DOMAIN', 'public-post-preview' );

include __DIR__ . '/includes/public-post-preview.php';

add_action( 'plugins_loaded', array( 'BBGI_Public_Post_Preview', 'init' ) );
register_uninstall_hook( __FILE__, array( 'BBGI_Public_Post_Preview', 'uninstall' ) );
