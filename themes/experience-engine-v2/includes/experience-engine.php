<?php

add_filter( 'bbgiconfig', 'ee_update_api_bbgiconfig', 50 );
add_filter( 'ee_feeds_content_html', 'ee_homepage_feeds_content_html', 10, 2 );

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
			$publisher_info = \Bbgi\Module::get( 'experience-engine' )->get_publisher();
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
					$value = 'https://www.youtube.com/channel/' . rawurlencode( $value );
				}
				break;
			case 'twitch':
				if ( ! filter_var( $value, FILTER_VALIDATE_URL ) ) {
					$value = 'https://www.twitch.tv/' . rawurlencode( $value );
				}
				break;
		}

		return $value;
	}
endif;

if ( ! function_exists( 'ee_update_api_bbgiconfig' ) ) :
	function ee_update_api_bbgiconfig( $config ) {
		$publisher_id = get_option( 'ee_publisher' );

		$publisher = array();
		$publishers_map = array();
		$ee = \Bbgi\Module::get( 'experience-engine' );
		foreach ( $ee->get_publisher_list() as $item ) {
			$publishers_map[ $item['id'] ] = $item['title'];
			if ( $item['id'] == $publisher_id ) {
				$publisher = $item;
			}
		}

		$config['publisher'] = $publisher;
		$config['publishers'] = $publishers_map;
		$config['locations'] = array();// $ee->get_locations();
		$config['genres'] = array();// $ee->get_genres();

		$config['streams'] = array();
		$feeds = $ee->get_publisher_feeds_with_content();
		$channels = wp_list_filter( $feeds, array( 'type' => 'stream' ) );

		/**
		 * This code block retrieves and processes the second stream configuration for each channel, if enabled.
		 * It checks if the second stream is enabled (using the 'ad_second_stream_enabled' option) and if so, it retrieves the
		 * enabled days for the second stream (using the 'ss_enabled_days' option). It then loops through all channels and
		 * populates the $config['streams'] array with stream data. If the second stream is enabled and there is more than one
		 * channel, it also adds the enabled days and status of the second stream to the corresponding channel's stream data.
		 */
		$isSecondStreamOn = get_option('ad_second_stream_enabled');
		$enabledData = get_option('ss_enabled_days');
		$enabledData = (array)json_decode($enabledData);
		$i = 0;
		$secondStreamTime = [];
		foreach ( $channels as $channel ) {
			foreach ( $channel['content'] as $stream ) {
				$config['streams'][$i] = $stream;
			}
			if($isSecondStreamOn == 'off'){
				break;
			}
			if($isSecondStreamOn == 'on') {
				if ($i > 0) {
					foreach ($enabledData as $key => $val) {
						$secondStreamTime[$key] = $val;
					}
					$config['streams'][$i]['isSecondStreamOn'] = $isSecondStreamOn;
					$config['streams'][$i]['secondStreamTime'] = $secondStreamTime;
				}
			}
			$i++;
		}

		return $config;
	}
endif;

if ( ! function_exists( 'ee_homepage_feeds_content_html' ) ) :
	function ee_homepage_feeds_content_html( $html, $response ) {
		global $wp_query;

		// we need to make sure that WP thinks that this page is homepage
		$wp_query->is_home = true;

		// We don't want to render the Google Analytics placeholder component for homepage feeds
		// as the page view is already triggered on initial load.
		add_filter( 'bbgi_render_ga_placeholder', '__return_false' );

		ob_start();

		get_header();
		ee_homepage_feeds( $response );
		get_footer();

		return ob_get_clean();
	}
endif;
