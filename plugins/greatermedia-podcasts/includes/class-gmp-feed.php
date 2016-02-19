<?php
/**
 * Created by Eduard
 * Date: 13.01.2015 17:28
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class GMPFeed {

	public static function init() {
		if( self::is_podcast_feed() ) {

			// Prevent feed from returning a 404 error when no posts are present on site
			add_action( 'template_redirect' , array( __CLASS__ , 'gmp_prevent_404' ) , 10 );
			add_action( 'template_redirect' , array( __CLASS__ , 'gmp_feed_template' ) , 11 );
		}
	}

	public static function is_podcast_feed() {
		if( isset( $_GET['feed'] ) && $_GET['feed'] == 'podcast' ) {
			return true;
		}
		return false;
	}

	public static function gmp_prevent_404() {
		global $wp_query;

		if( self::is_podcast_feed() ) {
			status_header( 200 );
			$wp_query->is_404 = false;
		}
	}


	public static function gmp_feed_template() {
		$file_name = 'feed-podcast.php';

		$theme_template_file = trailingslashit( get_template_directory() ) . $file_name;

		// Load feed template from theme if it exists, otherwise use plugin template

		if( file_exists( $theme_template_file ) ) {
			require( $theme_template_file );
		}

		exit;
	}


	public static function get_file_size( $file = false ) {

		if( $file ) {

			$data = wp_remote_head( $file );

			if ( ! is_wp_error( $data ) && isset( $data['headers']['content-length'] ) ) {

				$raw = $data['headers']['content-length'];
				$formatted = self::format_bytes( $raw );

				$size = array(
					'raw' => $raw,
					'formatted' => $formatted
				);

				return $size;

			}

		}

		return false;
	}

	public static function format_bytes( $size , $precision = 2 ) {

		if( $size ) {

		    $base = log ( $size ) / log( 1024 );
		    $suffixes = array( '' , 'k' , 'M' , 'G' , 'T' );
		    $bytes = round( pow( 1024 , $base - floor( $base ) ) , $precision ) . $suffixes[ floor( $base ) ];

		    return $bytes;
		}

		return false;
	}

	public static function get_attachment_mimetype( $attachment = false ) {

		if( $attachment ) {
			global $wpdb;

			$prefix = $wpdb->prefix;

			$attachment = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT ID FROM {$prefix}posts WHERE guid=%s",
					$attachment
				)
			);

			if( $attachment[0] ) {
				$id = $attachment[0];

				$mime_type = get_post_mime_type( $id );

				return $mime_type;
			}

		}

		return false;

	}

}

GMPFeed::init();