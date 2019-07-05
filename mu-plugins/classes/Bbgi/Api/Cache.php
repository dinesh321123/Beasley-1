<?php

namespace Bbgi\Api;

class Cache extends \Bbgi\Module {

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		add_action( 'rest_api_init', [ $this, 'rest_api_init' ] );
	}

	/**
	 * Registers custom endpoint that will flush homepage cache
	 */
	public function rest_api_init() {
		register_rest_route( 'experience_engine/v1', 'clear_homepage_cache', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'handle_cache_flush' ),
			'permission_callback' => array( $this, 'check_permissions' ),
		) );
	}

	/**
	 * Checks for permissions on the current request
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return boolean
	 */
	public function check_permissions( \WP_REST_Request $request ) {
		$token = get_site_option( 'ee_hp_cache_token', false );

		if ( empty( $token ) ) {
			return false;
		}

		if ( $token === $request->get_header( 'Authorization' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Flushes the homepage cache in batcache
	 */
	public function handle_cache_flush() {
		if ( function_exists( 'batcache_clear_url' ) && class_exists( 'batcache' ) ) {
			$home = trailingslashit( get_option( 'home' ) );
			batcache_clear_url( $home );
			batcache_clear_url( $home . 'feed/' );
		}

		return rest_ensure_response( array( 'success' => true ) );
	}

}
