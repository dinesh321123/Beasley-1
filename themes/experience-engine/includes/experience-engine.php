<?php

add_filter( 'bbgiconfig', 'ee_update_api_bbgiconfig', 50 );

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

if ( ! function_exists( 'ee_update_api_bbgiconfig' ) ) :
	function ee_update_api_bbgiconfig( $config ) {
		$publishers_map = array();
		$ee = \Bbgi\Module::get( 'experience-engine' );
		foreach ( $ee->get_publisher_list() as $publisher ) {
			$publishers_map[ $publisher['id'] ] = $publisher['title'];
		}

		$config['publishers'] = $publishers_map;
		$config['locations'] = $ee->get_locations();
		$config['genres'] = $ee->get_genres();

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
