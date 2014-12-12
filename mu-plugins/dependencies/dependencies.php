<?php
/**
 * Plugin Name: Greater Media Dependencies
 * Plugin URI:  http://10up.com
 * Description: Register all JS and CSS dependencies
 * Version:     0.0.1
 * Author:      10up
 */

if ( ! defined( 'WPINC' ) ) {
	die();
}

// Useful global constants
define( 'GMRDEPENDENCIES_VERSION', '0.0.1' );
define( 'GMRDEPENDENCIES_URL',     plugin_dir_url( __FILE__ ) );
define( 'GMRDEPENDENCIES_PATH',    dirname( __FILE__ ) . '/' );

if( is_readable( GMRDEPENDENCIES_PATH . 'includes/gmr-dependencies.php' ) ) {
	include_once GMRDEPENDENCIES_PATH . 'includes/gmr-dependencies.php';
}