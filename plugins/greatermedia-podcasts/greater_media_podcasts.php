<?php
/**
 * Plugin Name: Greater Media Podcasts
 * Plugin URI:  http://wordpress.org/plugins
 * Description: A podcasting solution for Greater Media
 * Version:     0.1.0
 * Author:      10up
 * Author URI:  http://10up.com
 */

/**
 * Built using grunt-wp-plugin
 * Copyright (c) 2013 10up, LLC
 * https://github.com/10up/grunt-wp-plugin
 */

// Useful global constants
define( 'GMPODCASTS_VERSION', '0.1.0' );
define( 'GMPODCASTS_URL',     plugin_dir_url( __FILE__ ) );
define( 'GMPODCASTS_PATH',    dirname( __FILE__ ) . '/' );

/**
 * Default initialization for the plugin:
 * - Registers the default textdomain.
 */
function gmpodcasts_init() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'gmpodcasts' );
	load_textdomain( 'gmpodcasts', WP_LANG_DIR . '/gmpodcasts/gmpodcasts-' . $locale . '.mo' );
	load_plugin_textdomain( 'gmpodcasts', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

/**
 * Add required files
 */
require_once( __DIR__ . '/includes/class-gmp-cpt.php' );
require_once( __DIR__ . '/includes/class-gmp-metabox.php' );
require_once( __DIR__ . '/includes/class-gmp-player.php' );
require_once( __DIR__ . '/includes/class-gmp-options.php' );

/**
 * Activate the plugin
 */
function gmpodcasts_activate() {
	// First load the init scripts in case any rewrite functionality is being loaded
	gmpodcasts_init();

	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'gmpodcasts_activate' );

/**
 * Deactivate the plugin
 * Uninstall routines should be in uninstall.php
 */
function gmpodcasts_deactivate() {

}
register_deactivation_hook( __FILE__, 'gmpodcasts_deactivate' );

// Wireup actions
add_action( 'init', 'gmpodcasts_init' );

// Wireup filters

// Wireup shortcodes
