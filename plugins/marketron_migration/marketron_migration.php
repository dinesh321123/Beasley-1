<?php
/**
 * Plugin Name: Marketron Migration
 * Plugin URI:  10up.com
 * Description: Migration script from Marketron XML to WordPress
 * Version:     0.0.1
 * Author:      10up.com
 */


/**
 * Built using grunt-wp-plugin
 * Copyright (c) 2013 10up, LLC
 * https://github.com/10up/grunt-wp-plugin
 */

// Useful global constants
define( 'GMEDIA_VERSION', '0.0.1' );
define( 'GMEDIA_URL',     plugin_dir_url( __FILE__ ) );
define( 'GMEDIA_PATH',    dirname( __FILE__ ) . '/' );

if ( defined( 'WP_CLI' ) and WP_CLI  ) {
	include_once GMEDIA_PATH . 'includes/class-gmediamigration.php';
	include_once GMEDIA_PATH . 'includes/class-CMM_Legacy_Redirects.php';
	include_once GMEDIA_PATH . 'includes/class-MTM_Migration_Utils.php';
}