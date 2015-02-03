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
