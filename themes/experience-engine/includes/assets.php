<?php

add_action( 'wp_enqueue_scripts', 'ee_enqueue_front_scripts', 20 );

add_action( 'beasley_after_body', 'ee_the_bbgiconfig' );

add_filter( 'wp_audio_shortcode_library', '__return_false' );
add_filter( 'script_loader_tag', 'ee_script_loader', 10, 3 );
add_filter( 'fvideos_show_video', 'ee_fvideos_show_video', 10, 2 );
add_filter( 'tribe_events_assets_should_enqueue_frontend', '__return_false' );

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

if ( ! function_exists( 'ee_enqueue_front_scripts' ) ) :
	function ee_enqueue_front_scripts() {
		$is_script_debug = defined( 'SCRIPT_DEBUG' ) && filter_var( SCRIPT_DEBUG, FILTER_VALIDATE_BOOLEAN );

		$base = untrailingslashit( get_template_directory_uri() );
		$min = $is_script_debug ? '' : '.min';

		wp_enqueue_style( 'ee-app', "{$base}/bundle/main.css", null, GREATERMEDIA_VERSION );

		/**
		 * Google WebFont scripts
		 */
		$webfont = [ 'google' => [ 'families' => [ 'Libre Franklin:300,400,500,600,700', 'Open Sans:600&display=swap' ] ] ];
		wp_enqueue_script( 'google-webfont', '//ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js', null, null, false );
		wp_add_inline_script( 'google-webfont', 'var WebFontConfig = ' . wp_json_encode( $webfont ), 'before' );
		wp_script_add_data( 'google-webfont', 'async', true );
		wp_script_add_data( 'google-webfont', 'noscript', '<link href="//fonts.googleapis.com/css?family=Libre+Franklin:300,400,500,600,700%7COpen+Sans:600" rel="stylesheet">' );

		// This is being used in Content Shortcodes https://gitlab.10up.com/beasley/beasley/blob/master/mu-plugins/classes/Bbgi/Shortcodes.php
		wp_register_script( 'iframe-resizer', '//cdnjs.cloudflare.com/ajax/libs/iframe-resizer/3.6.1/iframeResizer.min.js', null, null );
		wp_script_add_data( 'iframe-resizer', 'async', true );

		// DML Branded Content
		wp_register_script( 'branded-content-scripts', '//c.go-fet.ch/a/embed.js', null, null, true);
		wp_script_add_data( 'branded-content-scripts', 'async', true );

		// Triton Player SDK
		// Documentation: https://userguides.tritondigital.com/spc/tdplay2/
		wp_register_script( 'td-sdk', '//sdk.listenlive.co/web/2.9/td-sdk.min.js', null, null, true );
		wp_script_add_data( 'td-sdk', 'async', true );

		// Google Tag Manager
		wp_register_script( 'googletag', '//www.googletagservices.com/tag/js/gpt.js', null, null, true ); // must be loaded in the footer
		wp_script_add_data( 'googletag', 'async', true );

		$ad_confiant_enabled = get_option( 'ad_confiant_enabled', 'off' );
		if ( $ad_confiant_enabled == 'on') {
			echo '<script async="" src="https://confiant-integrations.global.ssl.fastly.net/G11pdlPHJcAhH4pQnGr31X7_kbM/gpt_and_prebid/config.js"></script>';
		}

		if ( function_exists( 'enqueue_prebid_scripts' ) ) {
			enqueue_prebid_scripts();

			$ad_reset_digital_enabled = get_option( 'ad_reset_digital_enabled', 'off' );
			if ( $ad_reset_digital_enabled == 'on') {
				enqueue_reset_digital_pixel();
			}
		}

		if ( function_exists( 'enqueue_vimeopreroll_scripts' ) ) {
			enqueue_vimeopreroll_scripts();
		}

		// TODO: refactor this to use wp_localize_script.
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

// Add alt parameter to auto genrated images for lighthouse issue
var checkTritonPixeltimes = 0;
var checkTritonPixel = setInterval(function() {
    checkTritonPixeltimes += 1;
    var triton_pixel_image = document.getElementsByClassName('triton-pixel');
    if(triton_pixel_image.length > 0) {
        for (var idx = 0; idx < triton_pixel_image.length; idx++) {
            if(triton_pixel_image[idx] && triton_pixel_image[idx].tagName == "IMG") {
                triton_pixel_image[idx].alt = "";
            }
        }
        clearInterval(checkTritonPixel);
    }
    if(checkTritonPixeltimes > 10) {
        clearInterval(checkTritonPixel);
    }
}, 500);

// Add alt parameter to auto genrated images for lighthouse issue
var checkResetPixeltimes = 0;
var checkResetPixel = setInterval(function() {
	checkResetPixeltimes += 1;
	var reset_pixel_image = document.getElementById('resetPixelContainer');
	if(reset_pixel_image) {
		var reset_pixel_image_nodes = reset_pixel_image.childNodes;
		if(reset_pixel_image_nodes.length) {
			for(var i=0; i<reset_pixel_image_nodes.length; i++) {
				if (reset_pixel_image_nodes[i].tagName == 'IMG') {
					reset_pixel_image_nodes[i].alt = "Reset Pixel Image";
				 }
			}
		}
		clearInterval(checkResetPixel);
	}
	if(checkResetPixeltimes > 10) {
		clearInterval(checkResetPixel);
	}
}, 500);
EOL;

		$deps = array(
			'googletag',
			'td-sdk',
			'iframe-resizer',
			'branded-content-scripts'
		);

		wp_enqueue_script( 'ee-app', "{$base}/bundle/app.js", $deps, GREATERMEDIA_VERSION, true );
		wp_add_inline_script( 'ee-app', $bbgiconfig, 'before' );

		// Deregister useless scripts
		wp_dequeue_script( 'elasticpress-facets' );
		wp_dequeue_style( 'elasticpress-facets' );
	}
