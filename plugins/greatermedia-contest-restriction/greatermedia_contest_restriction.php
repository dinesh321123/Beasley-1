<?php
/**
 * Plugin Name: GreaterMedia Contest Restriction
 * Plugin URI:  http://wordpress.org/plugins
 * Description: Adds options to restrict contest
 * Version:     0.0.1
 * Author:      10up
 * Author URI:  
 * License:     GPLv2+
 * Text Domain: gmedia_contest_restriction
 * Domain Path: /languages
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
define( 'GMEDIA_CONTEST_RESTRICTION_VERSION', '0.0.1' );
define( 'GMEDIA_CONTEST_RESTRICTION_URL',     plugin_dir_url( __FILE__ ) );
define( 'GMEDIA_CONTEST_RESTRICTION_PATH',    dirname( __FILE__ ) . '/' );

require_once GMEDIA_CONTEST_RESTRICTION_PATH . 'includes/ContestRestriction.php';
require_once GMEDIA_CONTEST_RESTRICTION_PATH . 'includes/RestrictionMetaboxes.php';