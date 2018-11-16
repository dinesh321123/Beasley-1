<?php
/**
 * Plugin Name: GreaterMedia Shows
 * Plugin URI:  http://wordpress.org/plugins/
 * Description: GreaterMedia Shows Plugin
 * Version:     1.0.0
 * Author:      10up Inc
 * Author URI:  http://10up.com/
 */

// Useful global constants
define( 'GMEDIA_SHOWS_VERSION', '1.0.0.3' );
define( 'GMEDIA_SHOWS_URL',     plugin_dir_url( __FILE__ ) );
define( 'GMEDIA_SHOWS_PATH',    dirname( __FILE__ ) . DIRECTORY_SEPARATOR );

include_once GMEDIA_SHOWS_PATH . 'includes/class-ShowsCPT.php';
include_once GMEDIA_SHOWS_PATH . 'includes/class-Metaboxes.php';
require_once GMEDIA_SHOWS_PATH . 'includes/gmi-show-support.php';
require_once GMEDIA_SHOWS_PATH . 'includes/gmi-show-live-links.php';
require_once GMEDIA_SHOWS_PATH . 'includes/gmi-show-endpoints.php';
require_once GMEDIA_SHOWS_PATH . 'includes/gmi-show-personalities.php';

register_activation_hook( __FILE__, 'gmr_shows_activated' );
register_deactivation_hook( __FILE__, 'gmr_shows_deactivated' );

function gmr_shows_activated() {
	$shows_cpt = new \ShowsCPT();
	$shows_cpt->register_post_type();

	load_capabilities( ShowsCPT::SHOW_CPT );
	flush_rewrite_rules();
}

function gmr_shows_deactivated() {
	unload_capabilities( ShowsCPT::SHOW_CPT );
	flush_rewrite_rules();
}