endif;

if ( ! function_exists( 'ee_get_css_colors' ) ) :
	function ee_get_css_colors() {
		$vars = [
			'--brand-primary'                => get_option( 'ee_theme_primary_color', '#ff0000' ),
			'--brand-secondary'              => get_option( 'ee_theme_secondary_color', '#ffe964' ),
			'--brand-tertiary'               => get_option( 'ee_theme_tertiary_color', '#ffffff' ),
			'--brand-background-color'       => get_option( 'ee_theme_background_color', '#ffffff' ),
			'--brand-button-color'           => get_option( 'ee_theme_button_color', '#ffe964' ),
			'--brand-text-color'             => get_option( 'ee_theme_text_color', '#000000' ),
			'--brand-sponsorship-text-color' => get_option( 'ee_theme_sponsorship_color', '#000000' ),
		];

		if ( get_option( 'ee_theme_version', '-dark' ) == '-dark' ) {
			$vars['--global-theme-primary'] = '#1a1a1a';
			$vars['--global-theme-secondary'] = '#282828';
			$vars['--global-theme-font-primary'] = 'var(--global-white)';
			$vars['--global-theme-font-secondary'] = '#a5a5a5';
			$vars['--global-theme-font-tertiary'] = 'var(--global-dove-gray)';
		}

		if ( get_option( 'ee_theme_version', '-light' ) == '-light' ) {
			$vars['--global-black'] = '#000000';
			$vars['--global-mine-shaft'] = '#333333';
			$vars['--global-tundora'] = '#444444';
			$vars['--global-dove-gray'] = '#737373';
			$vars['--global-silver'] = '#cccbcb';
			$vars['--global-silver-chalice'] = '#a5a5a5';
			$vars['--global-mercury'] = '#e5e5e5';
			$vars['--global-gallery'] = '#f0f0f0';
			$vars['--global-white'] = '#ffffff';
			$vars['--global-alabaster'] = '#FCFCFC';
			$vars['--global-gallery'] = '#EFEFEF';

			$vars['--global-theme-primary'] = '#f1f1f1';
			$vars['--global-theme-secondary'] = '#fcfcfc';
			$vars['--global-theme-font-primary'] = 'var(--global-tundora)';
			$vars['--global-theme-font-secondary'] = 'var(--global-font-primary)';
			$vars['--global-theme-font-tertiary'] = 'var(--global-dove-gray)';
		}

		return $vars;
	}
endif;

if ( ! function_exists( 'ee_get_other_css_vars' ) ) :
	function ee_get_other_css_vars() {
		$vars = [
			'--brand-play-opacity'           		=> get_option( 'play_opacity_setting', '0.8' ),
			'--brand-play-hover-opacity'     		=> get_option( 'play_hover_opacity_setting', '1' ),
			'--brand-play-live-hover-opacity'     	=> get_option( 'play_live_hover_opacity_setting', '0.8' ),
			'--default-configurable-iframe-height'	=> get_option( 'configurable_iframe_height', '0' ) . 'px',
			'--configurable-iframe-height'     		=> get_option( 'configurable_iframe_height', '0' ) . 'px',
		];

		return $vars;
	}
endif;

if ( ! function_exists( 'ee_the_custom_logo' ) ) :
	function ee_the_custom_logo( $base_w = 150, $base_h = 150 ) {
		$site_logo_id = get_option( 'gmr_site_logo', 0 );
		if ( $site_logo_id ) {
			$site_logo = bbgi_get_image_url( $site_logo_id, $base_w, $base_h, false );
			if ( $site_logo ) {
				$alt = get_bloginfo( 'name' ) . ' | ' . get_bloginfo( 'description' );
				$site_logo_2x = bbgi_get_image_url( $site_logo_id, 2 * $base_w, 2 * $base_h, false );
				echo '<a href="', esc_url( home_url() ), '" class="custom-logo-link" rel="home" itemprop="url">';
					printf(
						'<img src="%s" srcset="%s 2x" alt="%s" class="custom-logo" itemprop="logo">',
						esc_url( $site_logo ),
						esc_url( $site_logo_2x ),
						esc_attr( $alt )
					);
				echo '</a>';
			}
		}
	}
endif;

if ( ! function_exists( 'ee_the_beasley_logo' ) ) :
	function ee_the_beasley_logo() {
		echo '<a href="https://bbgi.com" target="_blank" rel="noopener">
			<img src="', get_template_directory_uri(), '/assets/images/large-BMG60YearsLogo.png" style="max-height: 150px; max-width: 150px; padding-bottom: 10px;" alt="Beasley Media Group">
		</a>';
	}
endif;

if ( ! function_exists( 'ee_the_subheader_logo' ) ) :
	function ee_the_subheader_logo( $mobile_or_desktop, $base_w = 150, $base_h = 150 ) {
	    $field_name = 'ee_subheader_' . $mobile_or_desktop . '_logo';
	    $atag_class_name = $mobile_or_desktop . '-subheader-logo-link';
		$site_logo_id = get_option( $field_name, 0 );
		if ( $site_logo_id ) {
			$site_logo = bbgi_get_image_url( $site_logo_id, $base_w, $base_h, false );
			if ( $site_logo ) {
				$alt = get_bloginfo( 'name' ) . ' | ' . get_bloginfo( 'description' );
				$site_logo_2x = bbgi_get_image_url( $site_logo_id, 2 * $base_w, 2 * $base_h, false );
				echo '<a href="', esc_url( home_url() ), '" class="', $atag_class_name, '" rel="home" itemprop="url">';
				printf(
					'<img src="%s" srcset="%s 2x" alt="%s" class="custom-logo" itemprop="logo">',
					esc_url( $site_logo ),
					esc_url( $site_logo_2x ),
					esc_attr( $alt )
				);
				echo '</a>';
			}
		}
	}
endif;

if ( ! function_exists( 'ee_the_beasley_logo' ) ) :
	function ee_the_beasley_logo() {
		echo '<a href="https://bbgi.com" target="_blank" rel="noopener">
			<img src="', get_template_directory_uri(), '/assets/images/large-BMG60YearsLogo.png" style="max-height: 150px; max-width: 150px;" alt="Beasley Media Group">
		</a>';
	}
endif;

