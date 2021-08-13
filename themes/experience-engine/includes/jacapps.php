<?php

add_action( 'wp_loaded', 'ee_setup_jacapps' );

if ( ! function_exists( 'ee_is_jacapps' ) ) :
	function ee_is_jacapps() {
		static $jacapps_pos = null;

		if ( $jacapps_pos === null ) {
			$jacapps_pos = stripos( $_SERVER['HTTP_USER_AGENT'], 'jacapps' );

			// Allow way to toggle jacapps through URL querystring
			if ( isset( $_GET['jacapps'] ) ) {
				$jacapps_pos = 1;
			}
		}

		return false !== $jacapps_pos;
	}
endif;

if ( ! function_exists( 'ee_setup_jacapps' ) ) :
	function ee_setup_jacapps() {
		if ( ! ee_is_jacapps() ) {
			return;
		}

		add_action( 'wp_print_scripts', 'ee_jacapps_enqueue_scripts', 99 );

		add_filter( 'body_class', 'ee_jacapps_body_class' );
		add_filter( 'omny_embed_key', 'ee_update_jacapps_omny_key' );
		add_filter( 'secondstreet_embed_html', 'ee_update_jacapps_secondstreet_html', 10, 2 );
		add_filter( 'secondstreetpref_html', 'ee_update_jacapps_secondstreetpref_html', 10, 2 );
		add_filter( 'secondstreetsignup_html', 'ee_update_jacapps_secondstreetsignup_html', 10, 2 );
		add_filter( 'mapbox_html', 'ee_update_jacapps_mapbox_html', 10, 2 );
		add_filter( 'hubspotform_html', 'ee_update_jacapps_hubspotform_html', 10, 2 );
		add_filter( 'dml-branded_html', 'ee_update_jacapps_dml_branded_content', 10, 2);
		add_filter( 'drimify_html', 'ee_update_jacapps_drimify_html', 10, 2 );

		remove_filter( 'omny_embed_html', 'ee_update_omny_embed' );
	}
endif;

if ( function_exists( 'vary_cache_on_function' ) ) :
	// batcache variant
	vary_cache_on_function( 'return (bool) preg_match("/jacapps/i", $_SERVER["HTTP_USER_AGENT"]);' );
endif;

if ( ! function_exists( 'ee_jacapps_body_class' ) ) :
	function ee_jacapps_body_class( $classes ) {
		$classes[] = 'jacapps';
		return $classes;
	}
endif;

if ( ! function_exists( 'ee_jacapps_enqueue_scripts' ) ) :
	function ee_jacapps_enqueue_scripts() {

/**
 * Application script
 * jacapps needs the overarching config now that ads
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
EOL;

		wp_dequeue_script( 'ee-app' );
		wp_enqueue_script( 'iframe-resizer' );
		wp_enqueue_script( 'embedly-player.js' );
		wp_enqueue_script( 'branded-content-scripts' );

		// Need googletag for ads in jacapps
		wp_enqueue_script( 'googletag' );
		wp_script_add_data( 'googletag', 'async', true );
		wp_add_inline_script( 'googletag', $bbgiconfig, 'before' );
		wp_enqueue_script( 'wp-embed', '', [], false, true );

	}
endif;

if ( ! function_exists( 'ee_update_jacapps_omny_key' ) ) :
	function ee_update_jacapps_omny_key( $key ) {
		return $key . ':jacapps';
	}
endif;

if ( ! function_exists( 'ee_update_jacapps_secondstreet_html' ) ) :
	function ee_update_jacapps_secondstreet_html( $embed, $atts ) {
		$url = 'https://embed-' . rawurlencode( $atts['op_id'] ) . '.secondstreetapp.com/Scripts/dist/embed.js';
		return '<script src="' . esc_url( $url ) . '" data-ss-embed="promotion" data-opguid="' . esc_attr( $atts['op_guid'] ) . '" data-routing="' . esc_attr( $atts['routing'] ) . '"></script>';
	}
endif;

if ( ! function_exists( 'ee_update_jacapps_dml_branded_content' ) ) :
	function ee_update_jacapps_dml_branded_content( $embed, $atts ) {

		$html = '';

		if ($atts['layout']) {
			$html = '<div data-stackid="' . esc_attr( $atts['stackid'] ) . '" data-layout="' . esc_attr( $atts['layout'] ) . '" class="dml-widget-container"></div>';
		} else {
			$html = '<div data-stackid="' . esc_attr( $atts['stackid'] ) . '" class="dml-widget-container"></div>';
		}

		return $html;
	}
endif;

if ( ! function_exists( 'ee_update_jacapps_secondstreetpref_html' ) ) :
	function ee_update_jacapps_secondstreetpref_html( $embed, $atts ) {
		$url = 'https://embed.secondstreetapp.com/Scripts/dist/preferences.js';
		return '<script src="' . esc_url( $url ) . '" data-ss-embed="preferences" data-organization-id="' . esc_attr( $atts['organization_id'] ) . '"></script>';
	}
endif;

if ( ! function_exists( 'ee_update_jacapps_secondstreetsignup_html' ) ) :
	function ee_update_jacapps_secondstreetsignup_html( $embed, $atts ) {
		$url = 'https://embed.secondstreetapp.com/Scripts/dist/optin.js';
		return '<script src="' . esc_url( $url ) . '" data-ss-embed="optin" data-design-id="' . esc_attr( $atts['design_id'] ) . '"></script>';
	}
endif;

if ( ! function_exists( 'ee_update_jacapps_mapbox_html' ) ) :
	function ee_update_jacapps_mapbox_html( $embed, $atts ) {
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

if ( ! function_exists( 'ee_update_jacapps_drimify_html' ) ) :
	function ee_update_jacapps_drimify_html( $embed, $atts ) {
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

if ( ! function_exists( 'ee_update_jacapps_hubspotform_html' ) ) :
	function ee_update_jacapps_hubspotform_html( $embed, $atts ) {
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

