<?php
/*
Plugin Name: CPT - General admin settings
Description: Settings to on/off Draft Kings iFrame, etc.
Version: 0.0.1
Author: Rupesh Jorkar (RJ)
Author URI: https://bbgi.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
define( 'GENERAL_SETTINGS_CPT_VERSION', '0.0.1' );
define( 'GENERAL_SETTINGS_CPT_URL', plugin_dir_url( __FILE__ ) );
define( 'GENERAL_SETTINGS_CPT_PATH', dirname( __FILE__ ) );
define( 'GENERAL_SETTINGS_CPT_TEXT_DOMAIN', 'general_settings_textdomain' );

$iframe_height =  get_option( 'configurable_iframe_height', '0' );

if ( ! empty( $iframe_height ) ) :
	include __DIR__ . '/includes/draftking-iframe-settings.php';
endif;