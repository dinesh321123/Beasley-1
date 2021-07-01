<?php
/*
Plugin Name: CST Tag permissions
Description: Custom tag permission on Tag metabox
Version: 0.0.1
Author: Rupesh Jorkar (RJ)
Author URI: https://bbgi.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

define( 'TAG_PERMISSIONS_VERSION', '0.0.1' );
define( 'TAG_PERMISSIONS_URL', plugin_dir_url( __FILE__ ) );
define( 'TAG_PERMISSIONS_PATH', dirname( __FILE__ ) );
define( 'TAG_PERMISSIONS_TEXT_DOMAIN', 'tag_permissions_textdomain' );

include __DIR__ . '/includes/tag-permissions-metaboxes.php';

register_activation_hook( __FILE__, 'tag_permissions_activated' );
register_deactivation_hook( __FILE__, 'tag_permissions_deactivated' );

function tag_permissions_activated() {
}

function tag_permissions_deactivated() {
}
