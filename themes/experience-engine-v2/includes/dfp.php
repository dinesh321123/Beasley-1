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
		$advanced = array( array( 970, 250 ), array( 970, 90 ), array( 728, 90 ), array( 300, 250 ), array( 320, 100 ), array( 320, 50 ) );
		$advanced_with_fluid = array_merge( $fluid, $advanced );

		$sizes = array(
			'top-leaderboard'    => $advanced,
			'bottom-leaderboard' => $advanced,
			'in-list'            => $advanced,
			'in-list-gallery'    => array( array( 1, 1 ), array( 300, 250 ) ),
			'player-sponsorship' => $fluid,
			'right-rail'         => array( array( 300, 600 ), array( 300, 250 ) ),
			'in-content'         => array( array( 1, 1 ), array( 300, 250 ) ),
			'countdown'          => array( array( 320, 50 ) ),
			'adhesion'           => array( array( 970, 90 ), array( 728, 90 ) ),
			'drop-down'          => array( array( 320, 50 ) ),
		);

		$headerad = array(
				'unitId'   => $ee->get_ad_slot_unit_id( 'top-leaderboard' ),
				'unitName' => 'top-leaderboard',
		);

		$adhesionad = array(
			'unitId'   => $ee->get_ad_slot_unit_id( 'adhesion' ),
			'unitName' => 'adhesion',
		);

		$dropdown = array(
			'unitId' 	=> $ee->get_ad_slot_unit_id( 'drop-down' ),
			'unitName' 	=> 'drop-down',
		);


		$incontentpreroll = array(
			'unitId'   => $ee->get_ad_slot_unit_id( 'in-content-preroll' ),
			'unitName' => 'in-content-preroll',
		);

		$tunerpreroll = array(
			'unitId'   => $ee->get_ad_slot_unit_id( 'tuner-preroll' ),
			'unitName' => 'tuner-preroll',
		);

		$countdown = array(
			'unitId'   => $ee->get_ad_slot_unit_id( 'countdown' ),
			'unitName' => 'countdown',
		);

		$config['dfp'] = array(
			'global'    => \Bbgi\Integration\Dfp::get_global_targeting(),
			'sizes'     => $sizes,
			'headerad'  => $headerad,
			'adhesionad'  => $adhesionad,
			'incontentpreroll' => $incontentpreroll,
			'tunerpreroll' => $tunerpreroll,
			'countdown' => $countdown,
			'dropdown' => $dropdown,
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

		$dfp_ad_lazy_loading = get_option( 'ad_lazy_loading_enabled', 'off' );
		if  ( $dfp_ad_lazy_loading === 'on' ) {
        	$dfp_ad_lazy_loading = "		googletag.pubads().enableLazyLoad({
        		fetchMarginPercent: 0,
				renderMarginPercent: 0,
				mobileScaling: 0.0,
			});
			ad_lazy_loading_enabled = true; // Explicit flag for indicating whether Lazy Load is enabled
			console.log('Ad Lazy Loading ENABLED');
			";
        } else {
        	$dfp_ad_lazy_loading = "
        		ad_lazy_loading_enabled = false; // Explicit flag for indicating whether Lazy Load is enabled
        		console.log('Ad Lazy Loading DISABLED');
			";
        }

        $dfp_ad_single_request = get_option( 'ad_single_request_enabled', 'on' );
        if  ( $dfp_ad_single_request === 'on' ) {
			$dfp_ad_single_request = "
				// googletag.pubads().enableSingleRequest();
				// console.log('Ad Single Request ENABLED');
			";
        } else {
        	$dfp_ad_single_request = "
				console.log('Ad Single Request DISABLED');
			";
        }

		$script = <<<EOL
var googletag = googletag || {};
googletag.cmd = googletag.cmd || [];
dfp_needs_refresh = true; // Explicit flag for controlling initial dfp refresh.

googletag.cmd.push(function() {
	googletag.pubads().collapseEmptyDivs(true);

	if (window.bbgiconfig && window.bbgiconfig.dfp) {
		for (var i = 0, pairs = window.bbgiconfig.dfp.global || []; i < pairs.length; i++) {
			googletag.pubads().setTargeting(pairs[i][0], pairs[i][1]);
		}
	}

	{$dfp_ad_lazy_loading}

	// googletag.pubads().disableInitialLoad(); // MFP 09/17/2020 - display() will only register the ad slot. No ad content will be loaded until a second action is taken. We will send a refresh() after all slots are defined.
	{$dfp_ad_single_request}
	googletag.enableServices();

	// MFP 09/17/2020 - Slot Configuration Should Be Done After enableServices()
	{$dfp_ad_interstitial}
});
EOL;

		wp_add_inline_script( 'googletag', $script, 'before' );
	}
endif;

