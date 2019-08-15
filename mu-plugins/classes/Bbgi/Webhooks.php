<?php

namespace Bbgi;

class Webhooks extends \Bbgi\Module {

	/**
	 * Pending webhook data
	 */
	public $pending = [];

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

		$this->debug = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || ( defined( 'WEBHOOKS_LOG_ENABLE' ) && WEBHOOKS_LOG_ENABLE );
	}

	/**
	 * Logs a message if debug is enabled.
	 *
	 * @param string $message The message log.
	 * @param array  $params Optional associated params.
	 *
	 * @return void
	 */
	protected function log( $message, $params = [] ) {
		if ( $this->debug ) {
			$blog_id = get_current_blog_id();
			$details = get_blog_details( $blog_id );

			error_log(
				sprintf(
					'[#%d - %s] %s - %s',
					$blog_id,
					$details->blogname,
					$message,
					print_r( $params, true )
				)
			);
		}
	}


	/**
	 * Helper to trigger save webhook and unregister self.
	 *
	 * @param int $post_id The Post id that changed
	 */
	public function do_save_post_webhook( $post_id ) {
		$this->do_lazy_webhook( $post_id, [ 'source' => 'save_post' ] );
	}

	/**
	 * Helper to trigger trash webhook and unregister self.
	 *
	 * @param int $post_id The Post id that changed
	 */
	public function do_trash_post_webhook( $post_id ) {
		$this->do_lazy_webhook( $post_id, [ 'source' => 'wp_trash_post' ] );
	}

	/**
	 * Helper to trigger delete webhook and unregister self.
	 *
	 * @param int $post_id The Post id that changed
	 */
	public function do_delete_post_webhook( $post_id ) {
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
			foreach( $this->pending as $site_id => $pending_webhook ) {
				$this->log( 'do_webhook' , [ 'site_id' => $site_id ] );
				$this->do_webhook(
					$pending_webhook['publisher'],
					$pending_webhook['post_id'],
					$pending_webhook['opts']
				);
			}

			$this->pending = [];

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
		$site_id = get_current_blog_id();

		$this->log( 'do_lazy_webook called.', [ 'post_id' => $post_id, 'opts' => $opts ] );

		if ( ! isset( $this->pending[ $site_id ] ) && $this->needs_webhook( $post_id ) ) {
			$publisher = get_option( 'ee_publisher', false );

			$this->pending[ $site_id ] = [
				'publisher' => $publisher,
				'post_id'   => $post_id,
				'opts'      => $opts,
			];

			$this->log( 'pending webook set. ', $this->pending[ $site_id ] );

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
	 *
	 * @param string $publisher publisher that this webhook should trigger.
	 * @param int $post_id The source post that changed
	 * @param array $opts Optional opts
	 * @return void
	 */
	public function do_webhook( $publisher, $post_id, $opts = [] ) {
		$debug_params = [
			'publisher' => $publisher,
			'post_id'   => $post_id,
			'opts'      => $opts
		];

		$base_url  = get_site_option( 'ee_host', false );
		$appkey    = get_site_option( 'ee_appkey', false );

		// Abort if notification URL isn't set
		if ( ! $base_url || ! $publisher || ! $appkey ) {
			$this->log( 'do_webhook notification url is not set.', $debug_params );
			return;
		}

		$url = trailingslashit( $base_url ) . 'admin/publishers/' . $publisher . '/build?appkey=' . $appkey;

		$request_args = [
			'blocking'        => false,
			'body'            => [
				'post_id'       => $post_id,
				'source_action' => ! empty( $opts['source'] ) ? $opts['source'] : 'unknown',
				'request_uri'   => ! empty( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : 'unknown',
				'wp_cli'        => defined( 'WP_CLI' ) && WP_CLI ? 'yes' : 'no',
				'wp_cron'       => defined( 'DOING_CRON' ) && DOING_CRON ? 'yes' : 'no',
				'wp_ajax'       => defined( 'DOING_AJAX' ) && DOING_AJAX ? 'yes' : 'no',
				'wp_minions'    => $this->is_wp_minions() ? 'yes' : 'no',
			],
		];

		$this->log( 'calling webohook', $request_args );

		wp_remote_post( $url, $request_args );
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

		$post = get_post( $post_id );

		if ( $post->post_status !== 'publish' ) {
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
