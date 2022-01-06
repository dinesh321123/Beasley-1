<?php
/*
Plugin Name: CPT - Tag and Category import/export on Network level
Description: Network admin can able to import/export the tag/category from network level
Version: 0.0.1
Author: Rupesh Jorkar (RJ)
Author URI: https://bbgi.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
define( 'TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_VERSION', '0.0.1' );
// define( 'GENERAL_SETTINGS_CPT_URL', plugin_dir_url( __FILE__ ) );
define( 'TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_URL', plugin_dir_url( __FILE__ ) );
// define( 'GENERAL_SETTINGS_CPT_PATH', dirname( __FILE__ ) );
define( 'TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_PATH', dirname( __FILE__ ) );
// define( 'GENERAL_SETTINGS_CPT_TEXT_DOMAIN', 'general_settings_textdomain' );
define( 'TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_TEXT_DOMAIN', 'general_settings_textdomain' );
// define( 'GENERAL_SETTINGS_CPT_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'TAG_CATEGORY_IMPORT_EXPORT_BY_NETWORK_DIR_PATH', plugin_dir_path( __FILE__ ) );

include __DIR__ . '/includes/ietc.php';	//Import export tag category network level

register_activation_hook( __FILE__, 'cpt_general_settings_activated' );
register_deactivation_hook( __FILE__, 'cpt_general_settings_deactivated' );

function cpt_general_settings_activated() {
	\ImportExportTagCategory::ietc_activation();
}

function cpt_general_settings_deactivated() {
}
?>
