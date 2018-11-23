<?php

add_action( 'wp_enqueue_scripts', 'ee_enqueue_dfp_scripts', 50 );
add_action( 'bbgi_register_settings', 'ee_register_dfp_settings', 10, 2 );
add_action( 'dfp_tag', 'ee_dfp_slot', 10, 3 );

add_filter( 'bbgiconfig', 'ee_update_dfp_bbgiconfig', 50 );

if ( ! function_exists( 'ee_register_dfp_settings' ) ) :
	function ee_register_dfp_settings( $group, $page ) {
		add_settings_section( 'beasley_dfp_settings', 'DoubleClick for Publishers', '__return_false', $page );
		add_settings_section( 'beasley_dfp_unit_codes', 'DFP Unit Codes', '__return_false', $page );

		register_setting( $group, 'dfp_network_code', 'sanitize_text_field' );
		add_settings_field( 'dfp_network_code', 'Network Code', 'bbgi_input_field', $page, 'beasley_dfp_settings', 'name=dfp_network_code' );

		$settings = array(
			'dfp_ad_leaderboard_pos1'  => 'Header Leaderboard',
			'dfp_ad_leaderboard_pos2'  => 'Footer Leaderboard',
			'dfp_ad_inlist_infinite'   => 'In List (infinite)',
			'dfp_ad_playersponsorship' => 'Player Sponsorship',
		);

		/**
		 * Filter dfp setting section titles.
		 *
		 */
		$settings = apply_filters( 'beasley-dfp-unit-codes-settings', $settings );

		foreach ( $settings as $key => $label ) {
			register_setting( $group, $key, 'trim' );
			add_settings_field( $key, $label, 'bbgi_input_field', $page, 'beasley_dfp_unit_codes', 'name=' . $key );
		}
	}
endif;

if ( ! function_exists( 'ee_update_dfp_bbgiconfig' ) ) :
	function ee_update_dfp_bbgiconfig( $config ) {
		$sizes = array(
			'dfp_ad_leaderboard_pos1'  => array( array( 728, 90 ), array( 970, 90 ), array( 970, 66 ), array( 320, 50 ), array( 320, 100 ) ),
			'dfp_ad_leaderboard_pos2'  => array( array( 728, 90 ), array( 970, 90 ), array( 320, 50 ), array( 320, 100 ) ),
			'dfp_ad_inlist_infinite'   => array( array( 300, 250 ) ),
			'dfp_ad_playersponsorship' => array( 'fluid' ),
		);

		$player = array(
			'network'  => get_option( 'dfp_network_code' ),
			'unitId'   => get_option( 'dfp_ad_playersponsorship' ),
			'unitName' => 'dfp_ad_playersponsorship',
		);

		$config['dfp'] = array(
			'global' => ee_get_dfp_global_targeting(),
			'sizes'  => $sizes,
			'player' => $player,
		);

		return $config;
	}
endif;

if ( ! function_exists( 'ee_get_dfp_global_targeting' ) ) :
	function ee_get_dfp_global_targeting() {
		static $targeting = null;

		if ( is_null( $targeting ) ) {
			$cpage = ! is_home() && ! is_front_page()
				? untrailingslashit( current( explode( '?', $_SERVER['REQUEST_URI'], 2 ) ) ) // strip query part and trailing slash of the current uri
				: 'home';

			$targeting = array(
				array( 'cdomain', parse_url( home_url( '/' ), PHP_URL_HOST ) ),
				array( 'cpage', $cpage ),
				array( 'genre', trim( strtolower( implode( ',', ee_get_publisher_information( 'genre' ) ) ) ) ),
				array( 'market', trim( strtolower( ee_get_publisher_information( 'location' ) ) ) ),
			);

			if ( is_singular() ) {
				$post_id = get_queried_object_id();
				$targeting[] = array( 'cpostid', "{$post_id}" );

				if ( class_exists( 'ShowsCPT' ) && defined( 'ShowsCPT::SHOW_TAXONOMY' ) ) {
					$terms = get_the_terms( $post_id, ShowsCPT::SHOW_TAXONOMY );
					if ( $terms && ! is_wp_error( $terms ) ) {
						$targeting[] = array( 'shows', implode( ",", wp_list_pluck( $terms, 'slug' ) ) );
					}
				}

				if ( class_exists( 'GMP_CPT' ) && defined( 'GMP_CPT::PODCAST_POST_TYPE' ) && defined( 'GMP_CPT::EPISODE_POST_TYPE' ) ) {
					$podcast = false;

					$post = get_post( $post_id );
					$post_type = get_post_type( $post );
					if ( GMP_CPT::PODCAST_POST_TYPE == $post_type ) {
						$podcast = $post->post_name;
					}

					if ( GMP_CPT::EPISODE_POST_TYPE == $post_type ) {
						$parent_podcast_id = wp_get_post_parent_id( $post );
						if ( $parent_podcast_id && ! is_wp_error( $parent_podcast_id ) ) {
							$parent_podcast = get_post( $parent_podcast_id );
							$podcast = $parent_podcast->post_name;
						}
					}

					if ( $podcast ) {
						$targeting[] = array( 'podcasts', $podcast );
					}
				}

				$categories = wp_get_post_categories( get_queried_object_id() );
				if ( ! empty( $categories ) ) {
					$categories = array_filter( array_map( 'get_category', $categories ) );
					$categories = wp_list_pluck( $categories, 'slug' );
					$targeting[] = array( 'categories', implode( ',', $categories ) );
				}
			} elseif ( is_category() ) {
				$category = get_queried_object();
				$targeting[] = array( 'categories', $category->slug );
			}
		}

		return $targeting;
	}
endif;

if ( ! function_exists( 'ee_enqueue_dfp_scripts' ) ) :
	function ee_enqueue_dfp_scripts() {
		$script = <<<EOL
var googletag = googletag || {};
googletag.cmd = googletag.cmd || [];

googletag.cmd.push(function() {
	googletag.pubads().enableSingleRequest();
	googletag.pubads().collapseEmptyDivs(true);

	for (var i = 0, pairs = bbgiconfig.dfp.global || []; i < pairs.length; i++) {
		googletag.pubads().setTargeting(pairs[i][0], pairs[i][1]);
	}

	googletag.enableServices();
});
EOL;

		wp_add_inline_script( 'googletag', $script, 'before' );
	}
endif;

if ( ! function_exists( 'ee_dfp_slot' ) ) :
	function ee_dfp_slot( $slot, $sizes = false, $targeting = array() ) {
		$network = get_option( 'dfp_network_code' );
		$unit_id = get_option( $slot );
		if ( empty( $network ) || empty( $unit_id ) ) {
			return;
		}

		$remnant_slots = array(
			'dfp_ad_leaderboard_pos1',
			'dfp_ad_leaderboard_pos2',
		);

		if ( ! is_array( $targeting ) ) {
			$targeting = array();
		}

		if ( in_array( $slot, $remnant_slots ) ) {
			$targeting[] = array( 'remnant', 'yes' );
		}

		printf(
			'<div class="dfp-slot" data-network="%s" data-unit-id="%s" data-unit-name="%s" data-targeting="%s"></div>',
			esc_attr( $network ),
			esc_attr( $unit_id ),
			esc_attr( $slot ),
			esc_attr( json_encode( $targeting ) )
		);
	}
endif;
