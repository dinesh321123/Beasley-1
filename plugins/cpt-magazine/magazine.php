<?php
/*
Plugin Name: CPT - Magazine
Description: Custom Post Type - Featured posts for categories
Version: 0.0.1
Author: Surjit Vala (SV)
Author URI: https://bbgi.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
define( 'MAGAZINE_CPT_VERSION', '0.0.1' );
define( 'MAGAZINE_CPT_URL', plugin_dir_url( __FILE__ ) );
define( 'MAGAZINE_CPT_PATH', dirname( __FILE__ ) );
define( 'MAGAZINE_CPT_TEXT_DOMAIN', 'magazine_textdomain' );

include __DIR__ . '/includes/post-types.php';
include __DIR__ . '/includes/rendering.php';

register_activation_hook( __FILE__, 'magazine_cpt_activated' );
register_deactivation_hook( __FILE__, 'magazine_cpt_deactivated' );

function magazine_cpt_activated() {
	\MagazineCPT::magazine_cpt();

	load_capabilities( MagazineCPT::MAGAZINE_POST_TYPE );
	flush_rewrite_rules();	// This function is useful when used with custom post types as it allows for automatic flushing of the WordPress rewrite rules
}

function magazine_cpt_deactivated() {
	unload_capabilities( MagazineCPT::MAGAZINE_POST_TYPE );
}
