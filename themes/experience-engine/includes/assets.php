<?php

add_action( 'wp_enqueue_scripts', 'ee_enqueue_front_scripts', 20 );
add_action( 'wp_head', 'ee_load_polyfills', 0 );
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

		wp_enqueue_style( 'ee-app', "{$base}/bundle/app.css", null, GREATERMEDIA_VERSION );

		/**
		 * Google WebFont scripts
		 */
		$webfont = [ 'google' => [ 'families' => [ 'Libre Franklin:300,400,500,600,700', 'Open Sans:600' ] ] ];
		wp_enqueue_script( 'google-webfont', '//ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js', null, null, false );
		wp_add_inline_script( 'google-webfont', 'var WebFontConfig = ' . wp_json_encode( $webfont ), 'before' );
		wp_script_add_data( 'google-webfont', 'async', true );
		wp_script_add_data( 'google-webfont', 'noscript', '<link href="//fonts.googleapis.com/css?family=Libre+Franklin:300,400,500,600,700|Open+Sans:600" rel="stylesheet">' );

		/**
		 * CSS vars polyfill
		 */
		wp_enqueue_script( 'css-vars-ponyfill', 'https://unpkg.com/css-vars-ponyfill@1.16.1/dist/css-vars-ponyfill.min.js', null, null, false );
		wp_script_add_data( 'css-vars-ponyfill', 'async', true );
		wp_script_add_data( 'css-vars-ponyfill', 'onload', 'cssVars(' . wp_json_encode( [ 'variables' => ee_get_css_colors() ] ) . ')' );

		/**
		 * External libraries
		 */
		wp_register_script( 'embedly-player.js', "//cdn.embed.ly/player-0.1.0{$min}.js", null, null, true );
		wp_script_add_data( 'embedly-player.js', 'async', true );

		wp_register_script( 'iframe-resizer', '//cdnjs.cloudflare.com/ajax/libs/iframe-resizer/3.6.1/iframeResizer.min.js', null, null );
		wp_script_add_data( 'iframe-resizer', 'async', true );

		wp_register_script( 'td-sdk', '//sdk.listenlive.co/web/2.9/td-sdk.min.js', null, null, true );
		wp_script_add_data( 'td-sdk', 'async', true );

		wp_register_script( 'googletag', '//www.googletagservices.com/tag/js/gpt.js', null, null, true ); // must be loaded in the footer
		wp_script_add_data( 'googletag', 'async', true );

		wp_register_script( 'firebase-app', '//www.gstatic.com/firebasejs/5.7.0/firebase-app.js', null, null, true );
		wp_register_script( 'firebase-auth', '//www.gstatic.com/firebasejs/5.7.0/firebase-auth.js', array( 'firebase-app' ), null, true );

		wp_register_script( 'intersection-observer', '//polyfill.io/v2/polyfill.min.js?features=IntersectionObserver', null, null, true );

		if ( $is_script_debug ) {
			$perfume = array(
				'firstPaint'           => true,
				'firstContentfulPaint' => true,
				'firstInputDelay'      => true,
			);

			// @see: https://zizzamia.github.io/perfume/
			wp_enqueue_script( 'perfume', "{$base}/bundle/perfume.umd.min.js", null, null, false );
			wp_add_inline_script( 'perfume', 'var perfumeInfo = new Perfume(' . json_encode( $perfume ) . ')', 'after' );
		}

		/**
		 * Application script
		 */
$bbgiconfig = <<<EOL
window.bbgiconfig = {};
try {
	window.bbgiconfig = JSON.parse( document.getElementById( 'bbgiconfig' ).innerHTML );
} catch( err ) {
	// do nothing
}
EOL;

		$deps = array(
			'firebase-app',
			'firebase-auth',
			'googletag',
			'embedly-player.js',
			'td-sdk',
			'intersection-observer',
			'iframe-resizer',
		);

		wp_enqueue_script( 'ee-app', "{$base}/bundle/app.js", $deps, GREATERMEDIA_VERSION, true );
		wp_add_inline_script( 'ee-app', $bbgiconfig, 'before' );

		/**
		 * Deregister useless scripts
		 */
		wp_dequeue_script( 'elasticpress-facets' );
		wp_dequeue_style( 'elasticpress-facets' );
	}
