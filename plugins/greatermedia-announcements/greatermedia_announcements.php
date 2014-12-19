<?php
/**
 * Plugin Name: GreaterMedia Announcements
 * Plugin URI:  http://wordpress.org/plugins
 * Description: This plugin proves option to create announcements to be visible on station dashboard page
 * Version:     0.0.1
 * Author:      10up
 * Author URI:  
 * License:     GPLv2+
 * Text Domain: gmannounce
 * Domain Path: /languages
 */

/**
 * Built using grunt-wp-plugin
 * Copyright (c) 2013 10up, LLC
 * https://github.com/10up/grunt-wp-plugin
 */

// Useful global constants
define( 'GMANNOUNCE_VERSION', '0.0.1' );
define( 'GMANNOUNCE_URL',     plugin_dir_url( __FILE__ ) );
define( 'GMANNOUNCE_PATH',    dirname( __FILE__ ) . '/' );

require_once( GMANNOUNCE_PATH . 'includes/class-announcements-cpt.php' );
require_once( GMANNOUNCE_PATH . 'includes/class-announcements-dashboard-widget.php' );