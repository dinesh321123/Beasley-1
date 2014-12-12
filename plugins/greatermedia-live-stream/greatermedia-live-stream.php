<?php
/*
 * Plugin Name: Greater Media Live Stream
 * Description: Adds Live Stream functionality.
 * Author:      10up
 * Author URI:  http://10up.com/
 */

define( 'GMR_LIVE_STREAM_CPT', 'live-stream' );
define( 'GMR_SONG_CPT',        'songs' );

require_once 'includes/live-streams.php';
require_once 'includes/songs.php';
require_once 'includes/endpoint.php';

register_activation_hook( __FILE__, 'flush_rewrite_rules' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );