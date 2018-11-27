<?php

add_action( 'wp_enqueue_scripts', 'ee_enqueue_dfp_scripts', 50 );
add_action( 'bbgi_register_settings', 'ee_register_dfp_settings', 10, 2 );
add_action( 'dfp_tag', 'ee_dfp_slot', 10, 3 );
add_action( 'wp_footer', 'ee_display_dfp_outofpage', 100 );

add_filter( 'bbgiconfig', 'ee_update_dfp_bbgiconfig', 50 );

if ( ! function_exists( 'ee_register_dfp_settings' ) ) :
	function ee_register_dfp_settings( $group, $page ) {
		add_settings_section( 'beasley_dfp_settings', 'DoubleClick for Publishers', '__return_false', $page );
		add_settings_section( 'beasley_dfp_unit_codes', 'DFP Unit Codes', '__return_false', $page );

		register_setting( $group, 'dfp_network_code', 'sanitize_text_field' );
		add_settings_field( 'dfp_network_code', 'Network Code', 'bbgi_input_field', $page, 'beasley_dfp_settings', 'name=dfp_network_code' );

		$settings = apply_filters( 'beasley-dfp-unit-codes-settings', array(
			'dfp_ad_leaderboard_pos1'  => 'Header Leaderboard',
			'dfp_ad_leaderboard_pos2'  => 'Footer Leaderboard',
			'dfp_ad_incontent_pos1'    => 'In Content (pos1)',
			'dfp_ad_incontent_pos2'    => 'In Content (pos2)',
			'dfp_ad_right_rail_pos1'   => 'Right Rail',
			'dfp_ad_inlist_infinite'   => 'In List (infinite)',
			'dfp_ad_interstitial'      => 'Out-of-Page',
			'dfp_ad_playersponsorship' => 'Player Sponsorship',
		) );

		foreach ( $settings as $key => $label ) {
			register_setting( $group, $key, 'trim' );
			add_settings_field( $key, $label, 'bbgi_input_field', $page, 'beasley_dfp_unit_codes', 'name=' . $key );
		}
	}
endif;

