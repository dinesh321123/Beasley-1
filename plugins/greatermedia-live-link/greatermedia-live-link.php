<?php
/*
 * Plugin Name: Greater Media Live Link
 * Description: Adds Live Link functionality.
 * Author:      10up
 * Author URI:  http://10up.com/
 */

define( 'GMEDIA_LIVE_LINK_VERSION', '1.0.0' );
define( 'GMEDIA_LIVE_LINK_URL',     plugin_dir_url( __FILE__ ) );
define( 'GMEDIA_LIVE_LINK_PATH',    dirname( __FILE__ ) . '/' );

define( 'GMR_LIVE_LINK_CPT', 'gmr-live-link' );

require_once 'includes/live-link.php';
require_once 'includes/quickpost.php';

register_activation_hook( __FILE__, 'gmr_live_link_activate' );
register_deactivation_hook( __FILE__, 'gmr_live_link_deactivate' );

function gmr_live_link_activate() {
	gmr_ll_register_post_type();
	load_capabilities( GMR_LIVE_LINK_CPT );
}

function gmr_live_link_deactivate() {
	unload_capabilities( GMR_LIVE_LINK_CPT );
}
