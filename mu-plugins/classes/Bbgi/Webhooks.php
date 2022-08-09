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
		add_action( 'save_post', array( $this, 'do_save_post_webhook' ), 10, 2 );
		add_action( 'wp_trash_post', array( $this, 'do_trash_post_webhook' ) );
		add_action( 'delete_post', array( $this, 'do_delete_post_webhook' ) );
		add_action( 'transition_post_status', [ $this, 'do_transition_from_publish' ], 10, 3 );
		add_action( 'shutdown', [ $this, 'do_shutdown' ] );


		$this->debug = true;//( defined( 'WP_DEBUG' ) && WP_DEBUG ) || ( defined( 'WEBHOOKS_LOG_ENABLE' ) && WEBHOOKS_LOG_ENABLE );
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
			$this->write_to_log(@$message, $params);
		}
	}

	protected function write_to_log( $message, $params = [] ) {
		$blog_id = get_current_blog_id();
		$details = get_blog_details( $blog_id );

		$logMessage = sprintf(
			'[#%d - %s] %s - %s',
			$blog_id,
			$details->blogname,
			$message,
			print_r( $params, true )
		);

		if ( defined( 'WP_CLI' ) && \WP_CLI ) {
			try {
				\WP_CLI::log( $logMessage );
			} catch ( \Exception $e ) {
				//do nothing here
			}

		} else if($this->is_wp_minions()){
			try {
				syslog( LOG_ERR, $logMessage );
			} catch (Exception $e) {
				error_log( "minions logging worked outside of syndications");
			}
		} else {
			error_log( $logMessage );
		}
	}


	/**
	 * Triggers webhook on save.
	 *
	 * @param int $post_id The Post id that changed
	 */
	public function do_save_post_webhook( $post_id, $post) {
		$type = '';
		$categories = '';
		$shows = '';

		if($this->is_wp_minions()){
			$type = $post->post_type;
			$categories = get_the_category( $post_id );
			$shows = get_the_terms( $post_id, '_shows' );
		}
		$this->do_lazy_webhook( $post_id, [ 'source' => 'save_post', 'post_type' => $type, 'category_list' => $categories, 'shows_list' => $shows ] );
	}

	/**
	 * Triggers webhook on trash.
	 *
	 * @param int $post_id The Post id that changed
	 */
	public function do_trash_post_webhook( $post_id ) {
		$this->do_lazy_webhook( $post_id, [ 'source' => 'wp_trash_post' ] );
	}

	/**
	 *  Triggers webhook on delete.
	 *
	 * @param int $post_id The Post id that changed
	 */
	public function do_delete_post_webhook( $post_id ) {
		$this->do_lazy_webhook( $post_id, [ 'source' => 'delete_post' ] );
	}

	/**
	 * Triggers webhook when transition to a publish status to a non-publish status.
	 *
	 * @param string   $new_status The new post status.
	 * @param string   $old_status The old post status.
	 *
	 * @param \WP_Post $post The Post id that changed
	 */
	public function do_transition_from_publish( $new_status, $old_status, $post ) {
		// if we're transitioning from publish to anything else we need to call the webhook.
		if ( $new_status !== 'publish' && $old_status === 'publish' ) {
			$this->do_lazy_webhook( $post->ID, [
				'source'         => 'transition_status',
				'only_published' => false,
			] );
		}

	}


	/**
	 * Triggers any pending webhook before shutdown.
	 *
	 * @return bool
	 */
	public function do_shutdown() {
		if ( ! empty( $this->pending ) ) {
			$this->log( 'shutdown called', ["pending" => "is not empty"] );
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
			//$this->log( 'shutdown called', ["pending" => "is empty"] );
			return false;
		}
	}

	/**
	 * Stores a pending webhook to be triggered later.
	 *
	 * @param int $post_id The post that changed.
	 * @param array $opts The change context
	 *
	 * @return bool
	 */
	public function do_lazy_webhook( $post_id, $opts = [] ) {

		$site_id = get_current_blog_id();
		$only_published = isset( $opts['only_published' ] ) ? $opts['only_published' ] : true;

		$this->log( 'do_lazy_webhook called.', [ 'post_id' => $post_id, 'opts' => $opts ] );

		if ( ! isset( $this->pending[ $site_id ] ) && $this->needs_webhook( $post_id, $only_published ) ) {
			$publisher = get_option( 'ee_publisher', false );

			$this->pending[ $site_id ] = [
				'publisher' => $publisher,
				'post_id'   => $post_id,
				'opts'      => $opts,
			];

			//$this->log( 'pending webhook set. ', $this->pending[ $site_id ] );

			return true;
		} else {
			$this->log('a pending webhook exists for site needs_webhook returned false', [ "post_id" => $post_id] );
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

		$post_type = get_post_type( $post_id );
		if(!$post_type && isset($opts['post_type'])){
			$post_type = $opts['post_type'];
		}

		$categories = get_the_category( $post_id );
		if (!$categories && isset($opts['category_list'])){
			$categories = $opts['category_list'];
		}

		$shows = get_the_terms( $post_id, '_shows' );;
		if (!$shows && isset($opts['shows_list'])){
			$shows = $opts['shows_list'];
		}

		$categoryCSV = '';
		if ( is_array( $categories ) && ! empty( $categories ) ) {
			$categoryCSV = implode( ",", wp_list_pluck($categories, 'slug' ) );
		}

		$showsCSV = '';
		if ( is_array( $shows ) && ! empty( $shows ) ) {
			$showsCSV = implode( ",", wp_list_pluck($shows, 'slug' ) );
		}

		if ($post_type !== 'gmr_homepage') {
			$this->clearCloudFlareCache($post_id, $post_type, $categories, $shows);
		}

		if ($post_type !== 'show') {
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
					'post_type'    	=> $post_type,
					'categories'	=> $categoryCSV,
					'shows'			=> $showsCSV,
				],
			];

			$this->log( 'calling webhook', $request_args );

			wp_remote_post( $url, $request_args );
		}

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
	 * @param int $post_id         The post id.
	 * @param bool $only_published Only fires if post is published.
	 * @return bool
	 */
	public function needs_webhook( $post_id, $only_published = true ) {
		/* autosaves don't need webhook */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			$this->log('no webhook when auto saving post');
			return false;
		}

		/** don't webhook bulk edit requests */
		if ( isset( $_REQUEST['bulk_edit'] ) ) {
			$this->log('no webhook called during bulk edit');
			return false;
		}

		$post = get_post( $post_id );

		if ( $only_published && $post->post_status !== 'publish' ) {
			$this->log('not a published post. no webhook ran', ["post_status" => $post->post_status]);
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
			'gmr_gallery',
			'episode',
			'tribe_events',
			'contest',
			'show',
			'gmr_homepage',
			'gmr_mobile_homepage',
			'affiliate_marketing',
			'listicle_cpt'
		];
	}

	public function clearCloudFlareCache($postID, $posttype, $categories, $shows){
		$this->log("clearCloudFlareCache", ["postId" => $postID]);

        if(!$postID){
            return false;
        }

		$cloudflaretoken = get_site_option( 'ee_cloudflare_token' );
		$zone_id = get_option('cloud_flare_zoneid');

		if ( empty($cloudflaretoken) || empty($zone_id) ) {
			$this->log("Cloudflare not configured for this site" );

			return false;
		}

        $post = get_post( $postID );
        $post_slug = $post->post_type.'-'.$post->post_name;

		$cache_tags = [$post_slug];
		$cache_tags[] = 'feed-'.$post_slug;
		if ( !empty($categories)) {
			foreach ($categories as $category) {
				$cache_tags[] = 'archive-' . $category->slug;
				$cache_tags[] = 'feed-' .$category->slug;
			}
		}

		if (!empty($shows)) {
			foreach ($shows as $show) {
				$cache_tags[] = 'show-' . $show->slug;
				$cache_tags[] = 'feed-' .$show->slug;
			}
		}

		if (!empty($posttype)) {
			$cache_tags[] = 'archive-' . $posttype;
			$cache_tags[] =  'feed-'.$posttype;
			if ($posttype == 'episode') {
				$cache_tags[] = 'podcast';
			}
		}

		// Clear specific page caches
		if ( function_exists( 'batcache_clear_url' ) && class_exists( 'batcache' ) ) {
			$url = get_permalink($postID);
			//$this->log( 'Batcache URL' , [ 'url' => $url ] );
			batcache_clear_url( $url );
		}

		$this->log( 'Cloudflare Clearing Cache Tags', $cache_tags);


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



		if ( is_wp_error( $response ) ) {
			$this->log('Cloudflare Response', ["errormessage" => $response->get_error_message()]);
		} else {
			$this->log('Cloudflare Response', [ "response" => json_encode( $response )]);
		}

		return true;
    }

}
