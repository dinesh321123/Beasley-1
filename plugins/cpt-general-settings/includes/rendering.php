<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
class GeneralSettingsFrontRendering {

	public static function init() {
		// Register scripts
		// add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_scripts' ), 1 );
	}

	/**
	 * Registers Affiliate Marketing scripts to use on the front end.
	 *
	 * @static
	 * @access public
	 * @action wp_enqueue_scripts
	 */
	public static function register_scripts() {
		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
	}

	/**
	 * Gets an array of meta data for the Affiliate Marketing
	 * @param $post
	 * @return Array
	 */
	function get_post_metadata_from_post( $value, $post ) {
		$field = get_post_meta( $post->ID, $value, true );

		if ( ! empty( $field ) ) {
			return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
		} else {
			return false;
		}
	}


}

GeneralSettingsFrontRendering::init();
