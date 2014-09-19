<?php
/**
 * Plugin Name: Greater Media Live Player
 * Plugin URI:  http://wordpress.org/plugins
 * Description: A persistent site-wide player for live audio and podcast for Greater Media
 * Version:     0.1.0
 * Author:      10up
 * Author URI:  http://10up.com
 * License:     GPLv2+
 * Text Domain: gmliveplayer
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2014 10up (email : allen@10up.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Built using grunt-wp-plugin
 * Copyright (c) 2013 10up, LLC
 * https://github.com/10up/grunt-wp-plugin
 */

// Useful global constants
define( 'GMLIVEPLAYER_VERSION', '0.1.0' );
define( 'GMLIVEPLAYER_URL',     plugin_dir_url( __FILE__ ) );
define( 'GMLIVEPLAYER_PATH',    dirname( __FILE__ ) . '/' );

/**
 * Default initialization for the plugin:
 * - Registers the default textdomain.
 */
function gmliveplayer_init() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'gmliveplayer' );
	load_textdomain( 'gmliveplayer', WP_LANG_DIR . '/gmliveplayer/gmliveplayer-' . $locale . '.mo' );
	load_plugin_textdomain( 'gmliveplayer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

/**
 * Required Files
 */
require_once( __DIR__ . '/includes/class-add-menu.php' );
require_once( __DIR__ . '/includes/class-gmlp-options.php' );
require_once( __DIR__ . '/includes/class-gmlp-player.php' );

/**
 * Activate the plugin
 */
function gmliveplayer_activate() {
	// First load the init scripts in case any rewrite functionality is being loaded
	gmliveplayer_init();

	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'gmliveplayer_activate' );

/**
 * Deactivate the plugin
 * Uninstall routines should be in uninstall.php
 */
function gmliveplayer_deactivate() {

}
register_deactivation_hook( __FILE__, 'gmliveplayer_deactivate' );

// Wireup actions
add_action( 'init', 'gmliveplayer_init' );

// Wireup filters

// Wireup shortcodes
