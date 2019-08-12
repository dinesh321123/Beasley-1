<?php

namespace Bbgi;

class Webhooks extends \Bbgi\Module {

	/**
	 * Pending webhook data
	 */
	public $pending = false;

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		add_action( 'save_post', array( $this, 'do_save_post_webhook' ) );
		add_action( 'wp_trash_post', array( $this, 'do_trash_post_webhook' ) );
		add_action( 'delete_post', array( $this, 'do_delete_post_webhook' ) );
		add_action( 'shutdown', [ $this, 'do_shutdown' ] );
	}

	/**
	 * Helper to trigger save webhook and unregister self.
	 *
	 * @param int $post_id The Post id that changed
	 */
	public function do_save_post_webhook( $post_id ) {
		remove_action( 'save_post', [ $this, 'do_save_post_webhook' ] );

		$this->do_lazy_webhook( $post_id, [ 'source' => 'save_post' ] );
	}

	/**
	 * Helper to trigger trash webhook and unregister self.
	 *
	 * @param int $post_id The Post id that changed
	 */
	public function do_trash_post_webhook( $post_id ) {
		remove_action( 'wp_trash_post', [ $this, 'do_trash_post_webhook' ] );

		$this->do_lazy_webhook( $post_id, [ 'source' => 'wp_trash_post' ] );
	}

	/**
	 * Helper to trigger delete webhook and unregister self.
	 *
	 * @param int $post_id The Post id that changed
	 */
	public function do_delete_post_webhook( $post_id ) {
		remove_action( 'delete_post', [ $this, 'do_delete_post_webhook' ] );

		$this->do_lazy_webhook( $post_id, [ 'source' => 'delete_post' ] );
	}

	/**
	 * Triggers any pending webhook before shutdown.
	 *
	 * @return bool
	 */
	public function do_shutdown() {
		remove_action( 'shutdown', [ $this, 'do_shutdown' ] );

		if ( ! empty( $this->pending ) ) {
			$this->do_webhook(
				$this->pending['post_id'],
				$this->pending['opts']
			);

			$this->pending = false;

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Stores a pending webhook to be triggered later.
	 *
	 * @param int $post_id The post that changed.
	 * @param array $opts The change context
	 * @return bool
	 */
	public function do_lazy_webhook( $post_id, $opts = [] ) {
		if ( empty( $this->pending ) ) {
			$this->pending = [
				'post_id' => $post_id,
				'opts'    => $opts,
			];

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Sends webhook notification when a post is published, changed, trashed, or deleted
	 *
	 * @access public
	 * @action save_post ($post_id)
	 * @action wp_trash_post ($post_id)
	 * @action delete_post ($post_id)
	 * @param int $post_id The source post that changed
	 * @param array $opts Optional opts
	 * @return void
	 */
	public function do_webhook( $post_id, $opts = [] ) {
		if ( ! $this->needs_webhook( $post_id ) ) {
			return false;
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
			'blocking'        => false,
			'body'            => [
				'post_id'       => $post_id,
				'source_action' => ! empty( $opts['source'] ) ? $opts['source'] : 'unknown',
				'request_uri'   => ! empty( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : 'unknown',
				'wp_cli'        => defined( 'WP_CLI' ) && WP_CLI ? 'yes' : 'no',
				'wp_cron'       => defined( 'DOING_CRON' ) && DOING_CRON ? 'yes' : 'no',
				'wp_ajax'       => defined( 'DOING_AJAX' ) && DOING_AJAX ? 'yes' : 'no',
				'wp_minions'    => $this->is_wp_minions() ? 'yes' : 'no',
			]
		) );
	}

	/**
	 * Checks if we are running in WP-Minions Job Runner context.
	 *
	 * @return boolean
	 */
	public function is_wp_minions() {
		return class_exists( '\WpMinions\Plugin' ) &&
			\WpMinions\Plugin::get_instance()->did_run;
	}

	/**
	 * Determines if the specified post & context needs a webhook push.
	 *
	 * @param int $post_id The post id.
	 * @return bool
	 */
	public function needs_webhook( $post_id ) {
		/* autosaves don't need webhook */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		/** don't webhook bulk edit requests */
		if ( isset( $_REQUEST['bulk_edit'] ) ) {
			return false;
		}

		$supported = $this->get_supported_post_types();
		$post_type = get_post_type( $post_id );

		return in_array( $post_type, $supported, true );
	}

	/**
	 * Webhook is only supported for the following post types.
	 *
	 * @return array
	 */
	public function get_supported_post_types() {
		return [
			'post',
			'page',
			'attachment',
			'gmr_gallery',
			'gmr_album',
			'episode',
			'tribe_events',
			'subscription',
			'content-kit',
			'contest',
			'songs',
			'show',
			'gmr_homepage',
			'gmr_mobile_homepage',
			'podcast',
		];
	}

}
