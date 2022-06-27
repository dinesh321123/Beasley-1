<?php
/**
 * API for interacting with Expression Engine
 */

namespace Bbgi\Integration;

class ExperienceEngine extends \Bbgi\Module {

	private static $_fields = array(
		'ee_host'             => 'API host',
		'ee_cache_token'      => 'Cache Clear Token',
		'ee_appkey'           => 'EE App Key',
		'ee_notification_key' => 'EE Notification App Key',
	);

	/**
	 * Registers module.
	 *
	 * @access public
	 */
	public function register() {
		add_action( 'wpmu_options', $this( 'show_network_settings' ) );
		add_action( 'update_wpmu_options', $this( 'save_network_settings' ) );
		add_action( 'rest_api_init', $this( 'init_rest_api' ) );

		add_filter( 'bbgiconfig', $this( 'update_bbgiconfig' ) );
	}

	/**
	 * Saves network settings.
	 *
	 * @access public
	 * @action update_wpmu_options
	 */
	public function save_network_settings() {
		foreach ( self::$_fields as $id => $label ) {
			$value = filter_input( INPUT_POST, $id );
			$value = sanitize_text_field( $value );
			update_site_option( $id, $value );
		}
	}

	/**
	 * Shows network settings
	 *
	 * @access public
	 * @action wpmu_options
	 */
	public function show_network_settings() {
		?><h2>Experience Engine Settings</h2>
		<table id="menu" class="form-table">
			<?php foreach ( self::$_fields as $id => $label ) : ?>
				<tr>
					<th scope="row"><?php echo esc_html( $label ); ?></th>
					<td>
						<input type="text" class="regular-text" name="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( get_site_option( $id ) ); ?>">
					</td>
				</tr>
			<?php endforeach; ?>
		</table><?php
	}

	protected function _get_request_cache_time( $request ) {
		$response_headers = wp_remote_retrieve_headers( $request );
		if ( empty( $response_headers['cache-control'] ) ) {
			return 0;
		}

		$cache_control = explode( ',', $response_headers['cache-control'] );
		$cache_time = 0;
		foreach ( $cache_control as $control_string ) {
			$control_string = strtolower( trim( $control_string ) );
			$parts = explode( '=', $control_string );

			if ( $parts[0] == 'no-cache') {
				$cache_time = 86400;
				break;
			}

			if ( $parts[0] == 's-maxage' ) {
				$cache_time = end( $parts );
				break;
			} elseif ( $parts[0] == 'max-age' ) {
				$cache_time = end( $parts );
			}
		}

		$cache_time = absint( $cache_time );
		if ( $cache_time < 5 * MINUTE_IN_SECONDS ) {
			$cache_time = 5 * MINUTE_IN_SECONDS;
		}

		return $cache_time;
	}

	protected function _get_publisher_key() {
		return get_option( 'ee_publisher' );
	}

	protected function _get_host() {
		$host = get_site_option( 'ee_host' );
		if ( ! filter_var( $host, FILTER_VALIDATE_URL ) ) {
			$host = 'https://experience.bbgi.com/';
		}

		return untrailingslashit( $host ) . '/v1/';
	}

	public function send_request( $path, $args = array() ) {
		$host = $this->_get_host();
		$args['headers'] = array( 'Content-Type' => 'application/json' );
		$args['timeout'] = 30;
		if ( empty( $args['method'] ) ) {
			$args['method'] = 'GET';
		}

		// Append the device parameter to indicate this request is from the website
		if ( false === stripos( $path, '?' ) ) {
			$path .= '?';
		}
		else {
			$path .= '&';
		}

		$path .= 'device=other';

		return wp_remote_request( $host . $path, $args );
	}

	public function do_request($path, $args = array(), $cache_group = '') {
		$cache_index = get_option( 'ee_cache_index', 0 );
		$cache_key = empty($cache_group) ? $cache_index : $cache_group;

		$response = wp_cache_get( $path, "experience_engine_api-{$cache_key}" );

		if ( empty( $response ) ) {
			$request = $this->send_request( $path, $args );
			if ( is_wp_error( $request ) ) {
				return $request;
			}

			$request_response_code = (int) wp_remote_retrieve_response_code( $request );
			$is_valid_res = ( $request_response_code >= 200 && $request_response_code <= 299 );
			if ( false === $request || ! $is_valid_res ) {
				return $request;
			}

			$response = json_decode( wp_remote_retrieve_body( $request ), true );
			$cache_time = $this->_get_request_cache_time( $request );
			if ( $cache_time ) {

				wp_cache_set( $path, $response, "experience_engine_api-{$cache_key}", $cache_time );
			}

			error_log($this->log_prefix() . "cached contents from $path into group: experience_engine_api-{$cache_key} for $cache_time seconds\n");
		} else {
			error_log($this->log_prefix() . "served from cache contents from $path in group: experience_engine_api-{$cache_key}\n");
		}

		return $response;
	}