if ( ! function_exists( 'ee_dfp_slot' ) ) :
	function ee_dfp_slot( $slot, $deprecated = false, $targeting = array(), $echo = true ) {
		$unit_id = \Bbgi\Module::get( 'experience-engine' )->get_ad_slot_unit_id( $slot );

		/* if no in-list-gallery config fallback to in-list */
		if ( empty( $unit_id ) && $slot === 'in-list-gallery' ) {
			$unit_id = \Bbgi\Module::get( 'experience-engine' )->get_ad_slot_unit_id( 'in-list' );
		}

		if ( empty( $unit_id ) ) {
			return;
		}

		if ( ! is_array( $targeting ) ) {
			$targeting = array();
		}

		$remnant_slots = array( 'top-leaderboard', 'bottom-leaderboard', 'in-list', 'in-list-gallery', 'right-rail', 'in-content' );
		if ( in_array( $slot, $remnant_slots ) ) {
			$targeting[] = array( 'remnant', 'yes' );
		}

		$targeting = apply_filters( 'dfp_single_targeting', $targeting, $slot );

		// When not jacapps or whiz, render react ready attributes
		if ( ! ee_is_common_mobile() ) {
			$html = sprintf(
				'<div class="dfp-slot" data-unit-id="%s" data-unit-name="%s" data-targeting="%s" ></div>',
				esc_attr( $unit_id ),
				esc_attr( $slot ),
				esc_attr( json_encode( $targeting ) )
			);
		}

		// When is jacapps or whiz, render standard div and script for display
		// We do this since the ad units are currently embedded in the react app
		// So fallback for jacapps or whiz is to add the script inline for display adds
		// along with an alternative DFP slot
		if ( ee_is_common_mobile() ) {

			$uuid = $slot . '_' . wp_generate_uuid4();

			$html = '<script>
				window.googletag = window.googletag || { cmd: [] };

				googletag.cmd.push( function() {

					var jacappsAdSizes = [[320,100], [320,50]];

					var jacappsMapping = googletag.sizeMapping()
						.addSize( [0, 0], jacappsAdSizes ) //other
						.build();

					googletag.defineSlot("' . esc_attr( $unit_id ) . '", jacappsAdSizes, "' . esc_attr( $uuid ) . '")
						.defineSizeMapping( jacappsMapping )
						.addService( googletag.pubads() )';

						forEach( $targeting as $value ) {
							$html .= '.setTargeting("' . $value[ 0 ] . '", "' . $value[ 1 ] . '")';
						}

					$html .= ';
					';

					$dfp_ad_single_request = get_option( 'ad_single_request_enabled', 'on' );
					if  ( $dfp_ad_single_request === 'on' ) {
						$html .= 'googletag.pubads().enableSingleRequest(); ';
					}

					$html .= ' googletag.enableServices();
				} );
			</script>';

			$html .= sprintf(
				'<div class="dfp-slot" id="%s">
					<script>
						googletag.cmd.push( function() {
							googletag.display("' . esc_attr( $uuid ) . '");
						} );
					</script>
				</div>',
				esc_attr( $uuid )
			);
		}

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
				<script type="text/javascript">
					var googletag = googletag || {};
					googletag.cmd = googletag.cmd || [];
					googletag.cmd.push(function() { googletag.display('div-gpt-ad-1484200509775-3'); });
				</script>
			</div><?php
		endif;
	}
endif;

/**
 * Adds STN and GAM ads to content. Default behavior is to include GAM ads, but can be supressed by passing false to $renderdfp
 *
 * @param $content
 * @param $renderdfp Optional. by default this is true, but if false will suppress gam ads
 * @return string
 */
function ee_inject_ads($content, $renderdfp = true)
{
	$parts = explode('</p>', $content);
	$new_content = '';

	$stn_video_paragraph_position = intval(get_option('stn_position', '2'));

	$len = count($parts);
	for ($i = 1; $i <= $len; $i++) {
		$new_content .= $parts[$i - 1] . '</p>';

		if ($i == 1 && $stn_video_paragraph_position == 0) {
			$snt_video = ee_category_exists() ? apply_filters('incontentvideo_filter', '') : '';
			$new_content = $snt_video . $new_content;
		} elseif ($stn_video_paragraph_position == $i) {
			// in-content pos1 slot after first 2 paragraphs
			$new_content .= ee_category_exists() ? apply_filters('incontentvideo_filter', '') : '';
		} elseif (0 == ($i - $stn_video_paragraph_position) % 4 && $len > 4 && $renderdfp) {
			// in-content pos2 slot after 4th paragraphs if we have more than 4 paragraphs on the page
			$new_content .= ee_dfp_slot('in-content', false, array(), false);
		}
	}

	if ($len < 2 && $renderdfp) {
		$new_content .= ee_dfp_slot('in-content', false, array(), false);
	}

	return $new_content;
}


if ( ! function_exists( 'ee_add_ads_to_content' ) ) :
	function ee_add_ads_to_content( $content ) {
		return ee_inject_ads($content);
	}
endif;

if ( ! function_exists( 'ee_add_stn_to_content' ) ) :
	function ee_add_stn_to_content( $content ) {
		return ee_inject_ads($content, false);
	}
endif;

if ( ! function_exists( 'ee_the_content_with_ads' ) ) :
	function ee_the_content_with_ads() {
		add_filter( 'the_content', 'ee_add_ads_to_content', 100 );
		the_content();
		remove_filter( 'the_content', 'ee_add_ads_to_content', 100 );
	}
endif;

if ( ! function_exists( 'ee_the_content_with_stn_only' ) ) :
	function ee_the_content_with_stn_only() {
		add_filter( 'the_content', 'ee_add_stn_to_content', 100 );
		the_content();
		remove_filter( 'the_content', 'ee_add_stn_to_content', 100 );
	}
endif;

