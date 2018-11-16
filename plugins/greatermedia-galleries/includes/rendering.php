<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class GreaterMediaGallery {

	public static $strip_shortcodes = false;

	public static function init() {
		if ( ! is_admin() && ! defined( 'WP_CLI' ) && ( ! defined( 'DOING_CRON' ) || ! DOING_CRON ) ) {
			// Override the core gallery shortcode with our own handler, only on the front end
			remove_shortcode( 'gallery' );
			add_shortcode( 'gallery', array( __CLASS__, 'render_gallery' ) );
		}

		// If we need to manually render somewhere, like on the top of a single-gallery template
		add_action( 'gmr_gallery', array( __CLASS__, 'do_gallery_action' ) );

		// Remove gallery shortcodes from content, since we have these at the top of single-page
		add_filter( 'the_content', array( __CLASS__, 'strip_for_single_gallery' ) );

		// Register scripts
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_scripts' ), 1 );
	}

	/**
	 * Registers gallery scripts to use on the front end.
	 *
	 * @static
	 * @access public
	 * @action wp_enqueue_scripts
	 */
	public static function register_scripts() {
		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

		/* all js files are being concatenated into a single js file.
		 * This include all js files for cycle2, located in `assets/js/vendor/cycle2/`
		 * and `gmr_gallery.js`, located in `assets/js/src/`
		 */
		wp_register_script( 'gmr-gallery', GREATER_MEDIA_GALLERIES_URL . "assets/js/gmr_gallery{$postfix}.js", array( 'jquery' ), GREATER_MEDIA_GALLERIES_VERSION, true );
		wp_register_style( 'gmr-gallery', GREATER_MEDIA_GALLERIES_URL . "assets/css/gmr_gallery{$postfix}.css", array(), GREATER_MEDIA_GALLERIES_VERSION );
	}

	/**
	 * Strips gallery shortcodes for content, on pages where we know we've run the action instead
	 *
	 * @param string $content
	 *
	 * @return string Final content with galleries removed
	 */
	public static function strip_for_single_gallery( $content ) {
		if ( self::$strip_shortcodes ) {
			$content = preg_replace( '/\[gallery.*?\]/', '', $content );
		}

		return $content;
	}

	/**
	 * Returns a WP_Query that corresponds to the IDs provided
	 *
	 * @param array $ids Array of image IDs
	 *
	 * @return WP_Query
	 */
	public static function get_query_for_ids( $ids ) {
		$photos = new WP_Query(
			array(
				'ignore_sticky_posts' => true,
				'post__in'            => $ids,
				'post_status'         => 'inherit',
				'post_type'           => 'attachment',
				'posts_per_page'      => count( $ids ),
				'orderby'             => 'post__in',
			)
		);

		return $photos;
	}

	/**
	 * Gets an array of ids for the attachments in the gallery
	 * @param $post
	 * @return Array
	 */
	public static function get_attachment_ids_for_post( $post ) {
		static $ids = array();

		$post = get_post( $post );
		if ( ! isset( $ids[ $post->ID ] ) ) {
			$array_ids = get_post_meta( $post->ID, 'gallery-image' );
			$array_ids = array_filter( array_map( 'intval', $array_ids ) );

			if ( empty( $array_ids ) && preg_match_all( '/\[gallery.*ids=.(.*).\]/', $post->post_content, $ids ) ) {
				$array_ids = array();
				foreach( $ids[1] as $match ) {
					$array_id = explode( ',', $match );
					$array_id = array_filter( array_map( 'intval', $array_id ) );

					$array_ids = array_merge( $array_ids, $array_id );
				}
			}

			$ids[ $post->ID ] = is_array( $array_ids )
				? array_values( array_filter( array_map( 'intval', $array_ids ) ) )
				: array();
		}

		return ! empty( $ids[ $post->ID ] )
			? $ids[ $post->ID ]
			: null;
	}

	/**
	 * Gets a WP_Query for the attachments in the gallery
	 * @param $post
	 * @return WP_Query
	 */
	public static function get_query_for_post( $post ) {
		$array_ids = get_post_meta( $post->ID, 'gallery-image' );

		if ( empty( $array_ids ) && preg_match_all( '/\[gallery.*ids=.(.*).\]/', $post->post_content, $ids ) ) {
			foreach( $ids[1] as $match ) {
				$array_id = explode( ',', $match );
				$array_id = array_map( 'intval', $array_id );

				$array_ids = array_merge( $array_ids, $array_id );
			}
		}

		return ! empty( $array_ids )
			? self::get_query_for_ids( $array_ids )
			: null;
	}

	/**
	 * Renders a gallery for a post when do_action( 'gmr_gallery' ) is called
	 */
	public static function do_gallery_action( $strip_shortcodes ) {
		// So that we remove the gallery from content, since we're rendering it now
		self::$strip_shortcodes = true;

		$ids = self::get_attachment_ids_for_post( get_queried_object() );
		if ( ! empty( $ids ) ) {
			$content = self::render_gallery( array( 'ids' => implode( ',', $ids ) ) );
			echo apply_filters( 'the_secondary_content', $content );
		}
	}

	public static function render_gallery( $attr ) {
		$attr = shortcode_atts( array( 'ids' => '' ), $attr );
		$ids = explode( ',', $attr['ids'] );
		$ids = array_filter( array_map( 'intval', $ids ) );
		if ( empty( $ids ) ) {
			return gallery_shortcode( $attr );
		}

		$post = get_queried_object();
		$sponsored_image = get_field( 'sponsored_image', $post );

		$image = current( $ids );
		$content = sprintf(
			'<div class="gallery__embed"><a href="%s/view/%s/"><div><img src="%s" style="margin: 0 auto"></div>',
			esc_attr( untrailingslashit( get_permalink( $post ) ) ),
			esc_attr( get_post_field( 'post_name', $sponsored_image ? $sponsored_image : $image ) ),
			bbgi_get_image_url( $image, 512, 342, 'crop', true )
		);

		$content .= '<div class="gallery__embed--thumbnails">';

		for ( $max = 5, $i = 1, $len = count( $ids ); $i < $len && $i <= $max; $i++ ) {
			$content .= '<div class="gallery__embed--thumbnail">';

			$content .= '<img src="' . esc_url( bbgi_get_image_url( $ids[ $i ], 100, 75 ) ) . '">';
			if ( $i == $max && $len > $max ) {
				$content .= '<span class="gallery__embed--more">+' . ( $len - $max ) . '</span>';
			}

			$content .= '</div>';
		}

		$content .= '</div>';
		$content .= '<small class="gallery__embed--cta">Click to see all</small>';
		$content .= '</a></div>';

		return $content;
	}

}

GreaterMediaGallery::init();