endif;

if ( ! function_exists( 'ee_get_css_colors' ) ) :
	function ee_get_css_colors() {
		$vars = [
			'--brand-primary'   => '#ff0000',
			'--brand-secondary' => '#ffe964',
			'--brand-tertiary'  => '#ffffff',
		];

		if ( get_option( 'ee_theme_version', '-dark' ) == '-dark' ) {
			$vars['--global-theme-primary'] = '#1a1a1a';
			$vars['--global-theme-secondary'] = '#282828';
			$vars['--global-theme-font-primary'] = 'var(--global-white)';
			$vars['--global-theme-font-secondary'] = '#a5a5a5';
			$vars['--global-theme-font-tertiary'] = 'var(--global-dove-gray)';
		}

		return $vars;
	}
endif;

if ( ! function_exists( 'ee_load_polyfills' ) ) :
	function ee_load_polyfills() {
		$base = untrailingslashit( get_template_directory_uri() );

		?><script id="polyfills">
			(function() {
				if (!Array.prototype.find) {
					var s = document.createElement('script');
					s.src = '<?php echo $base; ?>/bundle/core.min.js';
					var p = document.getElementById('polyfills')
					p.parentNode.replaceChild(s, p);
				}
			})();
		</script><?php
	}
endif;

if ( ! function_exists( 'ee_the_custom_logo' ) ) :
	function ee_the_custom_logo( $size = 'full' ) {
		$site_logo_id = get_option( 'gmr_site_logo', 0 );
		if ( $site_logo_id ) {
			$site_logo = bbgi_get_image_url( $site_logo_id, 150, 150, false );
			if ( $site_logo ) {
				echo '<a href="', esc_url( home_url() ), '" class="custom-logo-link" rel="home" itemprop="url">';
					echo '<img src="' . esc_url( $site_logo ) . '" alt="' . get_bloginfo( 'name' ) . ' | ' . get_bloginfo( 'description' ) . '" class="custom-logo" itemprop="logo">';
				echo '</a>';
			}
		}
	}
endif;

if ( ! function_exists( 'ee_the_bbgiconfig' ) ) :
	function ee_the_bbgiconfig() {
		$config = array();

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

		return $tag;
	}
endif;

if ( ! function_exists( '_ee_the_lazy_image' ) ) :
	function _ee_the_lazy_image( $url, $width, $height, $alt = '' ) {
		$is_jacapps = ee_is_jacapps();

		$image = sprintf(
			$is_jacapps
				? '<img src="%s" width="%s" height="%s">'
				: '<div class="lazy-image" data-src="%s" data-width="%s" data-height="%s" data-alt="%s"></div>',
			esc_attr( $url ),
			esc_attr( $width ),
			esc_attr( $height ),
			esc_attr( $alt )
		);

		$image = apply_filters( '_ee_the_lazy_image', $image, $is_jacapps, $url, $width, $height, $alt );

		return $image;
	}
endif;

if ( ! function_exists( 'ee_the_lazy_image' ) ) :
	function ee_the_lazy_image( $image_id, $echo = true ) {
		$html = '';
		if ( ! empty( $image_id ) ) {
			$alt = trim( strip_tags( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ) );

			if ( ee_is_jacapps() ) {
				$width = 800;
				$height = 500;
				$url = bbgi_get_image_url( $image_id, $width, $height );

				$html = _ee_the_lazy_image( $url, $width, $height, $alt );
			} else {
				$img = wp_get_attachment_image_src( $image_id, 'original' );
				if ( ! empty( $img ) ) {
					$html = _ee_the_lazy_image( $img[0], $img[1], $img[2], $alt );
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
	function ee_the_lazy_thumbnail( $post = null ) {
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
			if ( ! $thumbnail_id && ! is_singular() ) {
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
