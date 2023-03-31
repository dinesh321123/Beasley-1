<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class MagazineCPTFrontRendering {

	public static function init() {
	}

	/**
	 * Gets an array of meta data for the Magazine
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

MagazineCPTFrontRendering::init();
