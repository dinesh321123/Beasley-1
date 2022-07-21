<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
class GeneralSettingsFrontRendering {

	function __construct()
	{
		$this->init();
	}

	public static function init() {
		// Register scripts
		// add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_scripts' ), 1 );
		add_action('pre_get_posts', array( __CLASS__, 'author_pre_get_posts') );

		add_action( 'template_redirect', array( __CLASS__,'show_404_for_disabled_feeds' ) );
	}

	function show_404_for_disabled_feeds() {
		if ( is_feed() && is_singular() && in_array( get_post_type(), GeneralSettingsFrontRendering::restrict_feeds_posttype_list() ) ) {
			global $wp_query;

			$wp_query->set_404();	// Mark the current query as a 404
			status_header(404);	// Return 404 HTTP status code instead of the default 200
			header('Content-Type: text/html; charset=utf-8');	// By default, this page returns XML, so we change the Content-Type header // Because we want to show a 404 page
			get_template_part( 404 );	// Render the 404 template
			exit();	// You should exit from the script after that
		}
	}

	function restrict_feeds_posttype_list() {
		return (array) apply_filters( 'restrict-feeds-for-posttypes', array( 'post', 'affiliate_marketing', 'gmr_gallery', 'contest', 'tribe_events', 'listicle_cpt' ) );
	}
	function author_pre_get_posts($query) {
		if ( !is_admin() && $query->is_main_query() ) {
			if ($query->is_author()) {
				$query->set( 'posts_per_page', 16 );
				$query->set('post_type', array('post', 'gmr_gallery', 'listicle_cpt', 'affiliate_marketing'));
			}
		}
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

// GeneralSettingsFrontRendering::init();
new GeneralSettingsFrontRendering();
