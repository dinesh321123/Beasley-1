<?php
/*
Plugin Name: STN Video Player Selector
Plugin URI: https://bitbucket.org/sendtonews/wp-player-selector
Description: This plugin allows users to select Smart Match players or videos from their STN Video catalogue and embed them into WordPress via Blocks, Widgets, Shortcodes and oEmbed.
Version: 1.0.1.2
Author: STN Video
Author URI: https://www.stnvideo.com
Text Domain: stnvideo
Domain Path: /assets/lang
*/

namespace SendtoNews;

// Check that the file is not accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you can not directly access this file.' );
}

// Setup Plugin Constants
define( 'S2N_PLAYER_SELECTOR_BASENAME', plugin_basename(__FILE__) );
define( 'S2N_PLAYER_SELECTOR_DIR', wp_normalize_path( dirname( __FILE__ ) ) );
define( 'S2N_PLAYER_SELECTOR_URL', plugin_dir_url( __FILE__ ) );
define( 'S2N_API_URL', 'https://api.sendtonews.com/api/v1/' );
define( 'S2N_GUIDE', S2N_PLAYER_SELECTOR_URL . 'assets/pdf/guide.pdf' );

// Load the Bootstrap
require_once(__DIR__ . '/app/Boot/bootstrap.php');
