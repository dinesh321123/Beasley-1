<?php
/**
 * Plugin Name: GreaterMedia Surveys
 * Plugin URI:  http://wordpress.org/plugins
 * Description: Gives option to create surveys
 * Version:     0.0.1
 * Author:      10up
 * Author URI:
 * License:     GPLv2+
 * Text Domain: gmsurveys
 * Domain Path: /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

// Useful global constants
define( 'GMSURVEYS_VERSION', '0.0.1' );
define( 'GMSURVEYS_URL',     plugin_dir_url( __FILE__ ) );
define( 'GMSURVEYS_PATH',    dirname( __FILE__ ) . '/' );

require_once GMSURVEYS_PATH . 'includes/class-greatermedia-surveys.php';