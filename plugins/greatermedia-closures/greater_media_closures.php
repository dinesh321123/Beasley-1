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
define( 'GMCLOSURRES_VERSION', '0.0.1' );
define( 'GMCLOSURRES_URL',     plugin_dir_url( __FILE__ ) );
define( 'GMCLOSURRES_PATH',    dirname( __FILE__ ) . '/' );


require GMCLOSURRES_PATH . 'includes/class-greatermedia-closures-cpt.php';
require GMCLOSURRES_PATH . 'includes/class-greatermedia-closures-metaboxes.php';