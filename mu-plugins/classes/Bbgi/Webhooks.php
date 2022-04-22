<?php

namespace Bbgi;

class Webhooks extends \Bbgi\Module {

	/**
	 * Pending webhook data
	 */
	public $pending = [];
	public $postdata = [];
    public $isPrePostDone = false;
	public $isSavePostDone = false;
	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		add_filter( 'pre_post_update', array($this,'get_prepost_data'), 10, 2 );
		add_action( 'save_post', array( $this, 'do_save_post_webhook' ), 10, 2 );
		add_action( 'wp_trash_post', array( $this, 'do_trash_post_webhook' ) );
		add_action( 'delete_post', array( $this, 'do_delete_post_webhook' ) );
		add_action( 'transition_post_status', [ $this, 'do_transition_from_publish' ], 10, 3 );
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
	 * Triggers webhook on save.
	 *
	 * @param int $post_id The Post id that changed
	 */
	public function do_save_post_webhook( $post_id, $post) {

		$type = '';
		if($this->is_wp_minions()){
			$type = $post->post_type;
		}
		$doLazyWebhook = true;
		if($post->post_type === 'gmr_gallery'){
			if($this->isSavePostDone){
				return true;
			}
			$meta = get_post_meta($post_id);  
			
			$oldMeta = $this->postdata['METADATA']; 
		
			$post = (array)$post;
			
			/* remove extra fields */
			$remove = ['ID', 'filter','METADATA','post_modified', 'post_modified_gmt'];       
			foreach($remove as $key){
				unset($post[$key]);
				unset($this->postdata[$key]);
			}
			
			/* check post data */
			$diff = array_diff($this->postdata, $post); 
			$diff1 = array_diff($post, $this->postdata);    
			
			/* Check Meta Data */
			$metaDiff = $this->multi_diff($meta,$oldMeta);
			$metaDiff1 = $this->multi_diff($oldMeta, $meta);			
		
			if(sizeof($metaDiff) > 0 || sizeof($metaDiff1) > 0){
				$diff['METADATA']  = true;
			}
			
			$this->log( 'diff' , $diff );
			$this->log( 'diff1' , $diff1);
			if(sizeof($diff) > 0 || sizeof($diff1) > 0){				
				$doLazyWebhook  = false;
			}
			$this->isSavePostDone = true;
			if($doLazyWebhook){
				return true;
			}
		}

		
			$this->do_lazy_webhook( $post_id, [ 'source' => 'save_post', 'post_type' => $type ] );			
		
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
	 *
	 * @return bool
	 */
	public function do_lazy_webhook( $post_id, $opts = [] ) {
		$site_id = get_current_blog_id();
		$only_published = isset( $opts['only_published' ] ) ? $opts['only_published' ] : true;

		$this->log( 'do_lazy_webook called.', [ 'post_id' => $post_id, 'opts' => $opts ] );

		if ( ! isset( $this->pending[ $site_id ] ) && $this->needs_webhook( $post_id, $only_published ) ) {
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

		$post_type = get_post_type( $post_id );

		if(!$post_type && isset($opts['post_type'])){
			$post_type = $opts['post_type'];
		}

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
	 * @param int $post_id         The post id.
	 * @param bool $only_published Only fires if post is published.
	 * @return bool
	 */
	public function needs_webhook( $post_id, $only_published = true ) {
		/* autosaves don't need webhook */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		/** don't webhook bulk edit requests */
		if ( isset( $_REQUEST['bulk_edit'] ) ) {
			return false;
		}

		$post = get_post( $post_id );

		if ( $only_published && $post->post_status !== 'publish' ) {
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
			'gmr_homepage',
			'gmr_mobile_homepage',
			'affiliate_marketing',
			'listicle_cpt'
		];
	}

	public function get_prepost_data( $post_ID, $postarr ) {
		if($postarr['post_type'] !== 'gmr_gallery'){
			return true;
		}
		if($this->isPrePostDone){
			return true;
		}	
        $post = (array) get_post( $post_ID );
        $meta =  get_post_meta($post_ID); 
        $post['METADATA'] = $meta;
        $this->postdata = $post;     
		$this->isPrePostDone = true;   
        return true;
    }

	public function multi_diff($array1,$array2){
        foreach($array1 as $key => $value) {
            if(is_array($value)) {
                if(!isset($array2[$key])) {
                    $difference[$key] = $value;
                } else if(!is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = $this->multi_diff($value, $array2[$key]);
                    if($new_diff != FALSE) {
                        $difference[$key] = $new_diff;
                    }
                }
            } else if(!isset($array2[$key]) || $array2[$key] != $value) {
                $difference[$key] = $value;
            }
        }
        return !isset($difference) ? [] : $difference;
    }

}
