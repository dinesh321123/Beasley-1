<?php
/*
 * Plugin Name: Greater Media Capabilities
 * Description: Adds and Removes Capabilities to WordPress for the GMR Post Types
 * Author:      10up
 * Author URI:  http://10up.com/
 */

define( 'GMR_CAPABILITIES_URL', plugin_dir_url( __FILE__ ) );
define( 'GMR_CAPABILITIES_PATH', dirname( __FILE__ ) . '/' );
define( 'GMR_CAPABILITIES_PLUGIN_FILE', __FILE__ );
define( 'GMR_CAPABILITIES_VERSION', '0.1.0' );

require_once __DIR__ . '/includes/GreaterMedia/Capabilities/Plugin.php';

register_activation_hook( __FILE__, 'gmr_capabilities_activate' );
register_deactivation_hook( __FILE__, 'gmr_capabilities_deactivate' );

function gmr_capabilities_activate() {
	gmr_capabilities_plugin()->activate();
}

function gmr_capabilities_deactivate() {
	gmr_capabilities_plugin()->deactivate();
}

function gmr_capabilities_plugin() {
	return \GreaterMedia\Capabilities\Plugin::get_instance();
}

gmr_capabilities_plugin()->enable();

