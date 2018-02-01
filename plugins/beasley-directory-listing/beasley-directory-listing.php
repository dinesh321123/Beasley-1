<?php
/**
 * Plugin Name: Beasley Directory Listing
 * Description: The directory listing functionality.
 * Version: 1.0
 * Author: 10up
 * Author URI: http://10up.com/
 */

define( 'BEASLEY_LISTINGS_ABSURL', plugins_url( '/', __FILE__ ) );

require_once __DIR__ . '/includes/FeaturedImage.php';
require_once __DIR__ . '/includes/Listings.php';

register_activation_hook( __FILE__, 'flush_rewrite_rules' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

call_user_func( function() {
	$listings = new Beasley\DirectoryListing\Listings();
	$listings->register();
} );
