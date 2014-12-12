<?php
/**
 * Plugin Name: GreaterMedia Contests Categorization
 * Plugin URI:  http://wordpress.org/plugins
 * Description: Plugin to allow promotions manager to organize the type of contest: on-air, online, or an event etc.
 * Version:     0.1.0
 * Author:      10up
 */

/**
 * Built using grunt-wp-plugin
 * Copyright (c) 2013 10up, LLC
 * https://github.com/10up/grunt-wp-plugin
 */


if ( ! defined( 'WPINC' ) ) {
	die;
}

// Useful global constants
define( 'GMEDIA_CONTESTS_CATS_VERSION', '0.1.0' );
define( 'GMEDIA_CONTESTS_CATS_URL',     plugin_dir_url( __FILE__ ) );
define( 'GMEDIA_CONTESTS_CATS_PATH',    dirname( __FILE__ ) . '/' );

if( is_readable( GMEDIA_CONTESTS_CATS_PATH . 'includes/CustomTermMetaBox.php' ) ) {
	include_once GMEDIA_CONTESTS_CATS_PATH . 'includes/CustomTermMetaBox.php';
}

if( is_readable( GMEDIA_CONTESTS_CATS_PATH . 'includes/ContestsCategorizations.php' ) ) {
	include_once GMEDIA_CONTESTS_CATS_PATH . 'includes/ContestsCategorizations.php';
}