<?php
/**
 * Plugin Name: GreaterMedia Keywords
 * Plugin URI:  http://wordpress.org/plugins
 * Description: Gives listner option to keywords and get relevant content
 * Version:     0.0.1
 * Author:      10up
 * Author URI:  
 */


/**
 * Built using grunt-wp-plugin
 * Copyright (c) 2013 10up, LLC
 * https://github.com/10up/grunt-wp-plugin
 */

// Useful global constants
define( 'GMKEYWORDS_VERSION', '0.0.1' );
define( 'GMKEYWORDS_URL',     plugin_dir_url( __FILE__ ) );
define( 'GMKEYWORDS_PATH',    dirname( __FILE__ ) . '/' );
define( 'GMKEYWORDS_LIST_CACHE_TTL', 60 * 60 ); // One hour


require_once( GMKEYWORDS_PATH . 'includes/class-greatermedia-keyword-admin.php' );
require_once( GMKEYWORDS_PATH . 'includes/class-greatermedia-keyword-metabox.php' );
require_once( GMKEYWORDS_PATH . 'includes/class-greatermedia-keyword-autocomplete.php' );