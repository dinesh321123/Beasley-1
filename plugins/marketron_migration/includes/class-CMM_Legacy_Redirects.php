<?php
/**
 * Handles legacy redirects.
 *
 * Uses a custom post type because it is much quicker than storing all of this data in post meta.
 * Uses post_name, since it's indexed and will be really quick to query.
 *
 * Too add a legacy URL to the database, you need two things:
 *      1. The OLD URL
 *      2. The NEW Post ID
 * Then, just call CMM_Legacy_Redirects::add_redirect( $old_url, $post_id );
 *
 * The rest should be magic.
 */

class CMM_Legacy_Redirects {

	public function __construct() {
		// I don't do anything
	}

	/**
	 * Sets up actions and filters.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'template_redirect', array( __CLASS__, 'maybe_redirect' ), 1 );
	}

	/**
	 * Register the post type for redirects.
	 */
	public static function register_post_type() {
		$args = array(
			'public' => false,
		);
		register_post_type( 'cmm-redirect', $args );
	}

	/**
	 * If request is 404, checks to see if we have a legacy redirect entry for the url being accessed.
	 */
	public static function maybe_redirect() {
		if ( ! is_404() ) {
			return;
		}

		$path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );

		if ( false !== $path ) {
			$new_permalink = self::get_redirect_url( $path );
			if ( false !== $new_permalink ) {
				wp_safe_redirect( $new_permalink, 301 );
				exit;
			}
		}
	}

	/**
	 * Gets the permalink to the new post if a redirect matches.
	 *
	 * @param string $path The path to the page that was requested.
	 *
	 * @return bool|string URL to the post if found, or false otherwise
	 */
	public static function get_redirect_url( $path ) {
		global $wpdb;

		// Using $wpdb because we really don't need all the extra stuff that WP_Query or get_posts would return.
		$parent_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_parent FROM $wpdb->posts WHERE post_name = %s AND post_type = %s", md5( $path ), 'cmm-redirect' ) );
		if ( is_null( $parent_id ) ) {
			return false;
		}

		return get_permalink( $parent_id );
	}

	/**
	 * Adds a redirect to the database.
	 *
	 * @param string $old_url The old url to redirect.
	 * @param integer $post_id The post ID to redirect to.
	 *
	 * @return integer The post id for the redirect
	 */
	public static function add_redirect( $old_url, $post_id ) {
		$path = parse_url( $old_url, PHP_URL_PATH );

		$query = parse_url( $old_url, PHP_URL_QUERY );

		if( !is_null( $query) ) {
			$path = $path . '?' . $query;
		}

		$redirect_id = wp_insert_post( array(
			'post_parent' => $post_id,
			'post_name' => md5( $path ), // Max characters + sanitize_title makes this a decent idea
			'post_title' => $path,
			'post_type' => 'cmm-redirect',
		) );

		return $redirect_id;
	}
}

// Ready, Set, Go!
CMM_Legacy_Redirects::init();