	public function get_publisher_list() {
		$publishers = $this->do_request( 'publishers' );
		if ( is_wp_error( $publishers ) ) {
			$publishers = array();
		}

		return $publishers;
	}

	public function get_publisher() {
		$data = false;
		$publisher = $this->_get_publisher_key();
		if ( ! empty( $publisher ) ) {
			$data = $this->do_request( "publishers/{$publisher}" );
			if ( is_wp_error( $data ) ) {
				$data = array();
			}

			if ( is_array( $data ) && count( $data ) == 1 && is_array( $data[0] ) ) {
				$data = $data[0];
			}
		}

		return $data;
	}

	public function get_publisher_feeds() {
		$data = array();
		$publisher = $this->_get_publisher_key();
		if ( ! empty( $publisher ) ) {
			$data = $this->do_request( "publishers/{$publisher}/feeds/" );
			if ( is_wp_error( $data ) ) {
				$data = array();
			}
		}

		return $data;
	}

	public function get_publisher_feeds_with_content() {
		$data = array();
		$publisher = $this->_get_publisher_key();
		if ( ! empty( $publisher ) ) {
			$url = "experience/channels/{$publisher}/feeds/content/";

			$data = $this->do_request( $url, array(), 'ee_data');
			if ( is_wp_error( $data ) ) {
				$data = array();
			}
		}

		return $data;
	}

	public function get_publisher_feed( $feed ) {
		$data = array();
		$publisher = $this->_get_publisher_key();
		if ( ! empty( $data ) ) {
			$data = $this->do_request( "publishers/{$publisher}/feeds/{$feed}" );
			if ( is_wp_error( $data ) ) {
				$data = array();
			}
		}

		return $data;
	}

	public function get_locations() {
		$locations = $this->do_request( 'locations' );
		if ( is_wp_error( $locations ) ) {
			$locations = array();
		}

		return $locations;
	}

	public function get_genres() {
		$genres = $this->do_request( 'genres' );
		if ( is_wp_error( $genres ) ) {
			$genres = array();
		}

		return $genres;
	}

	public function get_ad_slot_unit_id( $slot ) {
		static $data = null;

		if ( is_null( $data ) ) {
			$publisher = $this->_get_publisher_key();
			if ( ! empty( $publisher ) ) {
				$data = $this->do_request( "experience/channels/{$publisher}/ads/" );
				if ( is_wp_error( $data ) ) {
					$data = array();
				}
			}
		}

		if ( is_array( $data ) && ! empty( $data ) ) {
			foreach ( $data as $config ) {
				if ( ! is_array( $config ) ) {
					$config = (array) $config;
				}
				if ( $config['region'] == $slot ) {
					return sprintf( '/%s/%s', $config['publisherId'], $config['adUnitId'] );
				}
			}
		}

		return '';
	}

	public function update_bbgiconfig( $config ) {
		$config['eeapi'] = $this->_get_host();
		$config['wpapi'] = rest_url( '/experience_engine/v1/' );
		$config['wpapi-mmrp'] = rest_url( '/megamenu_recent_posts/v1/' );

		return $config;
	}

	public function init_rest_api() {
		$namespace = 'experience_engine/v1';

		register_rest_route( $namespace, '/purge-cache', array(
			'methods'             => 'POST',
			'callback'            => $this( 'rest_purge_cache' ),
			'permission_callback' => array( $this, 'check_purge_cache_permissions' ),
			'show_in_index'       => false,
		) );

		register_rest_route( $namespace, '/test', array(
				'methods'             => 'POST',
				'callback'            => $this( 'test_post' ),
				'show_in_index'       => false,
		) );

		register_rest_route( $namespace, '/purge-ee-cache', array(
				'methods'             => 'POST',
				'callback'            => $this( 'rest_purge_ee_cache' ),
				'permission_callback' => array( $this, 'check_purge_cache_permissions' ),
				'show_in_index'       => false,
		) );

		$authorization = array(
			'authorization' => array(
				'type'              => 'string',
				'required'          => false,
				'validate_callback' => function( $value ) {
					return strlen( $value ) > 0;
				},
			),
		);

		register_rest_route( $namespace, 'feeds-content', array(
			'methods'  => \WP_REST_Server::CREATABLE,
			'callback' => $this( 'rest_get_feeds_content' ),
			'args'     => array_merge( $authorization, [] ),
		) );
	}

