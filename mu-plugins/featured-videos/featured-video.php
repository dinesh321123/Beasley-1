<?php
/**
 * Plugin Name: Featured Videos
 * Plugin URI:
 * Description: Import Youtube or Vimeo videos into media library and use it as featured image for articles.
 * Version: 1.0.0
 * Author: 10up
 * Author URI: http://10up.com/
 * License: BSD 2-Clause
 * License URI: http://www.opensource.org/licenses/bsd-license.php
 * Text Domain: fvideos
 * Domain Path: /languages
 */

define( 'FVIDEOS_VERSION', '1.0.0' );

add_action( 'wp_enqueue_media', 'fvideos_enqueue_scripts' );
add_action( 'admin_footer', 'fvideos_print_media_templates' );
add_action( 'wp_footer', 'fvideos_print_media_templates' );
add_action( 'customize_controls_print_footer_scripts', 'fvideos_print_media_templates' );
add_action( 'wp_ajax_fvideos_get_embed', 'fvideos_discover_oembed' );
add_action( 'wp_ajax_fvideos_import_embed', 'fvideos_import_oembed' );
add_action( 'plugins_loaded', 'fvideos_load_textdomain' );

add_filter( 'post_thumbnail_html', 'fvideos_post_thumbnail_video', 10, 3 );
add_filter( 'post_class', 'fvideos_update_post_classes', 10, 3 );

function fvideos_load_textdomain() {
	load_plugin_textdomain( 'fvideos', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

function fvideos_enqueue_scripts() {
	$min = defined( 'SCRIPT_DEBUG' ) && filter_var( SCRIPT_DEBUG, FILTER_VALIDATE_BOOLEAN ) ? '' : '.min';

	wp_enqueue_script( 'fvideo', plugins_url( "/assets/dist/media{$min}.js", __FILE__ ), array( 'wp-util' ), FVIDEOS_VERSION, true );
	wp_localize_script( 'fvideo', 'fvideo', array(
		'wrongUrl'    => esc_html__( 'Please, enter valid URL', 'fvideos' ),
		'cannotEmbed' => esc_html__( 'Unexpected error happened during import', 'fvideos' ),
	) );
}

function fvideos_post_thumbnail_video( $html, $post_id, $thumbnail_id ) {
	$show_video = is_singular();
	$show_video = apply_filters( 'fvideos_show_video', $show_video, $post_id, $thumbnail_id );
	if ( $show_video ) {
		$embed = get_post_meta( $thumbnail_id, 'embed', true );
		if ( ! empty( $embed ) && ! empty( $embed['html'] ) ) {
			$html = sprintf( '<div class="fvideos">%s</div>', $embed['html'] );
			$html = apply_filters( 'fvideos_video_html', $html, $embed, $post_id, $thumbnail_id );
		}
	}

	return $html;
}

function fvideos_update_post_classes( $classes, $class, $post_id ) {
	$thumbnail_id = get_post_thumbnail_id( $post_id );
	$thumbnail_id = apply_filters( 'fvideos_post_thumbnail_id', $thumbnail_id, $post_id );
	if ( ! empty( $thumbnail_id ) ) {
		$embed = get_post_meta( $thumbnail_id, 'embed', true );
		if ( ! empty( $embed ) && ! empty( $embed['html'] ) ) {
			$classes[] = 'has-featured-video';
		}
	}

	return $classes;
}

function fvideos_print_media_templates() {
	?><script type="text/html" id="tmpl-video-embed-import">
		<div class="video__embed embed-url">
			<input type="url" class="video__url" placeholder="https://...">
			<button type="button" class="video__submit button button-primary button-hero" disabled>&rarr;</button>
			<div class="video__preview"></div>
		</div>
	</script><?php
}

function fvideos_get_oembed( $url ) {
	$found = false;
	$key = 'fvideos-embed-' . $url;
	$embed = wp_cache_get( $key, 'fvideos', false, $found );
	if ( ! $found ) {
		$embed = _wp_oembed_get_object()->get_data( $url );
		wp_cache_set( $key, $embed, 'fvideos', 5 * MINUTE_IN_SECONDS );
	}

	return $embed;
}

function fvideos_discover_oembed() {
	$url = filter_input( INPUT_GET, 'url', FILTER_VALIDATE_URL );
	if ( ! $url ) {
		wp_send_json_error( $_GET['url'] );
	}

	$embed = fvideos_get_oembed( $url );
	if ( ! empty( $embed->html ) ) {
		wp_send_json_success( $embed->html );
	} else {
		wp_send_json_error();
	}
}

function fvideos_import_oembed() {
	$url = filter_input( INPUT_GET, 'url', FILTER_VALIDATE_URL );
	if ( ! $url ) {
		wp_send_json_error( $_GET['url'] );
	}

	$embed = fvideos_get_oembed( $url );
	if ( ! empty( $embed->html ) ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$title = strip_tags( $embed->title );

		$file_array = array();
		$file_array['name'] = str_replace( ' ', '-', mb_strtolower( $title ) ) . '.jpg';
		$file_array['tmp_name'] = download_url( $embed->thumbnail_url );

		if ( ! is_wp_error( $file_array['tmp_name'] ) ) {
			$post_id = filter_input( INPUT_GET, 'post_id', FILTER_VALIDATE_INT );
			$image_id = media_handle_sideload( $file_array, $post_id, $title );
			if ( is_int( $image_id ) ) {
				$embed_array = json_decode( json_encode( $embed ), true );
				update_post_meta( $image_id, 'embed', $embed_array );

				wp_send_json_success( $image_id );
			}
		}
	}

	wp_send_json_error();
}
