<?php
/**
 * Plugin Name: Greater Media Closures
 * Plugin URI:  http://10up.com
 * Description: Scholl & Buisness Closures
 * Version:     0.0.1
 * Author:      10up
 */


/**
 * Built using grunt-wp-plugin
 * Copyright (c) 2013 10up, LLC
 * https://github.com/10up/grunt-wp-plugin
 */

// Useful global constants
define( 'GMCLOSURES_VERSION', '0.0.1' );
define( 'GMCLOSURES_URL',     plugin_dir_url( __FILE__ ) );
define( 'GMCLOSURES_PATH',    dirname( __FILE__ ) . '/' );


require GMCLOSURES_PATH . 'includes/class-greatermedia-closures-cpt.php';
require GMCLOSURES_PATH . 'includes/class-greatermedia-closures-metaboxes.php';