	/**
	 * Checks if the current request has permissions to purge cache
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return boolean
	 */
	public function check_purge_cache_permissions( \WP_REST_Request $request ) {
		$token = get_site_option( 'ee_cache_token', false );

		if ( empty( $token ) ) {
			error_log( $this->log_prefix() . 'check_purge_cache_permissions token missing' );
			return false;
		}

		if ( $token === $request->get_header( 'Authorization' ) ) {
			return true;
		}

		error_log( $this->log_prefix() . 'check_purge_cache_permissions token did not match authorization' );
		return false;
	}

	public function test_post( \WP_REST_Request $request ) {
		if ($request->is_json_content_type()) {
			error_log( $this->log_prefix() . 'received json' );
			$content = $request->get_body();
			return wp_send_json($content);
		}
	}

	public function rest_purge_cache( \WP_REST_Request $request ) {
		error_log( $this->log_prefix() . 'rest_purge_cache reached' );

		//clear ee content feed values
		$publisher = $this->_get_publisher_key();
		$url = "experience/channels/{$publisher}/feeds/content/";
		// wp_cache_delete($url, 'experience_engine_api-ee_data');

		if (!$request->is_json_content_type()) {
			error_log( $this->log_prefix() . 'received json' );
			return rest_ensure_response( 'No Json' );
		}



		$content = $request->get_body();
		error_log( $this->log_prefix() . 'received json' );

		wp_cache_set($url, $content, 'experience_engine_api-ee_data', 120);

		// Clear specific page caches
		if ( function_exists( 'batcache_clear_url' ) && class_exists( 'batcache' ) ) {
			$home = trailingslashit( get_option( 'home' ) );
			batcache_clear_url( $home );
			batcache_clear_url( $home . 'feed/' );
		}

		$this->clearCloudFlareHomeCache();

		$track = $request->get_header('track-id');

		error_log($this->log_prefix() . "cache purged for track: $track\n");

		return rest_ensure_response( 'Cache Flushed' );
	}

	public function rest_purge_ee_cache() {
		//clear ee content feed values
		$publisher = $this->_get_publisher_key();
		$url = "experience/channels/{$publisher}/feeds/content/";
		wp_cache_delete($url, 'experience_engine_api-ee_data');

		// Clear EE Cache
		update_option( 'ee_cache_index', time(), 'no' );

		// Clear specific page caches
		if ( function_exists( 'batcache_clear_url' ) && class_exists( 'batcache' ) ) {
			$home = trailingslashit( get_option( 'home' ) );
			batcache_clear_url( $home );
			batcache_clear_url( $home . 'feed/' );
		}

		return rest_ensure_response( 'Cache Flushed' );
	}

	public function rest_get_feeds_content( $request ) {
		$publisher = get_option( 'ee_publisher' );
		if ( empty( $publisher ) ) {
			return new \WP_Error( 404, 'Not Found' );
		}

		$request = rest_ensure_request( $request );
		$authorization = $request->get_param( 'authorization' );

		if ( ! empty( $authorization ) ) {
			$path = sprintf(
				'experience/channels/%s/feeds/content/?authorization=%s',
				urlencode( $publisher ),
				urlencode( $authorization )
			);
		} else {
			$path = sprintf(
				'experience/channels/%s/feeds/content/',
				urlencode( $publisher )
			);
		}

		$response = $this->send_request( $path );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( wp_remote_retrieve_response_code( $response ) != 200 ) {
			return new \WP_Error( 401, 'Authorization failed' );
		}

		$response = wp_remote_retrieve_body( $response );
		$response = json_decode( $response, true );

		$data = apply_filters( 'ee_feeds_content_html', '', $response );

		return rest_ensure_response( [
			'status'    => 200,
			'html'      => $data,
		] );
	}

	public function log_prefix() {

		$site_id	=	get_current_blog_id();
		$site_name	=	get_blog_option( $site_id, 'blogname' );

		$result = "[EE Event SiteID:{$site_id} SiteName:$site_name] ";

		return $result;
	}

	public function clearCloudFlareHomeCache(){
		$zone_id = get_option('cloud_flare_zoneid');

        if(!$zone_id){
            return false;
        }

		$request_url = 'https://api.cloudflare.com/client/v4/zones/'.$zone_id.'/purge_cache';
		$data = [ "tags" => [$_SERVER['HTTP_HOST'].'-'.'home'] ];

		$response = wp_remote_post( $request_url, array(
				'method' => 'POST',
				'headers' => array(
						'Content-Type' => 'application/json',
						'Authorization' => 'Bearer _unAkz2VlqZXiW02gJq5FzrPc9QnH1nTtDkaGKny',
						),
						'body' => wp_json_encode( $data )
					)
				);

		$response_json = 'Cloudflare response 4: '. json_encode( $response );
		error_log( $response_json );

		if ( is_wp_error( $response ) ) {
			error_log( 'Cloudflare error notice query var from is_wp_error function 5' );
		}

	}

}
