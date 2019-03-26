<?php

namespace Bbgi\Integration;

class ExperienceEngine extends \Bbgi\Module {

	private static $_fields = array(
		'ee_host' => 'API host',
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

	public function do_request( $path, $args = array() ) {
		$cache_index = get_option( 'ee_cache_index', 0 );
		$response = wp_cache_get( $path, "experience_engine_api-{$cache_index}" );
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
				wp_cache_set( $path, $response, "experience_engine_api-{$cache_index}", $cache_time );
			}
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
			if ( ! empty( $_REQUEST ) ) {
				$url .= '?authorization=' . urlencode( $_REQUEST['authorization'] );
			}

			$data = $this->do_request( $url );
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

		return $config;
	}

	public function init_rest_api() {
		$namespace = 'experience_engine/v1';

		register_rest_route( $namespace, '/purge-cache', array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => $this( 'rest_purge_cache' ),
		) );

		$authorization = array(
			'authorization' => array(
				'type'              => 'string',
				'required'          => true,
				'validate_callback' => function( $value ) {
					return strlen( $value ) > 0;
				},
			),
		);

		register_rest_route( $namespace, 'feeds-content', array(
			'methods'  => \WP_REST_Server::CREATABLE,
			'callback' => $this( 'rest_get_feeds_content' ),
			'args'     => array_merge( $authorization, array(
				'format' => array(
					'type'     => 'string',
					'required' => false,
				),
			) ),
		) );
	}

	public function rest_purge_cache() {
		update_option( 'ee_cache_index', time(), 'no' );
		return rest_ensure_response( 'Cache Flushed' );
	}

	public function rest_get_feeds_content( $request ) {
		$publisher = get_option( 'ee_publisher' );
		if ( empty( $publisher ) ) {
			return new \WP_Error( 404, 'Not Found' );
		}

		$request = rest_ensure_request( $request );
		$authorization = $request->get_param( 'authorization' );

		$path = sprintf(
			'experience/channels/%s/feeds/content/?authorization=%s',
			urlencode( $publisher ),
			urlencode( $authorization )
		);

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
		if ( $request->get_param( 'format' ) == 'raw' ) {
			// @todo: find a better way to send html data
			header( 'content-type: text/html' );
			echo $data;
			exit;
		}

		return rest_ensure_response( array(
			'html' => $data,
		) );
	}

}
