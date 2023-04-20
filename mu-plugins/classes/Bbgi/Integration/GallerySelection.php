<?php
/**
 * Sets up settings page and shortcode for Second Street
 */

namespace Bbgi\Integration;

use Bbgi\Util;

class GallerySelection extends \Bbgi\Module {
	use Util;

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

	private function stringify_selected_gallery($contentVal)
	{
		if (is_array($contentVal) || is_object($contentVal)) {
			if (WP_DEBUG) {
				error_log('WARNING: GALLERY CONTENT IS AN OBJECT OR ARRAY: ');
				error_log(print_r($contentVal, true));
			}
			if( is_object($contentVal) && isset($contentVal->post_content) ) {
				return $contentVal->post_content;
			}
			return print_r($contentVal, true);
		} else {
			return $contentVal;
		}
	}

	/**
	 * Renders ss-promo shortcode.
	 *
	 * @access public
	 * @param array $attributes Array of shortcode arguments.
	 * @return string Shortcode markup.
	 */
	public function render_shortcode( $atts ) {
		global $cpt_embed_flag, $cpt_embed_ids;
		$post_id = get_the_ID();

		// echo "<pre>", "FROM Gallery: Post ID: ".$post_id, "</pre>";
		if( !empty($cpt_embed_flag) && $cpt_embed_flag[$post_id] ) {  // Check for the source post already have embed
			// echo "<pre>", "This post is already have one embed", "</pre>";
			return '';
		}

		if( !empty($cpt_embed_ids) && in_array($post_id, $cpt_embed_ids) ) {  // Check if the post is already embed in other post
			// echo "<pre>", "This post is already embedded", "</pre>";
			return '';
		}

		$attributes = shortcode_atts( array(
			'gallery_id' => '',
			'syndication_name' => '',
			'description' => ''
		), $atts, 'select-gallery' );

		$post = get_queried_object();
		if ( $this->is_future_date($post->post_type) ) {
			return;
		}

		if( !empty( $attributes['syndication_name'] ) ) {
			$listicle_id = $this->check_embedded_id( 'gmr_gallery', $attributes['syndication_name'] );
		}

		if(empty($gallery_id) && !empty( $attributes['gallery_id'] ) && !empty( get_post( $attributes['gallery_id'] ) ) ) {
			$gallery_id = $attributes['gallery_id'];
		}

		if(empty($gallery_id)) {
			return;
		}

		$ids = $this->get_attachment_ids_for_post( $gallery_id, $attributes['syndication_name'] );

		$gallery_object = get_post( $gallery_id );
		$content = apply_filters( 'bbgi_gallery_content', false, $gallery_object, $ids, $post );
		if ( ! empty( $content ) ) {
			$content_updated = "<h2 class=\"section-head\"><span>".$gallery_object->post_title."</span></h2>";
			if( !empty( $attributes['description'] ) &&  ($attributes['description'] == 'yes') ) {
				remove_filter( 'the_content', 'ee_add_ads_to_content', 100 );
				$the_content = apply_filters('the_content', $gallery_object->post_content);
				add_filter( 'the_content', 'ee_add_ads_to_content', 100 );
				if ( !empty($the_content) ) {
					$content_updated .= "<div class=\"gallery-embed-description\">".$the_content."</div>";
				}
			}
			$content_updated .= $this->stringify_selected_gallery($content);
			// echo "<pre>", "Gallery updating embed flag", "</pre>";
			$cpt_embed_flag[$post_id] = true;
			return $content_updated;
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

		$content_updated = "<h2 class=\"section-head\"><span>".$gallery_object->post_title."</span></h2>";
		if( !empty( $attributes['description'] ) &&  ($attributes['description'] == 'yes') ) {
			$the_content = apply_filters('the_content', $gallery_object->post_content);
			if ( !empty($the_content) ) {
				$content_updated .= "<div class=\"gallery-embed-description\">".$the_content."</div>";
			}
		}
		$content_updated .= $this->stringify_selected_gallery($content);
		// echo "<pre>", "Gallery updating embed flag", "</pre>";
		$cpt_embed_flag[$post_id] = true;
		return $content_updated;
	}

	/**
	 * Gets an array of ids for the attachments in the gallery
	 * @param $post
	 * @return Array
	 */
	public function get_attachment_ids_for_post( $post, $syndication_name ) {
		$ids = array();

		$post = get_post( $post );
		if( $post->post_type !== 'gmr_gallery' || $post->post_name !== $syndication_name ) {
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

		return !empty( $ids[ $post->ID ] )
			? $ids[ $post->ID ]
			: null;
	}
}
