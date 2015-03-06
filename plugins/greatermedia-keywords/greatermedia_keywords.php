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
define( 'GMKEYWORDS_PATH',    dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
define( 'GMKEYWORDS_LIST_CACHE_TTL', HOUR_IN_SECONDS );


require_once( GMKEYWORDS_PATH . 'includes/class-greatermedia-keyword-admin.php' );
require_once( GMKEYWORDS_PATH . 'includes/class-greatermedia-keyword-metabox.php' );
require_once( GMKEYWORDS_PATH . 'includes/class-greatermedia-keyword-autocomplete.php' );

register_activation_hook( __FILE__, 'gmr_keywords_activated' );
register_deactivation_hook( __FILE__, 'gmr_keywords_deactivated' );

function gmr_keywords_activated() {
	$roles = array( 'editor', 'author', 'administrator' );
	foreach ( $roles as $role ) {
		$role = get_role( $role );
		$role->add_cap( 'manage_keywords', true );
	}
}

function gmr_keywords_deactivated() {
	$roles = array( 'editor', 'author', 'administrator' );
	foreach ( $roles as $role ) {
		$role = get_role( $role );
		$role->remove_cap( 'manage_keywords', true );
	}
}