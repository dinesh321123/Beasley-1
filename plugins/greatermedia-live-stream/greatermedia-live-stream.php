<?php
/*
 * Plugin Name: Greater Media Live Stream
 * Description: Adds Live Stream functionality.
 * Author:      10up
 * Author URI:  http://10up.com/
 */

// global constants
define( 'GMEDIA_LIVE_STREAM_VERSION', '1.0.0' );
define( 'GMEDIA_LIVE_STREAM_URL',     plugin_dir_url( __FILE__ ) );
define( 'GMEDIA_LIVE_STREAM_PATH',    dirname( __FILE__ ) . '/' );

// post type constants
define( 'GMR_LIVE_STREAM_CPT', 'live-stream' );
define( 'GMR_SONG_CPT',        'songs' );

require_once 'includes/live-streams.php';
require_once 'includes/songs.php';
require_once 'includes/endpoint.php';
require_once 'includes/blogroll-widget.php';

register_activation_hook( __FILE__, 'flush_rewrite_rules' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );