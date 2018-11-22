<?php

add_action( 'wp_enqueue_scripts', 'ee_enqueue_front_scripts', 20 );

add_filter( 'wp_audio_shortcode_library', '__return_false' );
add_filter( 'script_loader_tag', 'ee_script_loader', 10, 3 );
add_filter( 'fvideos_show_video', 'ee_fvideos_show_video', 10, 2 );
add_filter( 'tribe_events_assets_should_enqueue_frontend', '__return_false' );

remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

if ( ! function_exists( 'ee_enqueue_front_scripts' ) ) :
	function ee_enqueue_front_scripts() {
		$base = untrailingslashit( get_template_directory_uri() );
		$min = defined( 'SCRIPT_DEBUG' ) && filter_var( SCRIPT_DEBUG, FILTER_VALIDATE_BOOLEAN ) ? '' : '.min';

		wp_enqueue_style( 'ee-app', "{$base}/bundle/app.css", null, GREATERMEDIA_VERSION );

		/**
		 * Google WebFont scripts
		 */
		$webfont = array(
			'google' => array(
				'families' => array( 'Libre+Franklin:300,400,500,600,700' ),
			),
		);

		wp_enqueue_script( 'google-webfont', '//ajax.googleapis.com/ajax/libs/webfont/1/webfont.js', null, null, false );
		wp_add_inline_script( 'google-webfont', 'var WebFontConfig = ' . wp_json_encode( $webfont ), 'before' );
		wp_script_add_data( 'google-webfont', 'async', true );
		wp_script_add_data( 'google-webfont', 'noscript', '<link href="//fonts.googleapis.com/css?family=Libre+Franklin:300,400,500,600,700" rel="stylesheet">' );

		/**
		 * Polyfills
		 */
		wp_register_script( 'es6-promise', '//cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.auto.min.js', null, null );
		wp_script_add_data( 'es6-promise', 'conditional', 'lte IE 11' );

		/**
		 * Player scripts
		 */
		wp_register_script( 'embedly-player.js', "//cdn.embed.ly/player-0.1.0{$min}.js", null, null, true );
		wp_script_add_data( 'embedly-player.js', 'async', true );

		wp_register_script( 'td-sdk', '//sdk.listenlive.co/web/2.9/td-sdk.min.js', null, null, true );
		wp_script_add_data( 'td-sdk', 'async', true );

		/**
		 * Application script
		 */
		wp_enqueue_script( 'ee-app', "{$base}/bundle/app.js", array( 'embedly-player.js', 'td-sdk', 'es6-promise' ), GREATERMEDIA_VERSION, true );
		wp_localize_script( 'ee-app', 'bbgiconfig', apply_filters( 'bbgiconfig', array() ) );

		/**
		 * Deregister useless scripts
		 */
		wp_dequeue_script( 'elasticpress-facets' );
		wp_dequeue_style( 'elasticpress-facets' );
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

		return $tag;
	}
endif;

if ( ! function_exists( '_ee_the_lazy_image' ) ) :
	function _ee_the_lazy_image( $url, $width, $height ) {
		return sprintf(
			ee_is_jacapps()
				? '<img src="%s" width="%s" height="%s">'
				: '<div class="lazy-image" data-src="%s" data-width="%s" data-height="%s"></div>',
			esc_attr( $url ),
			esc_attr( $width ),
			esc_attr( $height )
		);
	}
endif;

if ( ! function_exists( 'ee_the_lazy_image' ) ) :
	function ee_the_lazy_image( $image_id, $echo = true ) {
		$html = '';
		if ( ! empty( $image_id ) ) {
			if ( ee_is_jacapps() ) {
				$width = 800;
				$height = 500;
				$url = bbgi_get_image_url( $image_id, $width, $height );

				$html = _ee_the_lazy_image( $url, $width, $height );
			} else {
				$img = wp_get_attachment_image_src( $image_id, 'original' );
				if ( ! empty( $img ) ) {
					$html = _ee_the_lazy_image( $img[0], $img[1], $img[2] );
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
