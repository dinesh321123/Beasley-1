<?php
/**
 * Plugin Name: Greater Media Gigya
 * Plugin URI:  http://wordpress.org/extend/plugins
 * Description: Gigya plugin for Greater Media
 * Version:     0.1.0
 * Author:      Taylor Dewey
 * Author URI:  
 * License:     GPLv2+
 */

/** 
 * Copyright 2014  Taylor Dewey  (email : td@tddewey.com)
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

// Useful global constants
define( 'GMGIGYA_VERSION', '0.1.0' );
define( 'GMGIGYA_URL',     plugin_dir_url( __FILE__ ) );
define( 'GMGIGYA_PATH',    dirname( __FILE__ ) . '/' );

include __DIR__ . '/includes/class-gigya.php';
include __DIR__ . '/includes/class-comments.php';
include __DIR__ . '/includes/class-share.php';
include __DIR__ . '/includes/example-survey.php';


/**
 * Default initialization for the plugin:
 * - Registers the default textdomain.
 */
function gmgigya_init() {
	load_plugin_textdomain( 'gmgigya', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/lang' );
	GMI_Gigya::hooks();
	GMI_Gigya_Comments::hooks();
}

/**
 * Activate the plugin
 */
function gmgigya_activate() {
	// First load the init scripts in case any rewrite functionality is being loaded
	gmgigya_init();

	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'gmgigya_activate' );

/**
 * Deactivate the plugin
 * Uninstall routines should be in uninstall.php
 */
function gmgigya_deactivate() {

}
register_deactivation_hook( __FILE__, 'gmgigya_deactivate' );

// Wireup actions
add_action( 'init', 'gmgigya_init' );

// Wireup filters

// Wireup shortcodes
