<?php
/*
Plugin Name: Greater Media Advertisers
Description:
Version: 1.0
Author: 10up
Author URI: http://10up.com/
*/

define( 'GREATER_MEDIA_ADVERTISERS_VERSION', '1.0.0' );
define( 'GREATER_MEDIA_ADVERTISERS_URL', plugin_dir_url( __FILE__ ) );
define( 'GREATER_MEDIA_ADVERTISERS_PATH', dirname( __FILE__ ) );

define( 'GMR_ADVERTISER_CPT', 'advertiser' );

include 'includes/post-types.php';

register_activation_hook( __FILE__, 'gmr_advertisers_activated' );
register_deactivation_hook( __FILE__, 'gmr_advertisers_deactivated' );

function gmr_advertisers_activated() {
	gmr_advertisers_register_post_type();

	load_capabilities( GMR_ADVERTISER_CPT );
	flush_rewrite_rules();
}

function gmr_advertisers_deactivated() {
	unload_capabilities( GMR_ADVERTISER_CPT );

	flush_rewrite_rules();
}
