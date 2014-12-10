<?php
/**
 * Plugin Name: Greater Media Content Syndication
 * Plugin URI:  http://wordpress.org/plugins
 * Description: Content Syndication to copy content from Corporate site to station sites
 * Version:     0.1.0
 * Author:      10up
 * Author URI:  http://10up.com
 * License:     GPLv2+
 * Text Domain: gmr_syndication
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
define( 'GMR_SYNDICATION_VERSION', '0.1.0' );
define( 'GMR_SYNDICATION_URL',     plugin_dir_url( __FILE__ ) );
define( 'GMR_SYNDICATION_PATH',    dirname( __FILE__ ) . '/' );

require_once GMR_SYNDICATION_PATH . 'includes/syndication-cpt.php';
require_once GMR_SYNDICATION_PATH . 'includes/blog-data.php';
require_once GMR_SYNDICATION_PATH . 'includes/cron-tasks.php';
require_once GMR_SYNDICATION_PATH . 'includes/content-kit.php';