<?php
/**
 * Plugin Name: Greater Media Live Player
 * Plugin URI:  http://wordpress.org/plugins
 * Description: A persistent site-wide player for live audio and podcast for Greater Media
 * Version:     1.1.3
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
define( 'GMLIVEPLAYER_URL',  plugins_url( '/', __FILE__ ) );
define( 'GMLIVEPLAYER_VERSION', '20181112.1' );
define( 'GMLIVEPLAYER_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );

/**
 * Required Files
 */
require_once( __DIR__ . '/includes/class-gmlp-player.php' );
require_once( __DIR__ . '/includes/audio-shortcodes.php' );

register_activation_hook( __FILE__, array( 'GMLP_Player', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'GMLP_Player', 'deactivate' ) );

add_action( 'after_setup_theme', function() {
	if ( current_theme_supports( 'legacy-live-player' ) ) {
		GMLP_Player::init();
		GMR_Audio_Shortcodes::init();
	}
} );
