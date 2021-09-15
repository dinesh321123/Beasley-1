<?php
/*
Plugin Name: CPT - Listicle
Description: Custom Post Type - Architect listicle content type with pageviews for embeds
Version: 0.0.2
Author: Rupesh Jorkar (RJ)
Author URI: https://bbgi.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
define( 'LISTICLE_CPT_VERSION', '0.0.2' );
define( 'LISTICLE_CPT_URL', plugin_dir_url( __FILE__ ) );
define( 'LISTICLE_CPT_PATH', dirname( __FILE__ ) );
define( 'LISTICLE_CPT_TEXT_DOMAIN', 'listicle_textdomain' );

include __DIR__ . '/includes/post-types.php';
include __DIR__ . '/includes/listicle-metaboxes.php';
include __DIR__ . '/includes/rendering.php';

register_activation_hook( __FILE__, 'listicle_cpt_activated' );
register_deactivation_hook( __FILE__, 'listicle_cpt_deactivated' );

function listicle_cpt_activated() {
	\ListicleCPT::listicle_cpt();

	load_capabilities( ListicleCPT::LISTICLE_POST_TYPE );
	flush_rewrite_rules();	// This function is useful when used with custom post types as it allows for automatic flushing of the WordPress rewrite rules
}

function listicle_cpt_deactivated() {
	unload_capabilities( ListicleCPT::LISTICLE_POST_TYPE );
}
