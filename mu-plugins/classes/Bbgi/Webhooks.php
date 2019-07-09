<?php

namespace Bbgi;

class Webhooks extends \Bbgi\Module {

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		add_action( 'save_post', array( $this, 'do_webhook' ) );
		add_action( 'wp_trash_post', array( $this, 'do_webhook' ) );
		add_action( 'delete_post', array( $this, 'do_webhook' ) );
	}

	/**
	 * Sends webhook notification when a post is published, changed, trashed, or deleted
	 *
	 * @access public
	 * @action save_post ($post_id)
	 * @action wp_trash_post ($post_id)
	 * @action delete_post ($post_id)
	 */
	public function do_webhook( $post_id ) {
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
			return;
		}

		$post = get_post( $post_id );
		if ( $post->post_status !== 'publish' ) {
			return;
		}

		$base_url  = get_site_option( 'ee_host', false );
		$appkey    = get_site_option( 'ee_appkey', false );
		$publisher = get_option( 'ee_publisher', false );

		// Abort if notification URL isn't set
		if ( ! $base_url || ! $publisher || ! $appkey ) {
			return;
		}

		$url = trailingslashit( $base_url ) . 'admin/publishers/' . $publisher . '/build?appkey=' . $appkey;

		wp_remote_post( $url, array(
			'blocking' => false,
		) );
	}

}