if ( ! function_exists( 'ee_the_bbgiconfig' ) ) :
	function ee_the_bbgiconfig() {
		$config = array(
			'cssvars' => array( 'variables' => array_merge(ee_get_css_colors(), ee_get_other_css_vars()) ),
			'geotargetly' => ee_current_page_needs_geotargetly(),
			'related_article_title' => get_option( 'related_article_title', 'You May Also Like' ),
			'ad_rotation_enabled' => get_option( 'ad_rotation_enabled', 'on' ),
			'ad_rotation_polling_sec_setting' => get_option( 'ad_rotation_polling_sec_setting', '5' ),
			'ad_rotation_refresh_sec_setting' => get_option( 'ad_rotation_refresh_sec_setting', '30' ),
			'ad_vid_rotation_refresh_sec_setting' => get_option( 'ad_vid_rotation_refresh_sec_setting', '60' ),
			'vid_ad_html_tag_csv_setting' => get_option( 'vid_ad_html_tag_csv_setting', 'mixpo' ),
			'ad_rubicon_zoneid_setting' => get_option( 'ad_rubicon_zoneid_setting', '' ),
			'ad_appnexus_placementid_setting' => get_option( 'ad_appnexus_placementid_setting', '' ),
			'ad_ix_siteid_setting' => get_option( 'ad_ix_siteid_setting', '' ),
			'ad_reset_digital_enabled' => get_option( 'ad_reset_digital_enabled', 'off' ),
			'prebid_enabled' => function_exists( 'enqueue_prebid_scripts' ),

			/** Live Streaming Intervals */
			'intervals'  => [
				'live_streaming' => absint( get_option( 'gmr_live_streaming_interval', 1 ) ),
				'inline_audio'   => absint( get_option( 'gmr_inline_audio_interval', 1 ) ),
			],
		);

		$custom_logo_id = get_option( 'gmr_site_logo' );
		if ( $custom_logo_id ) {
			$image = wp_get_attachment_image_src( $custom_logo_id, 'original' );
			if ( is_array( $image ) && ! empty( $image ) ) {
				$config['theme'] = array(
					'logo' => array(
						'url'    => $image[0],
						'width'  => $image[1],
						'height' => $image[2],
					),
				);
			}
		}

		printf(
			'<script id="bbgiconfig" type="application/json">%s</script>',
			json_encode( apply_filters( 'bbgiconfig', $config ) )
		);
	}
endif;

if ( ! function_exists( 'ee_gmr_site_logo' ) ) :
	function ee_gmr_site_logo() {
		$custom_logo_id = get_option( 'gmr_site_logo' );
		if ( $custom_logo_id ) {
			$image = wp_get_attachment_image_src( $custom_logo_id, 'original' );
			if ( is_array( $image ) && ! empty( $image ) ) {
				$config['theme'] = array(
					'logo' => array(
						'url'    => $image[0],
						'width'  => $image[1],
						'height' => $image[2],
					),
				);
			}
		}
		return $config;
	}
endif;

if ( ! function_exists( 'ee_script_loader' ) ) :
	function ee_script_loader( $tag, $handler, $src ) {
		global $wp_scripts;

		$async = $wp_scripts->get_data( $handler, 'async' );
		if ( filter_var( $async, FILTER_VALIDATE_BOOLEAN ) ) {
			$tag = str_replace( " src=\"{$src}\"", " async src=\"{$src}\"", $tag );
			$tag = str_replace( " src='{$src}'", " async src='{$src}'", $tag );
		}

		$noscript = $wp_scripts->get_data( $handler, 'noscript' );
		if ( $noscript ) {
			$tag .= sprintf( '<noscript>%s</noscript>', $noscript );
		}

		$onload = $wp_scripts->get_data( $handler, 'onload' );
		if ( $onload ) {
			$onload = esc_attr( $onload );
			$tag = str_replace( " src=\"{$src}\"", " src=\"{$src}\" onload=\"{$onload}\"", $tag );
			$tag = str_replace( " src='{$src}'", " src=\"{$src}\" onload=\"{$onload}\"", $tag );
		}

		$crossorigin = $wp_scripts->get_data( $handler, 'crossorigin' );
		if ( $crossorigin ) {
			$tag = str_replace( " src=\"{$src}\"", " src=\"{$src}\" crossorigin", $tag );
			$tag = str_replace( " src='{$src}'", " src=\"{$src}\" crossorigin", $tag );
		}

		return $tag;
	}
