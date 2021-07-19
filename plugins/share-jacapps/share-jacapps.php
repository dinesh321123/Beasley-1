<?php
/**
 * Plugin Name: Social Share Jacapps
 * Description: Add Social Share For Mobile View
 * Version: 0.0.1
 * Author: Surjit Vala (SV)
 * Author URI: https://bbgi.com/
**/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

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

if( ee_is_jacapps() ) {
    add_action( 'wp_enqueue_scripts', 'my_custom_script_load' );
    function my_custom_script_load(){
        wp_register_style( 'share-jacapps', plugin_dir_url( __FILE__ ) . '/share.css');
        wp_enqueue_style('share-jacapps');
        wp_enqueue_script( 'share-jacapps', plugin_dir_url( __FILE__ ) . '/share.js', array( 'jquery' ) );
    }
}