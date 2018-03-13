<?php
/**
 * Plugin Name: Greater Media Podcasts
 * Description: A podcasting solution for Greater Media
 * Version:     1.0.0
 * Author:      10up Inc
 * Author URI:  http://10up.com/
 */

// Useful global constants
define( 'GMPODCASTS_VERSION', '0.1.1' );
define( 'GMPODCASTS_URL',     plugin_dir_url( __FILE__ ) );
define( 'GMPODCASTS_PATH',    dirname( __FILE__ ) . DIRECTORY_SEPARATOR );

/**
 * Add required files
 */
require_once( __DIR__ . '/includes/class-gmp-cpt.php' );
require_once( __DIR__ . '/includes/class-gmp-metabox.php' );
require_once( __DIR__ . '/includes/class-gmp-player.php' );
require_once( __DIR__ . '/includes/class-gmp-options.php' );
require_once( __DIR__ . '/includes/class-gmp-feed.php' );

register_activation_hook( __FILE__, 'gmr_podcasts_activated' );
register_deactivation_hook( __FILE__, 'gmr_podcasts_deactivated' );

function gmr_podcasts_activated() {
	\GMP_CPT::podcast_cpt();
	\GMP_CPT::episode_cpt();

	load_capabilities( \GMP_CPT::PODCAST_POST_TYPE );
	load_capabilities( \GMP_CPT::EPISODE_POST_TYPE );

	flush_rewrite_rules();
}

function gmr_podcasts_deactivated() {
	unload_capabilities( \GMP_CPT::PODCAST_POST_TYPE );
	unload_capabilities( \GMP_CPT::EPISODE_POST_TYPE );

	flush_rewrite_rules();
}
