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

if ( defined( 'WP_CLI' ) && WP_CLI  ) {
	require_once GMR_SYNDICATION_PATH . 'includes/syndication-cli.php';
}

register_activation_hook( __FILE__, 'gmr_content_syndication_activated' );
register_deactivation_hook( __FILE__, 'gmr_content_syndication_deactivated' );

function gmr_content_syndication_activated() {
	$content_kit = new \ContentKit();
	$content_kit->register_content_kit_cpt();

	$syndication = new \SyndicationCPT();
	$syndication->register_syndication_cpt();

	load_capabilities( 'content-kit' );
	load_capabilities( 'subscription' );

	flush_rewrite_rules();
}

function gmr_content_syndication_deactivated() {
	unload_capabilities( 'content-kit' );
	unload_capabilities( 'subscription' );

	flush_rewrite_rules();
}
