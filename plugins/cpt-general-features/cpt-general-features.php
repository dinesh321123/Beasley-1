<?php
/*
Plugin Name: CPT - General feature and functionality
Description: Manage general BBGI feature and functionality: Clone listicle
Version: 0.1
Author: Rupesh Jorkar (RJ)
Author URI: https://bbgi.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
define( 'GFF_VERSION', '0.0.1' );
define( 'GFF_URL', plugin_dir_url( __FILE__ ) );
define( 'GFF_PATH', dirname( __FILE__ ) );
define( 'GFF_TEXT_DOMAIN', 'gff_textdomain' );

/* Clone Listicle post type similarly to Posts */
include __DIR__ . '/includes/duplicate-listicle.php';

// This line includes the cache manager file for caching functionality
// include __DIR__ . '/includes/cache-manager.php';
