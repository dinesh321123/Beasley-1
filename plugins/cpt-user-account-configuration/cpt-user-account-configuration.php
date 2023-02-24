<?php
/*
Plugin Name: BBGI - User Account Pages and Configuration
Plugin URI: https://bbgi.com/
Description: Manage user account related pages and configuration: My Account etc.
Version: 0.0.1
Author: Rupesh Jorkar (RJ)
Author URI: https://bbgi.com/
Text Domain: user-account-configuration
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
define( 'USER_ACCOUNT_CONFIGURATION_VERSION', '0.0.1' );
define( 'USER_ACCOUNT_CONFIGURATION_URL', plugin_dir_url( __FILE__ ) );
define( 'USER_ACCOUNT_CONFIGURATION_PATH', dirname( __FILE__ ) );
define( 'USER_ACCOUNT_CONFIGURATION_TEXT_DOMAIN', 'general_settings_textdomain' );


include __DIR__ . '/includes/my-account.php';
