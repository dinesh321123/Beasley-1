<?php

add_filter( 'bbgiconfig', 'ee_update_bbgiconfig' );

if ( ! function_exists( 'ee_has_publisher_information' ) ) :
	function ee_has_publisher_information( $meta ) {
		$value = ee_get_publisher_information( $meta );
		return ! empty( $value );
	}
endif;

if ( ! function_exists( 'ee_get_publisher_information' ) ) :
	function ee_get_publisher_information( $meta ) {
		static $publisher_info = null;

		if ( is_null( $publisher_info ) ) {
			$publisher = get_option( 'ee_publisher' );
			if ( ! empty( $publisher ) ) {
				$publisher_info = bbgi_ee_get_publisher( $publisher );
			}
		}

		// temporarily return # for itunes_app and play_app
		if ( $meta == 'itunes_app' || $meta == 'play_app' ) {
			return '#';
		}

		if ( empty( $publisher_info ) || empty( $publisher_info[ $meta ] ) ) {
			return false;
		}

		$value = $publisher_info[ $meta ];

		switch ( $meta ) {
			case 'facebook':
				if ( ! filter_var( $value, FILTER_VALIDATE_URL ) ) {
					$value = 'https://www.facebook.com/' . rawurlencode( $value );
				}
				break;
			case 'twitter':
				if ( ! filter_var( $value, FILTER_VALIDATE_URL ) ) {
					$value = 'https://twitter.com/' . rawurlencode( ltrim( $value, '@' ) );
				}
				break;
			case 'instagram':
				if ( ! filter_var( $value, FILTER_VALIDATE_URL ) ) {
					$value = 'https://www.instagram.com/' . rawurlencode( ltrim( $value, '@' ) );
				}
				break;
			case 'youtube':
				if ( ! filter_var( $value, FILTER_VALIDATE_URL ) ) {
					$value = 'https://www.youtube.com/user/' . rawurlencode( $value );
				}
				break;
		}

		return $value;
	}
endif;

if ( ! function_exists( 'ee_update_bbgiconfig' ) ) :
	function ee_update_bbgiconfig( $config ) {
		$publishers = bbgi_ee_get_publisher_list();
		$publisher_id = get_option( 'ee_publisher' );

		$publishers_map = array();
		$current_publisher = array();
		foreach ( $publishers as $publisher ) {
			$publishers_map[ $publisher['id'] ] = $publisher['title'];
			if ( $publisher['id'] == $publisher_id ) {
				$current_publisher['phone'] = $publisher['phone'];
				$current_publisher['address'] = $publisher['address'];
				$current_publisher['email'] = $publisher['email'];
				$current_publisher['picture'] = $publisher['picture'];
			}
		}

		$config['publishers'] = $publishers_map;
		$config['locations'] = bbgi_ee_get_locations();
		$config['genres'] = bbgi_ee_get_genres();
		$config['publisher'] = $current_publisher;

		return $config;
	}
endif;

if ( ! function_exists( 'ee_homepage_feeds' ) ) :
	function ee_homepage_feeds() {
		foreach ( bbgi_ee_get_publisher_feeds_with_content() as $feed ) {
			// do nothing for the following feeds
			if ( ! empty( $feed['id'] ) && ( $feed['id'] == 'feedback' || $feed['id'] == 'utilities' ) ) {
				continue;
			}

			echo '<div class="ribon">';

				if ( ! empty( $feed['title'] ) ) {
					ee_the_subtitle( $feed['title'] );
					if ( ! empty( $feed['description'] ) ) {
						echo '<p>', esc_html( $feed['description'] ), '</p>';
					}
				}

				echo '<div class="ribon-items">';
					switch ( $feed['type'] ) {
						case 'news':
							foreach ( $feed['content'] as $item ) {
								if ( $item['contentType'] == 'link' ) {
									$post = new \stdClass();
									$post->ID = 0;
									$post->post_title = $item['title'];
									$post->post_status = 'publish';
									$post->post_type = 'post';
									$post->post_content = $item['excerpt'];
									$post->post_excerpt = $item['excerpt'];
									$post->post_date = $post->post_date_gmt = $post->post_modified = $post->post_modified_gmt = date( 'Y:m:d H:i:s', strtotime( $item['publishedAt'] ) );
									$post->link = $item['link'];
									$post->id = $item['id'];
									$post->filter = 'raw';
									if ( ! empty( $item['picture']['large'] ) ) {
										$post->picture = $item['picture']['large'];
									}

									$post = new \WP_Post( $post );
									setup_postdata( $post );
									$GLOBALS['post'] = $post;

									get_template_part( 'partials/tile', $post->post_type );
								}
							}
							break;
						default:
							break;
					}
				echo '</div>';
			echo '</div>';
		}

		wp_reset_postdata();
	}
endif;

