<?php

add_action( 'wp_loaded', 'ee_setup_whiz' );

if ( ! function_exists( 'ee_is_whiz' ) ) :
	function ee_is_whiz() {
		return ee_is_common_mobile();
	}
endif;

if ( ! function_exists( 'ee_is_hidefeature' ) ) :
	function ee_is_hidefeature() {
		static $hidefeature_pos = null;

		if($hidefeature_pos === null ) {
			// Allow way to toggle hidefeature through URL querystring
			if ( isset( $_GET['hidefeature'] ) ) {
				$hidefeature_pos = true;
			}
		}

		return true === $hidefeature_pos;
	}
endif;

if ( ! function_exists( 'ee_setup_whiz' ) ) :
	function ee_setup_whiz() {
		if ( ! ee_is_whiz() ) {
			return;
		}

		add_action( 'wp_print_scripts', 'ee_whiz_enqueue_scripts', 99 );

		add_filter( 'body_class', 'ee_whiz_body_class' );
		add_filter( 'omny_embed_key', 'ee_update_whiz_omny_key' );
		add_filter( 'secondstreet_embed_html', 'ee_update_whiz_secondstreet_html', 10, 2 );
		add_filter( 'audience_embed_html', 'ee_update_whiz_audience_embed_html', 10, 2 );
		add_filter( 'secondstreetpref_html', 'ee_update_whiz_secondstreetpref_html', 10, 2 );
		add_filter( 'secondstreetsignup_html', 'ee_update_whiz_secondstreetsignup_html', 10, 2 );
		add_filter( 'mapbox_html', 'ee_update_whiz_mapbox_html', 10, 2 );
		add_filter( 'hubspotform_html', 'ee_update_whiz_hubspotform_html', 10, 2 );
		add_filter( 'dml-branded_html', 'ee_update_whiz_dml_branded_content', 10, 2);
		add_filter( 'drimify_html', 'ee_update_whiz_drimify_html', 10, 2 );

		remove_filter( 'omny_embed_html', 'ee_update_omny_embed' );
	}
endif;

if ( function_exists( 'vary_cache_on_function' ) ) :
	// batcache variant
	vary_cache_on_function( 'return (bool) preg_match("/whiz/i", $_SERVER["HTTP_USER_AGENT"]);' );
endif;

if ( ! function_exists( 'ee_whiz_body_class' ) ) :
	function ee_whiz_body_class( $classes ) {
		$classes[] = 'whiz';
		return $classes;
	}
endif;

if ( ! function_exists( 'ee_whiz_enqueue_scripts' ) ) :
	function ee_whiz_enqueue_scripts() {

/**
 * Application script
 * whiz needs the overarching config now that ads
 * will be initialized. There are specific globals that
 * we need access to.
 */
$bbgiconfig = <<<EOL
window.bbgiconfig = {};
try {
	window.bbgiconfig = JSON.parse( document.getElementById( 'bbgiconfig' ).innerHTML );
} catch( err ) {
	// do nothing
}

function scrollToSegmentation(type, item, heading_item = null) {
	var gotoID = null;
	if(item) {
		gotoID = document.getElementById(jQuery.trim(type) + '-segment-item-' + item);
	}
	if(heading_item) {
		gotoID = document.getElementById(jQuery.trim(type) + '-segment-header-item-' + heading_item);
	}
	if(gotoID) {
		gotoID.scrollIntoView({
			block: "start",
			behavior: "smooth",
		});
	}
}

	if (window.loadVimeoPlayers) {
		try {
			if(document.readyState === 'ready' || document.readyState === 'complete') {
				window.loadVimeoPlayers();
			} else {
				const handleSendPageEventFunc = () => {
					if (document.readyState === 'ready' || document.readyState === 'complete') {
						window.loadVimeoPlayers();
						document.removeEventListener('readystatechange', handleSendPageEventFunc);
					}
				};
				document.addEventListener('readystatechange', handleSendPageEventFunc);
			}
		} catch (err) {
			console.log("Error while initializing Vimeo Prerolls ", err.message);
		}
	} else {
		console.log("Vimeo Players NOT configured for prerolls");
	}

EOL;

		wp_dequeue_script( 'ee-app' );
		wp_enqueue_script( 'iframe-resizer' );
		wp_enqueue_script( 'embedly-player.js' );
		wp_enqueue_script( 'branded-content-scripts' );

		// Need googletag for ads in whiz
		wp_enqueue_script( 'googletag' );
		wp_script_add_data( 'googletag', 'async', true );
		wp_add_inline_script( 'googletag', $bbgiconfig, 'before' );
		wp_enqueue_script( 'wp-embed', '', [], false, true );

	}
endif;

if ( ! function_exists( 'ee_update_whiz_omny_key' ) ) :
	function ee_update_whiz_omny_key( $key ) {
		return $key . ':whiz';
	}
endif;

if ( ! function_exists( 'ee_update_whiz_secondstreet_html' ) ) :
	function ee_update_whiz_secondstreet_html( $embed, $atts ) {
		$url = 'https://embed-' . rawurlencode( $atts['op_id'] ) . '.secondstreetapp.com/Scripts/dist/embed.js';
		return '<script src="' . esc_url( $url ) . '" data-ss-embed="promotion" data-opguid="' . esc_attr( $atts['op_guid'] ) . '" data-routing="' . esc_attr( $atts['routing'] ) . '"></script>';
	}
