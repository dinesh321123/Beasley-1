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
					$value = 'https://www.youtube.com/user/' . rawurlencode( $value );
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
		foreach ( $channels as $channel ) {
			foreach ( $channel['content'] as $stream ) {
				$config['streams'][] = $stream;
			}
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
