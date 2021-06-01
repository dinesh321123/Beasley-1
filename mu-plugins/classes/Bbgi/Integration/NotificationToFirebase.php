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
		// is_admin()
		foreach( $this->get_posttype_list() as $post_type ){
			add_action( 'publish_'.$post_type , $this( 'send_notification' ) );
		}
		// add_action( 'publish_post', $this( 'send_notification' ) );
		// add_action( 'publish_gmr_gallery', $this( 'send_notification' ) );
		// add_action( 'publish_show', $this( 'send_notification' ) );
	}
	/**
	 * Returns array of post type.
	 *
	 * @return array
	 */
	public function get_posttype_list() {
		return array( 'post', 'gmr_gallery', 'show', 'gmr_album', 'tribe_events', 'subscription' );
	}

	/**
	 * call this function when publish gallery
	 * 
	 *  @param $post_id, $post
	 */
	public function send_notification( $post_id, $post_obj ) {
		$permalink = get_permalink( $post_id );
		// echo $permalink, '<br>', urlencode_deep($permalink) ;

		$url = get_site_url(
			null, 
			'wp-json/experience_engine/v1/page?url='. urlencode_deep($permalink) 
		); 
		echo '<br>', $url; exit;
		// echo "<pre>", print_r($_POST), "</pre>"; exit;
	}
}
