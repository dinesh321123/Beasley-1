<?php
/*
Plugin Name: Now Playing Jacapps
Plugin URI: https://bbgi.com/
Description: This plugin adds support for now playing in jacapps apps
Version: 1.0
Author: K Gilper
Author URI: https://bbgi.com/
License: GPL2
*/


if ( ! function_exists( 'ee_is_jacapps' ) ) :
	function ee_is_jacapps() {
		static $jacapps_pos = null;

		if ( $jacapps_pos === null ) {
			$jacapps_pos = stripos( $_SERVER['HTTP_USER_AGENT'], 'jacapps' );

			// Allow way to toggle jacapps through URL querystring
			if ( isset( $_GET['jacapps'] ) ) {
				$jacapps_pos = 1;
			}
		}

		return false !== $jacapps_pos;
	}
endif;

if ( ee_is_jacapps() ) {


	$nowplayingurl = plugins_url( '/nowplaying.js', __FILE__ );
	$dayjsurl = 'https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.10.6/dayjs.min.js';
	wp_enqueue_script( 'day-js', "{$dayjsurl}", [] , null, false );
	wp_enqueue_script( 'now-playing-jacapps', "{$nowplayingurl}", [] , null, true );
}





