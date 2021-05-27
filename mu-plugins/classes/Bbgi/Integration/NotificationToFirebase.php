<?php
/**
 * Module responsible for send a notification to firebase to clear cache for that page.
 *
 * @package Bbgi
 */

namespace Bbgi\Integration;


class NotificationToFirebase extends \Bbgi\Module
{
	public function register()
	{
		add_action( 'publish_post', $this( 'send_notification_post' ) );
		add_action( 'publish_gmr_gallery', $this( 'send_notification_gallery' ) );
	}

	/**
	 * call this function when publish post 
	 * 
	 *  @param $post_id, $post
	 */
	public function send_notification_post( $post_id, $post ) {
		// echo $post_id, ' - ', 'Send notification  to gallery'; exit;
	}
	/**
	 * call this function when publish gallery
	 * 
	 *  @param $post_id, $post
	 */
	public function send_notification_gallery( $post_id, $post ) {
		// echo $post_id, ' - ', 'Send notification to gallery'; exit;
	}
}
