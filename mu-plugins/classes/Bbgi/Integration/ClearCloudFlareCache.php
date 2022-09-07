<?php
/**
 * Module responsible for Clear CloudFlare Cache.
 *
 * @package Bbgi
 */

namespace Bbgi\Integration;

class ClearCloudFlareCache extends \Bbgi\Module {


	/**
	 * Register actions and hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_filter( 'post_row_actions', [ $this, 'clear_cache_link' ], 10, 2 );
		add_filter( 'page_row_actions', [ $this, 'clear_cache_link' ], 10, 2 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'add_script' ),10,2 );
		add_action( 'wp_ajax_clear_cloudflare_cache', [ $this, 'clear_cloudflare_cache'],10,2 );
	}

	/**
	 * Get post types whiltelist
	 *
	 * @return array
	 */
	public function get_post_types_whitelist() {
		$blacklist = [ 'fp_feed', 'subscription' ];
		return array_diff( get_post_types(), $blacklist );
	}



	/**
	 * Check if the current user can clear cloudflare cache.
	 *
	 */
	public function can_send_notifications() {
		return current_user_can( 'manage_cache_button' );
	}

	/**
	 * Clear cache link
	 *
	 * @return array
	 */
	public function clear_cache_link( $actions, \WP_Post $post ) {


		if ( $this->can_send_notifications() &&
		    in_array( get_post_type( $post ), $this->get_post_types_whitelist(), true ) ) {

		}
		$actions['clear_cache'] = sprintf(
				'<a href="%s" class="clearCloudFlareCache" data-id="%s">%s</a>',
				'#',
				$post->ID,
			    esc_html__( 'Clear Cache', 'bbgi' )
		);
		return $actions;
	}



	/**
	 * Enqueues admin scripts and styles.
	 *
	 */

	function add_script() {
		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_script('clear-cloudflare-cache', plugins_url( 'assets/js/clear-cloudflare-cache'.$postfix.'.js', __FILE__ ), array('jquery','wp-util'), '0.1.0', false);
	}

	/**
	 * Clead cloudflare cache API
	 *
	 */

	public function clear_cloudflare_cache(){
		$postID = filter_input( INPUT_GET, 'postID', FILTER_SANITIZE_SPECIAL_CHARS );
		error_log("clearCloudFlareCache [postId => ". $postID."]");

		if(!$postID){
			wp_send_json_success(array( 'Message' => "Post ID not Found" ));
		}

		$cloudflaretoken = get_site_option( 'ee_cloudflare_token' );
		$zone_id = get_option('cloud_flare_zoneid');

		if ( empty($cloudflaretoken) || empty($zone_id) ) {
			error_log("Cloudflare not configured for this site" );
			wp_send_json_success(array( 'Message' => "Cloudflare not configured for this site" ));
		}

		$post = get_post( $postID );
		$post_slug = $post->post_type.'-'.$post->post_name;
		$posttype = $post->post_type;


		$categories = get_the_category( $postID );
		if (!$categories && isset($opts['category_list'])){
			$categories = $opts['category_list'];
		}

		$shows = get_the_terms( $postID, '_shows' );;
		if (!$shows && isset($opts['shows_list'])){
			$shows = $opts['shows_list'];
		}

		$cache_tags = [$post_slug];
		if ( !empty($categories)) {
			foreach ($categories as $category) {
				$cache_tags[] = 'archive-' . $category->slug;
			}
		}

		if (!empty($shows)) {
			foreach ($shows as $show) {
				$cache_tags[] = 'show-' . $show->slug;
			}
		}

		if (!empty($posttype)) {
			$cache_tags[] = 'archive-' . $posttype;
			if ($posttype == 'episode') {
				$cache_tags[] = 'podcast';
			}
		}




		// Clear specific page caches
		if ( function_exists( 'batcache_clear_url' ) && class_exists( 'batcache' ) ) {
			$url = get_permalink($postID);
			batcache_clear_url( $url );
		}

		error_log( 'Cloudflare Clearing Cache Tags'. $cache_tags);

		$data = [ "tags" => $cache_tags];
		$request_url = 'https://api.cloudflare.com/client/v4/zones/'.$zone_id.'/purge_cache';
		$response = wp_remote_post( $request_url, array(
				'method' => 'POST',
				'headers' => array(
					'Content-Type' => 'application/json',
					'Authorization' => 'Bearer ' . $cloudflaretoken,
				),
				'body' => wp_json_encode( $data )
			)
		);

       print_r(json_decode($response));

		if ( is_wp_error( $response ) ) {
			error_log('Cloudflare Response'.$response->get_error_message());
			wp_send_json_success(array( "Response" => $response->get_error_message()));
		} else {
			error_log('Cloudflare Response'.json_encode( $response ));
			wp_send_json_success(array( "Response" => json_decode($response)));
		}

	}


}
