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

		$url = get_option( 'ee_webhook_url', false );

		// Abort if notification URL isn't set
		if ( ! $url ) {
			return;
		}

		wp_remote_post( $url, array(
			'blocking' => false,
			'headers'  => array(
				'Content-Type' => 'application/json',
			),
			'body'     => wp_json_encode( array(
				'publisher' => get_option( 'ee_publisher', '' ),
				'home_url'  => home_url( '/' ),
			) ),
		) );
	}

}
