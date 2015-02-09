<?php
/*
 * Helpers to load capabilities for a post type into current roles.
 * To be invoked by plugins on activation/deactivation.
 *
 */

require_once __DIR__ . '/includes/GreaterMedia/Capabilities/CapabilitiesLoader.php';

function load_capabilities( $post_type ) {
	$loader = new \GreaterMedia\Capabilities\CapabilitiesLoader();
	$loader->load( $post_type );
}

function unload_capabilities( $post_type ) {
	$loader = new \GreaterMedia\Capabilities\CapabilitiesLoader();
	$loader->unload( $post_type );
}