if ( ! function_exists( 'ee_update_dfp_bbgiconfig' ) ) :
	function ee_update_dfp_bbgiconfig( $config ) {
		$fluid = array( 'fluid' );
		$advanced = array( array( 970, 250 ), array( 970, 90 ), array( 728, 90 ), array( 320, 100 ), array( 320, 50 ) );
		$advanced_with_fluid = array_merge( $fluid, $advanced );

		$sizes = array(
			'dfp_ad_leaderboard_pos1'  => $advanced_with_fluid,
			'dfp_ad_leaderboard_pos2'  => $advanced,
			'dfp_ad_inlist_infinite'   => $advanced_with_fluid,
			'dfp_ad_playersponsorship' => $fluid,
			'dfp_ad_right_rail_pos1'   => array( array( 300, 600 ), array( 300, 250 ) ),
			'dfp_ad_incontent_pos1'    => array( array( 1, 1 ), array( 300, 250 ) ),
			'dfp_ad_incontent_pos2'    => array( array( 300, 250 ) ),
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

			$genre = ee_get_publisher_information( 'genre' );
			$genre = is_array( $genre ) ? trim( strtolower( implode( ',', $genre ) ) ) : '';

			$market = ee_get_publisher_information( 'location' );
			$market = is_string( $market ) ? trim( strtolower( $market ) ) : '';

			$targeting = array(
				array( 'cdomain', parse_url( home_url( '/' ), PHP_URL_HOST ) ),
				array( 'cpage', $cpage ),
				array( 'genre', $genre ),
				array( 'market', $market ),
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
		$network_id = trim( get_option( 'dfp_network_code' ) );
		if ( empty( $network_id ) ) {
			return;
		}

		$size_mapping = 'googletag.sizeMapping().addSize([970, 200], [[1, 1]]).addSize([0, 0], []).build()';

		$dfp_ad_interstitial = trim( get_option( 'dfp_ad_interstitial' ) );
		if ( ! empty( $dfp_ad_interstitial ) ) {
			$dfp_ad_interstitial = sprintf(
				"googletag.defineOutOfPageSlot('/%s/%s', 'div-gpt-ad-1484200509775-3').defineSizeMapping(%s).addService(googletag.pubads());",
				esc_js( $network_id ),
				esc_js( $dfp_ad_interstitial ),
				esc_js( $size_mapping )
			);
		}

		$script = <<<EOL
var googletag = googletag || {};
googletag.cmd = googletag.cmd || [];

googletag.cmd.push(function() {
	{$dfp_ad_interstitial}

	googletag.pubads().collapseEmptyDivs(true);

	if (window.bbgiconfig && window.bbgiconfig.dfp) {
		for (var i = 0, pairs = window.bbgiconfig.dfp.global || []; i < pairs.length; i++) {
			googletag.pubads().setTargeting(pairs[i][0], pairs[i][1]);
		}
	}

	googletag.enableServices();
});
EOL;

		wp_add_inline_script( 'googletag', $script, 'before' );
	}
endif;

if ( ! function_exists( 'ee_dfp_slot' ) ) :
	function ee_dfp_slot( $slot, $deprecated = false, $targeting = array(), $echo = true ) {
		$network = get_option( 'dfp_network_code' );
		$unit_id = get_option( $slot );
		if ( empty( $network ) || empty( $unit_id ) ) {
			return;
		}

		$remnant_slots = array(
			'dfp_ad_leaderboard_pos1',
			'dfp_ad_leaderboard_pos2',
			'dfp_ad_inlist_infinite',
			'dfp_ad_right_rail_pos1',
			'dfp_ad_incontent_pos1',
			'dfp_ad_incontent_pos2',
		);

		if ( ! is_array( $targeting ) ) {
			$targeting = array();
		}

		if ( in_array( $slot, $remnant_slots ) ) {
			$targeting[] = array( 'remnant', 'yes' );
		}

		$html = sprintf(
			'<div class="dfp-slot" data-network="%s" data-unit-id="%s" data-unit-name="%s" data-targeting="%s"></div>',
			esc_attr( $network ),
			esc_attr( $unit_id ),
			esc_attr( $slot ),
			esc_attr( json_encode( $targeting ) )
		);

		if ( $echo ) {
			echo $html;
		}

		return $html;
	}
endif;

if ( ! function_exists( 'ee_display_dfp_outofpage' ) ) :
	function ee_display_dfp_outofpage() {
		$network_id = trim( get_option( 'dfp_network_code' ) );
		if ( empty( $network_id ) ) {
			return;
		}

		$dfp_ad_interstitial = get_option( 'dfp_ad_interstitial' );
		if ( ! empty( $dfp_ad_interstitial ) ) :
			?><div id="div-gpt-ad-1484200509775-3" style="height:0;overflow:hidden;width:0;">
				<script type="text/javascript">googletag.cmd.push(function() { googletag.display('div-gpt-ad-1484200509775-3'); });</script>
			</div><?php
		endif;
	}
endif;

if ( ! function_exists( 'ee_the_content_with_ads' ) ) :
	function ee_the_content_with_ads() {
		$content = apply_filters( 'the_content', get_the_content() );
		$content = str_replace( ']]>', ']]&gt;', $content );

		$parts = explode( '</p>', $content );
		$new_content = '';

		$len = count( $parts );
		for ( $i = 1; $i <= $len; $i++ ) {
			$new_content .= $parts[ $i - 1 ] . '</p>';

			if ( 2 == $i ) {
				// in-content pos1 slot after first 2 paragraphs
				$new_content .= ee_dfp_slot( 'dfp_ad_incontent_pos1', false, array(), false );
			} elseif ( 0 == ( $i - 2 ) % 4 && $len > 6 ) {
				// in-content pos2 slot after 4th paragraphs if we have more than 6 paragraphs on the page
				$new_content .= ee_dfp_slot( 'dfp_ad_incontent_pos2', false, array(), false );
			}
		}

		if ( $len < 2 ) {
			$new_content .= ee_dfp_slot( 'dfp_ad_incontent_pos1', false, array(), false );
		}

		echo $new_content;
	}
endif;