if ( ! function_exists( 'bbgi_ee_request' ) ) :
	/**
	 * Wrapper for wp_remote_request
	 *
	 * This is a wrapper function for wp_remote_request.
	 *
	 *
	 * @param string $path Site URL to retrieve.
	 * @param array $args Optional. Request arguments. Default empty array.
	 *
	 * @return WP_Error|array The response or WP_Error on failure.
	 */
	function bbgi_ee_request( $path, $args = array() ) {
		$response = wp_cache_get( $path, 'experience_engine_api' );
		if ( empty( $response ) ) {
			if ( empty( $args['method'] ) ) {
				$args['method'] = 'GET';
			}

			//Add the API Header
			$args['headers'] = array(
				'Content-Type' => 'application/json',
			);

			$host = untrailingslashit( EE_API_HOST ) . '/v1/' . $path;
			$request = wp_remote_request( $host, $args );
			if ( is_wp_error( $request ) ) {
				return $request;
			}

			$request_response_code = (int) wp_remote_retrieve_response_code( $request );
			$is_valid_res = ( $request_response_code >= 200 && $request_response_code <= 299 );
			if ( false === $request || ! $is_valid_res ) {
				return $request;
			}

			$response   = json_decode( wp_remote_retrieve_body( $request ), true );
			$cache_time = bbgi_ee_get_request_cache_time( $request );
			if ( $cache_time ) {
				wp_cache_set( $path, $response, 'experience_engine_api', $cache_time );
			}
		}

		return $response;
	}
endif;

if ( ! function_exists( 'bbgi_ee_get_request_cache_time' ) ) :
	/**
	 * Get request cache time
	 *
	 * @param array $request HTTP response.
	 *
	 * @return int cache time.
	 */
	function bbgi_ee_get_request_cache_time( $request ) {
		$response_headers = wp_remote_retrieve_headers( $request );
		if ( empty( $response_headers['cache-control'] ) ) {
			return 0;
		}

		$cache_control = explode( ',', $response_headers['cache-control'] );
		$cache_time    = 0;
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

		return absint( $cache_time );
	}
endif;

if ( ! function_exists( 'bbgi_ee_get_publisher_list' ) ) :
	/**
	 * Get publisher list from BBGI Experience API.
	 *
	 * @return array Contains list of publishers.
	 */
	function bbgi_ee_get_publisher_list() {
		return bbgi_ee_request( 'publishers' );
	}
endif;


if ( ! function_exists( 'bbgi_ee_get_publisher' ) ) :
	/**
	 * Get a single publisher from BBGI Experience API.
	 *
	 * @return array Contains publisher data
	 */
	function bbgi_ee_get_publisher( $publisher = null ) {
		if ( empty( $publisher ) ) {
			$publisher = get_option( 'ee_publisher' );
		}

		$data = false;
		if ( $publisher ) {
			$data = bbgi_ee_request( "publishers/{$publisher}" );
			if ( is_array( $data ) && count( $data ) == 1 && is_array( $data[0] ) ) {
				$data = $data[0];
			}
		}

		return $data;
	}
endif;

if ( ! function_exists( 'bbgi_ee_get_publisher_feeds' ) ) :
	/**
	 * Get a single publisher's feeds from BBGI Experience API.
	 *
	 * @return array Contains publisher feeds.
	 */
	function bbgi_ee_get_publisher_feeds( $publisher = null ) {
		if ( empty( $publisher ) ) {
			$publisher = get_option( 'ee_publisher' );
		}

		return bbgi_ee_request( "publishers/{$publisher}/feeds/" );
	}
endif;

if ( ! function_exists( 'bbgi_ee_get_publisher_feeds_with_content' ) ) :
	function bbgi_ee_get_publisher_feeds_with_content( $publisher = null ) {
		if ( empty( $publisher ) ) {
			$publisher = get_option( 'ee_publisher' );
		}

		return bbgi_ee_request( "experience/channels/{$publisher}/feeds/content/" );
	}
endif;

if ( ! function_exists( 'bbgi_ee_get_publisher_feed' ) ) :
	/**
	 * Get a particular feed belonging to a publisher from BBGI Experience API.
	 *
	 * @return array Containing the publisher feed.
	 */
	function bbgi_ee_get_publisher_feed( $feed, $publisher = null ) {
		if ( empty( $publisher ) ) {
			$publisher = get_option( 'ee_publisher' );
		}

		return bbgi_ee_request( "publishers/{$publisher}/feeds/{$feed}" );
	}
endif;

if ( ! function_exists( 'bbgi_ee_get_locations' ) ) :
	/**
	 * Get list of all locations from BBGI Experience API.
	 *
	 * @return array Containing locations.
	 */
	function bbgi_ee_get_locations() {
		return bbgi_ee_request( 'locations' );
	}
endif;

if ( ! function_exists( 'bbgi_ee_get_genres' ) ) :
	/**
	 * Get list of all genres from BBGI Experience API.
	 *
	 * @return array Containing genres.
	 */
	function bbgi_ee_get_genres() {
		return bbgi_ee_request( 'genres' );
	}
endif;
