<?php

add_action( 'dfp_tag', 'ee_dfp_slot', 10, 3 );
add_action( 'wp_enqueue_scripts', 'ee_enqueue_dfp_scripts', 50 );
add_action( 'wp_footer', 'ee_display_dfp_outofpage', 100 );

add_filter( 'bbgiconfig', 'ee_update_dfp_bbgiconfig', 50 );
add_filter( 'dfp_global_targeting', 'ee_update_dfp_global_targeting' );
add_filter( 'livestream_ad_tag_iu', 'ee_update_livestream_ad_tag_iu' );

if ( ! function_exists( 'ee_update_dfp_bbgiconfig' ) ) :
	function ee_update_dfp_bbgiconfig( $config ) {
		$ee = \Bbgi\Module::get( 'experience-engine' );

		$fluid = array( 'fluid' );
		$advanced = array( array( 970, 250 ), array( 970, 90 ), array( 728, 90 ), array( 320, 100 ), array( 320, 50 ) );
		$advanced_with_fluid = array_merge( $fluid, $advanced );

		$sizes = array(
			'top-leaderboard'    => $advanced_with_fluid,
			'bottom-leaderboard' => $advanced,
			'in-list'            => $advanced_with_fluid,
			'player-sponsorship' => $fluid,
			'right-rail'         => array( array( 300, 600 ), array( 300, 250 ) ),
			'in-content'         => array( array( 1, 1 ), array( 300, 250 ) ),
			'countdown'          => array( array( 320, 50 ) ),
		);

		$player = array(
			'unitId'   => $ee->get_ad_slot_unit_id( 'player-sponsorship' ),
			'unitName' => 'player-sponsorship',
		);

		$countdown = array(
			'unitId'   => $ee->get_ad_slot_unit_id( 'countdown' ),
			'unitName' => 'countdown',
		);

		$config['dfp'] = array(
			'global'    => \Bbgi\Integration\Dfp::get_global_targeting(),
			'sizes'     => $sizes,
			'player'    => $player,
			'countdown' => $countdown,
		);

		return $config;
	}
endif;

if ( ! function_exists( 'ee_update_dfp_global_targeting' ) ) :
	function ee_update_dfp_global_targeting( $targeting ) {
		foreach ( $targeting as $index => $keyvalue ) {
			switch ( $keyvalue[0] ) {
				case 'genre':
					$genre = ee_get_publisher_information( 'genre' );
					$genre = is_array( $genre ) ? trim( strtolower( implode( ',', $genre ) ) ) : '';
					$targeting[ $index ][1] = $genre;
					break;
				case 'market':
					$market = ee_get_publisher_information( 'location' );
					$market = is_string( $market ) ? trim( strtolower( $market ) ) : '';
					$targeting[ $index ][1] = $market;
					break;
			}
		}

		return $targeting;
	}
endif;

if ( ! function_exists( 'ee_update_livestream_ad_tag_iu' ) ) :
	function ee_update_livestream_ad_tag_iu( $iu ) {
		$dfp_livestream_uid = \Bbgi\Module::get( 'experience-engine' )->get_ad_slot_unit_id( 'video-preroll' );
		if ( ! empty( $dfp_livestream_uid ) ) {
			$iu = $dfp_livestream_uid;
		}

		return $iu;
	}
endif;

if ( ! function_exists( 'ee_enqueue_dfp_scripts' ) ) :
	function ee_enqueue_dfp_scripts() {
		$size_mapping = 'googletag.sizeMapping().addSize([970, 200], [[1, 1]]).addSize([0, 0], []).build()';

		$dfp_ad_interstitial = \Bbgi\Module::get( 'experience-engine' )->get_ad_slot_unit_id( 'interstitial' );
		if ( ! empty( $dfp_ad_interstitial ) ) {
			$dfp_ad_interstitial = sprintf(
				"googletag.defineOutOfPageSlot('%s', 'div-gpt-ad-1484200509775-3').defineSizeMapping(%s).addService(googletag.pubads());",
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
		$unit_id = \Bbgi\Module::get( 'experience-engine' )->get_ad_slot_unit_id( $slot );
		if ( empty( $unit_id ) ) {
			return;
		}

		if ( ! is_array( $targeting ) ) {
			$targeting = array();
		}

		$remnant_slots = array( 'top-leaderboard', 'bottom-leaderboard', 'in-list', 'right-rail', 'in-content' );
		if ( in_array( $slot, $remnant_slots ) ) {
			$targeting[] = array( 'remnant', 'yes' );
		}

		$targeting = apply_filters( 'dfp_single_targeting', $targeting, $slot );
		$html = sprintf(
			'<div class="dfp-slot" data-unit-id="%s" data-unit-name="%s" data-targeting="%s"></div>',
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
		$dfp_ad_interstitial = \Bbgi\Module::get( 'experience-engine' )->get_ad_slot_unit_id( 'interstitial' );
		if ( ! empty( $dfp_ad_interstitial ) ) :
			?><div id="div-gpt-ad-1484200509775-3" style="height:0;overflow:hidden;width:0;">
				<script type="text/javascript">googletag.cmd.push(function() { googletag.display('div-gpt-ad-1484200509775-3'); });</script>
			</div><?php
		endif;
	}
endif;

if ( ! function_exists( 'ee_add_ads_to_content' ) ) :
	function ee_add_ads_to_content( $content ) {
		$parts = explode( '</p>', $content );
		$new_content = '';

		$len = count( $parts );
		for ( $i = 1; $i <= $len; $i++ ) {
			$new_content .= $parts[ $i - 1 ] . '</p>';

			if ( 2 == $i ) {
				// in-content pos1 slot after first 2 paragraphs
				$new_content .= ee_dfp_slot( 'in-content', false, array(), false );
			} elseif ( 0 == ( $i - 2 ) % 4 && $len > 6 ) {
				// in-content pos2 slot after 4th paragraphs if we have more than 6 paragraphs on the page
				$new_content .= ee_dfp_slot( 'in-content', false, array(), false );
			}
		}

		if ( $len < 2 ) {
			$new_content .= ee_dfp_slot( 'in-content', false, array(), false );
		}

		return $new_content;
	}
endif;

if ( ! function_exists( 'ee_the_content_with_ads' ) ) :
	function ee_the_content_with_ads() {
		add_filter( 'the_content', 'ee_add_ads_to_content', 100 );
		the_content();
		remove_filter( 'the_content', 'ee_add_ads_to_content', 100 );
	}
endif;
