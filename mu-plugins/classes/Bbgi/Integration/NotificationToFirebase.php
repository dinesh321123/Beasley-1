<?php
/**
 * Module responsible for send a notification to firebase to clear cache for that page.
 *
 * @package Bbgi
 */

namespace Bbgi\Integration;

const posttype = array('');

class NotificationToFirebase extends \Bbgi\Module
{
	public function register()
	{
		add_action( 'bbgi_register_settings', $this( 'register_settings' ), 10, 2 );
		// add_filter( 'notification-to-firebase-post-types', array( __CLASS__, 'extend_curration_post_types' ) );

		foreach( $this->get_posttype_list() as $post_type ){
			add_action( 'publish_'.$post_type , $this( 'send_notification' ) );
		}
	}

	/**
	 * Registers Notification to Firebase settings.
	 *
	 * @access public
	 * @action bbgi_register_settings
	 * @param string $group
	 * @param string $page
	 */
	public function register_settings( $group, $page ) {
		$section_id = 'beasley_notification_to_firebase';

		add_settings_section( $section_id, 'Notification To Firebase', '__return_false', $page );
		add_settings_field( 'bbgi_zone_id', 'Zone', 'bbgi_input_field', $page, $section_id, 'name=bbgi_zone_id' );
		register_setting( $group, 'bbgi_zone_id', 'sanitize_text_field' );
	}

	/**
	 * Returns array of post type.
	 *
	 * @return array
	 */
	public function get_posttype_list() {
		return (array) apply_filters( 'notification-to-firebase-post-types', array( 'post', 'gmr_gallery', 'show', 'gmr_album', 'tribe_events' )  );
	}

	/**
	 * call this function when publish gallery
	 * 
	 *  @param $post_id, $post
	 */
	public function send_notification( $post_id, $post_type = null ) {
		$zone_id = get_option( 'bbgi_zone_id' );
		if( isset( $zone_id ) && $zone_id != "" ){
			$permalink = get_permalink( $post_id );
			$url = get_site_url(
				null, 
				'wp-json/experience_engine/v1/page?url='. urlencode_deep($permalink) 
			); 
			$request_url = 'https://api.cloudflare.com/client/v4/zones/'.$zone_id.'/purge_cache';
			$data = [ "url" => $url ];
			$response = wp_remote_post($request_url, array(
					'method' => 'POST',
					'headers' => array(
							'Content-Type' => 'application/json',
							'X-Auth-Email' => 'Kevin.Gilper@bbgi.com',
							'X-Auth-Key'=> 'e-LOIaM9yVbHEpFrbKZGMUa4VUwniazUEn38ApoQ',
							// 'Authorization' => 'Bearer e-LOIaM9yVbHEpFrbKZGMUa4VUwniazUEn38ApoQ',
							),
							'body' => $data
						)
					);
			$response = json_decode( $response['body'], true );
			$response_store = 'Page Link: ' . $permalink . ' | URL: ' . $url . ' | Response:'. $response;
			// update_post_meta( $post_id, '_firebase_response_data', $response_store );
			// get_post_meta($post_id, '_firebase_response_data', true);

			print_r($response); exit;
			
			/* $timestamp = date("Y-m-d H:i:s");
			$message =  'Requested time:'.$timestamp.'[Requested URL: '.$request_url.' ||  Response->body: '.$response['body'].' ||  Response: '.json_encode($response['response']).']';
			$dateval = date("dmy");
			$filename = __DIR__ .'/log/'.$dateval.".txt";
			$current_url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			if (!file_exists(__DIR__.'/log')) {
				mkdir(__DIR__.'/log', 0777, true);
			}
			$fh = file_exists($filename) ? fopen($filename, 'a') : fopen($filename, 'w') ;
			fwrite($fh, $message."\n");
			fclose($fh); */ 
		}
	}



	public static function extend_curration_post_types( $post_types ) {
		$post_types[] = 'data';
		return $post_types;
	}

}
