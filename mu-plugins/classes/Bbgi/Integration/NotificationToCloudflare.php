<?php
/**
 * Module responsible for send a notification to Cloudflare to clear cache for that page.
 *
 * @package Bbgi
 */

namespace Bbgi\Integration;

const posttype = array('');

class NotificationToCloudflare extends \Bbgi\Module
{
	public function register()
	{
		add_action( 'bbgi_register_settings', $this( 'register_settings' ), 10, 2 );
		add_action('admin_notices', $this( 'show_error_notice' ) );
		// add_filter( 'notification-to-cloudflare-post-types', array( __CLASS__, 'extend_curration_post_types' ) );

		error_log( 'Cloudflare send notification register' );
		
		/* foreach( $this->get_posttype_list() as $post_type ){
			add_action( 'publish_'.$post_type , $this->send_notification() );
		} */
		add_action( 'publish_post' , $this->send_notification() );
		/* add_action( 'publish_gmr_gallery' , $this( 'send_notification' ) );
		add_action( 'publish_show' , $this( 'send_notification' ) );
		add_action( 'publish_gmr_album' , $this( 'send_notification' ) );
		add_action( 'publish_tribe_events' , $this( 'send_notification' ) );
		add_action( 'publish_affiliate_marketing' , $this( 'send_notification' ) );
		add_action( 'publish_listicle_cpt' , $this( 'send_notification' ) ); */

		// error_log( 'Cloudflare supported post type list 1: '. json_encode( $this->get_posttype_list() ) );
	}

	/**
	 * Registers Notification to cloudflare settings.
	 *
	 * @access public
	 * @action bbgi_register_settings
	 * @param string $group
	 * @param string $page
	 */
	public function register_settings( $group, $page ) {
		$section_id = 'beasley_notification_to_cloudflare';

		add_settings_section( $section_id, 'Notification To Cloudflare', '__return_false', $page );
		add_settings_field( 'bbgi_zone_id', 'Zone', 'bbgi_input_field', $page, $section_id, 'name=bbgi_zone_id' );
		register_setting( $group, 'bbgi_zone_id', 'sanitize_text_field' );
	}

	/**
	 * Returns array of post type.
	 *
	 * @return array
	 */
	public function get_posttype_list() {
		return (array) apply_filters( 'notification-to-cloudflare-post-types', array( 'post', 'gmr_gallery', 'show', 'gmr_album', 'tribe_events', 'affiliate_marketing', 'listicle_cpt' )  );
	}

	/**
	 * call this function when publish gallery
	 *
	 *  @param $post_id, $post
	 */
	public function send_notification( $post_id, $post_type = null ) {
		error_log( 'Cloudflare in send notification function.' );

		$zone_id = get_option( 'bbgi_zone_id' );
		error_log( 'Cloudflare function called 2:' );

		if( isset( $zone_id ) && $zone_id != "" ){
			error_log( 'Cloudflare in zone_id condition 3: '. $zone_id );

			$permalink = get_permalink( $post_id );
			$url = get_site_url(
				null,
				'wp-json/experience_engine/v1/page?url='. urlencode_deep($permalink)
			);
			$request_url = 'https://api.cloudflare.com/client/v4/zones/'.$zone_id.'/purge_cache';
			$data = [ "files" => array( $url ) ];

			$response = wp_remote_post( $request_url, array(
					'method' => 'POST',
					'headers' => array(
							'Content-Type' => 'application/json',
							'Authorization' => 'Bearer e-LOIaM9yVbHEpFrbKZGMUa4VUwniazUEn38ApoQ',
							),
							'body' => wp_json_encode( $data )
						)
					);

			$response_json = 'Cloudflare response 4: '. json_encode( $response );
			error_log( $response_json );

			if ( is_wp_error( $response ) ) {
				error_log( 'Cloudflare error notice query var from is_wp_error function 5' );
				add_filter( 'redirect_post_location', array( $this, 'error_notice_query_var' ), 99 );

    			$response_error = 'Cloudflare response error: '.json_encode( $response );
				error_log( $response_error );
			} else {
				if( isset( $response['body']['success'] ) && $response['body']['success'] == 'true' ){
					error_log( 'Cloudflare success notice query var 6' );
					add_filter( 'redirect_post_location', array( $this, 'success_notice_query_var' ), 99 );
				} else {
					error_log( 'Cloudflare error notice query var from fail condition 7' );
					add_filter( 'redirect_post_location', array( $this, 'error_notice_query_var' ), 99 );
				}
				// $response_json = json_decode( $response['body'], true );
			}

			$response_json = 'Cloudflare response: '. json_encode( $response );
			error_log( $response_json );

			// $response_json = json_decode( $response['body'], true );
			// $response_store = 'Page Link: ' . $permalink . ' | URL: ' . $url . ' | Response:'. $response;
			// update_post_meta( $post_id, '_cloudflare_response_data', $response_store );
			// get_post_meta($post_id, '_cloudflare_response_data', true);
		}
	}

	public function success_notice_query_var( $location ) {
		error_log( 'Cloudflare redirect with success parameter 9' );
		remove_filter( 'redirect_post_location', array( $this, 'success_notice_query_var' ), 99 );
		return add_query_arg( array( 'msg' => 'success' ), $location );
	}

	public function error_notice_query_var( $location ) {
		error_log( 'Cloudflare redirect with error parameter 10' );
		remove_filter( 'redirect_post_location', array( $this, 'error_notice_query_var' ), 99 );
		return add_query_arg( array( 'msg' => 'error' ), $location );
	}

	public function show_error_notice() {
		global $typenow, $pagenow;
		if ( ! isset( $_GET['msg'] ) ) {
			return;
		  }
		  if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			$error_class = $_GET['msg'] == 'success' ? 'notice notice-success is-dismissible' : 'error' ;
			$error_message = $_GET['msg'] == 'success' ? 'Cache has been clear successfully' : 'You are unable to clear cache' ;
			 echo '<div class="'. $error_class .'">
				 <p>'. $error_message .'</p>
			 </div>';
			if( $_GET['msg'] == 'phpinfo' ){
				phpinfo();
			}
		}
	}

	public static function extend_curration_post_types( $post_types ) {
		$post_types[] = 'data';
		return $post_types;
	}
}