endif;

if ( ! function_exists( '_ee_the_lazy_image' ) ) :
	function _ee_the_lazy_image( $url, $width, $height, $alt = '', $attribution = '' ) {
		$is_common_mobile = ee_is_common_mobile();

		$image = sprintf(
			$is_common_mobile
				? '<div class="non-lazy-image"><img src="%s" width="%s" height="%s" alt="%s"><div class="non-lazy-image-attribution">%s</div></div>'
				: '<div class="lazy-image" data-src="%s" data-width="%s" data-height="%s" data-alt="%s" data-attribution="%s"></div>',
			esc_attr( $url ),
			esc_attr( $width ),
			esc_attr( $height ),
			esc_attr( $alt ),
			esc_attr( $attribution )
		);

		$image = apply_filters( '_ee_the_lazy_image', $image, $is_common_mobile, $url, $width, $height, $alt );

		return $image;
	}
endif;

if ( ! function_exists( 'ee_the_lazy_image' ) ) :
	function ee_the_lazy_image( $image_id, $echo = true ) {
		$html = '';
		if ( ! empty( $image_id ) ) {
			$alt = trim( strip_tags( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ) );
			$attribution = get_post_meta( $image_id, 'gmr_image_attribution', true );

			if ( ee_is_common_mobile() ) {
				$width = 800;
				$height = 500;
				$url = bbgi_get_image_url( $image_id, $width, $height );

				$html = _ee_the_lazy_image( $url, $width, $height, $alt, $attribution );
			} else {
				$img = wp_get_attachment_image_src( $image_id, 'original' );
				if ( ! empty( $img ) ) {
					$html = _ee_the_lazy_image( $img[0], $img[1], $img[2], $alt, $attribution );
				}
			}
		}

		if ( $echo ) {
			echo $html;
		}

		return $html;
	}
endif;

if ( ! function_exists( 'ee_the_lazy_thumbnail' ) ) :
	function ee_the_lazy_thumbnail( $post = null, $force = false ) {
		$post = get_post( $post );
		if ( ! is_a( $post, '\WP_Post' ) ) {
			return;
		}

		if ( ! empty( $post->picture ) ) {
			$url = $post->picture['url'];
			$parts = parse_url( $url );
			if ( $parts['host'] == 'resize.bbgi.com' ) {
				$query = array();
				parse_str( $parts['query'], $query );
				if ( ! empty( $query['url'] ) ) {
					$url = $query['url'];
				}
			}

			$width = ! empty( $post->picture['width'] ) ? intval( $post->picture['width'] ) : 400;
			$height = ! empty( $post->picture['height'] ) ? intval( $post->picture['height'] ) : 300;

			echo _ee_the_lazy_image( $url, $width, $height );
		} else {
			$thumbnail_id = get_post_thumbnail_id( $post );
			$thumbnail_id = apply_filters( 'ee_post_thumbnail_id', $thumbnail_id, $post );

			$thumbnail = false;
			if ( $thumbnail_id ) {
				$thumbnail = get_post( $thumbnail_id );
			}

			if ( ! is_a( $thumbnail, '\WP_Post' ) && ( ! is_singular() || is_singular( 'show' ) || $force ) ) {
				$fallback_id = get_option( "{$post->post_type}_fallback" );
				if ( $fallback_id ) {
					$thumbnail_id = $fallback_id;
				}
			}

			$html = ee_the_lazy_image( $thumbnail_id, false );

			echo apply_filters( 'post_thumbnail_html', $html, $post->ID, $thumbnail_id );
		}
	}
endif;

if ( ! function_exists( 'ee_fvideos_show_video' ) ) :
	function ee_fvideos_show_video( $show, $post_id ) {
		$queried = get_queried_object();
		$post = get_post( $post_id );

		return is_a( $post, '\WP_Post' ) && is_a( $queried, '\WP_Post' ) && $post->post_type == $queried->post_type;
	}
endif;
