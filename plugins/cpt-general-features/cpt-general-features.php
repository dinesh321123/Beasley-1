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
include __DIR__ . '/includes/editor-toolbar-widgets.php';

/* This file is responsible for managing and defining Outbrain widgets within the WordPress context. */
include __DIR__ . '/includes/outbrain-widgets.php';