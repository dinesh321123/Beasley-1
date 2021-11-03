<?php
/**
 * Sets up settings page and shortcode for Second Street
 */

namespace Bbgi\Integration;

class GallerySelection extends \Bbgi\Module {

	// track index of the app
	private static $total_index = 0;

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		// add shortcodes
		add_shortcode( 'select-gallery', $this( 'render_shortcode' ) );
	}

	/**
	 * Renders ss-promo shortcode.
	 *
	 * @access public
	 * @param array $attributes Array of shortcode arguments.
	 * @return string Shortcode markup.
	 */
	public function render_shortcode( $atts ) {
		$attributes = shortcode_atts( array(
			'gallery_id' => ''
		), $atts, 'select-gallery' );

		if( empty( $attributes['gallery_id'] ) ) {
			return;
		}

		if( is_null( get_post( $attributes['gallery_id'] ) ) ){
			return;
	  	}

		$ids = $this->get_attachment_ids_for_post( $attributes['gallery_id'] );
		// echo "<pre>", print_r($ids), "</pre>";

		$post = get_queried_object();
		$content = apply_filters( 'bbgi_gallery_cotnent', false, $post, $ids );
		if ( ! empty( $content ) ) {
			return $content;
		}

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

	/**
	 * Gets an array of ids for the attachments in the gallery
	 * @param $post
	 * @return Array
	 */
	public function get_attachment_ids_for_post( $post ) {
		$ids = array();

		$post = get_post( $post );
		// echo "<pre>", print_r($post). "</pre>";
		if( $post->post_type !== 'gmr_gallery' ) {
			return null;
		}
		
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
}
