<?php
/*
Plugin Name: Vimeo Video Selector
Description: This plugin allows users to select videos from their Vimeo Video and embed them into WordPress via Shortcodes.
Version: 1.0
Author: Rupesh Jorkar (RJ)
Author URI: https://bbgi.com/
Text Domain: stnvideo
Domain Path: /assets/lang
*/
namespace VimeoVideoSelector;

// Check that the file is not accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you can not directly access this file.' );
}

// Setup Plugin Constants
define( 'VVPS_PLAYER_SELECTOR_BASENAME', plugin_basename(__FILE__) );
define( 'VVPS_PLAYER_SELECTOR_DIR', wp_normalize_path( dirname( __FILE__ ) ) );
define( 'VVPS_PLAYER_SELECTOR_URL', plugin_dir_url( __FILE__ ) );

// Load the Bootstrap
require_once( __DIR__ . '/app/Boot/bootstrap.php' );
