<?php
/*
Plugin Name: Affiliate Marketing Selection
Plugin URI:
Description: Import Existing must have/affiliate marketing posts in the post as short code
Version: 0.0.1
Author: Surjit Vala (SV)
Author URI: https://bbgi.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}


define( 'AFFILIATE_MARKETING_SELECTION_VERSION', '1.0.0' );
define( 'AFFILIATE_MARKETING_SELECTION_URL', plugin_dir_url( __FILE__ ) );

include __DIR__ . '/includes/select-affiliate-marketing.php';

register_activation_hook( __FILE__, 'select_affiliate_marketing_activated' );
register_deactivation_hook( __FILE__, 'select_affiliate_marketing_deactivated' );

function select_affiliate_marketing_activated() {
}

function select_affiliate_marketing_deactivated() {
}
