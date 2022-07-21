<?php
/*
Plugin Name: CPT Co-Author Settings
Description: Display coauthor in the front page
Version: 0.0.1
Author: Surjit Vala (SV)
Author URI: https://bbgi.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

define( 'COAUTHOR_SETTINGS_VERSION', '0.0.1' );
define( 'COAUTHOR_SETTINGS_URL', plugin_dir_url( __FILE__ ) );

include __DIR__ . '/includes/co-author-setting-metaboxe.php';

register_activation_hook( __FILE__, 'coauthor_settings_activated' );
register_deactivation_hook( __FILE__, 'coauthor_settings_deactivated' );

function coauthor_settings_activated() {
}

function coauthor_settings_deactivated() {
}
