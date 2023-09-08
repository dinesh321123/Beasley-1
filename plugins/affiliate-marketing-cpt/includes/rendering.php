<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class AffiliateMarketingCPTFrontRendering {

	public static $strip_shortcodes = false;

	public static function init() {
		
	}

     

	/**
	 * Gets an array of meta data for the Affiliate Marketing
	 * @param $post
	 * @return Array
	 */
	public static function get_post_metadata_from_post( $value, $post ) {
		$field = get_post_meta( $post->ID, $value, true );
		
		if ( ! empty( $field ) ) {
            return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
        } else {
            return false;
        }
	}
	
	 public static function get_attachment_ids_for_post( $post ) {
		static $ids = array();

		$post = get_post( $post );
		if ( ! isset( $ids[ $post->ID ] ) ) {
			$array_ids = get_post_meta( $post->ID, 'gallery-image' );
			$array_ids = array_filter( array_map( 'intval', $array_ids ) );

			if ( empty( $array_ids ) && preg_match_all( '/\[gallery.*ids=.(.*).\]/', $post->post_content, $ids ) ) {
				$array_ids = array();
				foreach( $ids[1] as $match ) {
					$array_id = explode( ',', $match );
					$array_id = array_filter( array_map( 'intval', $array_id ) );

					$array_ids = array_merge( $array_ids, $array_id );
				}
			}

			$ids[ $post->ID ] = is_array( $array_ids )
				? array_values( array_filter( array_map( 'intval', $array_ids ) ) )
				: array();
		}

		return ! empty( $ids[ $post->ID ] )
			? $ids[ $post->ID ]
			: null;
	}
}

AffiliateMarketingCPTFrontRendering::init();
