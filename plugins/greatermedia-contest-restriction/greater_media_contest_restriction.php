<?php
/**
 * Plugin Name: Greater Media Contest Restriction
 * Plugin URI:  http://wordpress.org/plugins
 * Description: Plugin will give option to promotions manager to restirct contests number of entries per person, per time duration, and/or by age.
 * Version:     0.1.0
 * Author:      10up
 * Author URI:  http://10up.com
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
define( 'GMEDIA_CONTEST_RESTRICTION_VERSION', '0.1.0' );
define( 'GMEDIA_CONTEST_RESTRICTION_URL',     plugin_dir_url( __FILE__ ) );
define( 'GMEDIA_CONTEST_RESTRICTION_PATH',    dirname( __FILE__ ) . '/' );


if( is_readable( GMEDIA_CONTEST_RESTRICTION_PATH . 'includes/ContestRestriction.php' ) ) {
	include_once GMEDIA_CONTEST_RESTRICTION_PATH . 'includes/ContestRestriction.php';
}

if( is_readable( GMEDIA_CONTEST_RESTRICTION_PATH . 'includes/RestrictionMetaboxes.php' ) ) {
	include_once GMEDIA_CONTEST_RESTRICTION_PATH . 'includes/RestrictionMetaboxes.php';
}