endif;

if ( ! function_exists( 'ee_update_whiz_dml_branded_content' ) ) :
	function ee_update_whiz_dml_branded_content( $embed, $atts ) {

		$html = '';

		if ($atts['layout']) {
			$html = '<div data-stackid="' . esc_attr( $atts['stackid'] ) . '" data-layout="' . esc_attr( $atts['layout'] ) . '" class="dml-widget-container"></div>';
		} else {
			$html = '<div data-stackid="' . esc_attr( $atts['stackid'] ) . '" class="dml-widget-container"></div>';
		}

		return $html;
	}
endif;

if ( ! function_exists( 'ee_update_whiz_secondstreetpref_html' ) ) :
	function ee_update_whiz_secondstreetpref_html( $embed, $atts ) {
		$url = 'https://embed.secondstreetapp.com/Scripts/dist/preferences.js';
		return '<script src="' . esc_url( $url ) . '" data-ss-embed="preferences" data-organization-id="' . esc_attr( $atts['organization_id'] ) . '"></script>';
	}
endif;

if ( ! function_exists( 'ee_update_whiz_secondstreetsignup_html' ) ) :
	function ee_update_whiz_secondstreetsignup_html( $embed, $atts ) {
		$url = 'https://embed.secondstreetapp.com/Scripts/dist/optin.js';
		return '<script src="' . esc_url( $url ) . '" data-ss-embed="optin" data-design-id="' . esc_attr( $atts['design_id'] ) . '"></script>';
	}
endif;

if ( ! function_exists( 'ee_update_whiz_audience_embed_html' ) ) :
	function ee_update_whiz_audience_embed_html( $embed, $atts ) {

		$audiencescript = '<script async src="https://campaign.aptivada.com/sdk.js"></script>';
		$aptivadadiv = '<div class="aptivada-campaign"></div>';
		$implementation = sprintf('<script>
					window.AptivadaAsyncInit = function() {
						var sdk = window.Aptivada.init({
							campaignId: %s,
							campaignType: \'%s\'
						});
                    }
			</script>', $atts['widget-id'], $atts['widget-type']);

		return $audiencescript . $aptivadadiv . $implementation;
	}
endif;



if ( ! function_exists( 'ee_update_whiz_mapbox_html' ) ) :
	function ee_update_whiz_mapbox_html( $embed, $atts ) {
		$style = '<style>#mapboxdiv { width: 100%; height: 400px }</style>';
		$mapboxscript = '<script id="mapscript" async defer src="https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.js"></script>';
		$mapboxstyle = '<link href="https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.css" rel="stylesheet" />';
		$mapboxdiv = '<div id="mapboxdiv"></div>';
		$implementation = sprintf(
			'<script>
					document.getElementById(\'mapscript\').onload = function() {
					    mapboxgl.accessToken = \'%s\'

						var map = new mapboxgl.Map({
						container: \'mapboxdiv\',
						style: \'%s\',
						center: [ %s, %s ],
						zoom: %s
						});
					}

			       	</script>',
					esc_attr( $atts['accesstoken']),
					esc_attr( $atts['style']),
					esc_attr( $atts['lat']),
					esc_attr( $atts['long']),
					esc_attr( $atts['zoom'])
		);

		return $style . $mapboxscript . $mapboxstyle . $mapboxdiv . $implementation;
	}
endif;

if ( ! function_exists( 'ee_update_whiz_drimify_html' ) ) :
	function ee_update_whiz_drimify_html( $embed, $atts ) {
		$drimifyscript = '<script src="https://cdn.drimify.com/js/drimifywidget.release.min.js"></script>';
		$drimifydiv = '<div id="drimify-container-' . $atts['total_index'] . '" style="line-height:0"></div>';
		$atts['app_style'] = 'height: 850px; ' . $atts['app_style'];

		$implementation = sprintf(
			'<script>
				window.addEventListener("load", function() {
					var drimifyWidget = new Drimify.Widget({
						autofocus: true,
						height: "600px",
						element: "drimify-container-' . $atts['total_index'] . '",
						engine: "%s",
						style: "%s"
					});
					drimifyWidget.load();
				});
			</script>',
			esc_attr( $atts['app_url']),
			esc_attr( $atts['app_style'])
		);

		return $drimifyscript . $drimifydiv . $implementation;
	}
endif;

if ( ! function_exists( 'ee_update_whiz_hubspotform_html' ) ) :
	function ee_update_whiz_hubspotform_html( $embed, $atts ) {
		$hubspotformscript = '<script id="hubspotformscript" async defer src="https://js.hsforms.net/forms/v2.js"></script>';
		$hubspotformdiv = '<div id="hsFormDiv"></div>';
		$implementation = sprintf(
			'<script>
					document.getElementById(\'hubspotformscript\').onload = function() {
						hbspt.forms.create({
							portalId: \'%s\',
							formId: \'%s\',
							target: \'#hsFormDiv\',
						});
					}
			       	</script>',
					esc_attr( $atts['portalid']),
					esc_attr( $atts['formid'])
		);
		return $hubspotformscript . $hubspotformdiv . $implementation;
	}
endif;
