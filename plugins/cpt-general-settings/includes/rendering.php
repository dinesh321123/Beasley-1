<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
class GeneralSettingsFrontRendering {

	public static function init() {
		// Register scripts
		// add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_scripts' ), 1 );
		add_action('pre_get_posts', array( __CLASS__, 'author_pre_get_posts') );
		add_filter('query_vars', array( __CLASS__, 'add_query_vars'));
	}
	function author_pre_get_posts($query) {
		if ( !is_admin() && $query->is_main_query() ) {
			if ($query->is_author()) {
				$query->set( 'posts_per_page', 16 );
				$query->set('post_type', array('post', 'gmr_gallery', 'listicle_cpt', 'affiliate_marketing'));
			}
		}
	}
	function add_query_vars( $author_query_vars ) {
		$author_query_vars[] = 'author_id';
		return $author_query_vars;
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
