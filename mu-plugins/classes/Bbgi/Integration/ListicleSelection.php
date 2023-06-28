<?php
/**
 * Sets up settings page and shortcode for Second Street
 */

namespace Bbgi\Integration;

use Bbgi\Util;

class ListicleSelection extends \Bbgi\Module {
	use Util;

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		// add shortcodes
		add_shortcode( 'select-listicle', $this( 'render_shortcode' ) );
	}

	/**
	 * Renders select-listicle shortcode.
	 *
	 * @access public
	 * @param array $atts Array of shortcode arguments.
	 * @return string Shortcode markup.
	 */
	public function render_shortcode( $atts ) {
		global $cpt_embed_flag;
		$post_id = get_the_ID();

		if( !empty($cpt_embed_flag) && $cpt_embed_flag[$post_id] ) {  // Check for the source post already have embed
			return '';
		}

		$attributes = shortcode_atts( array(
			'listicle_id' => '',
			'syndication_name' => '',
			'description' => ''
		), $atts, 'select-listicle' );

		$post_object = get_queried_object();

		$listicle_id = $this->getObjectId( $post_object->post_type, "listicle_cpt", $attributes['listicle_id'], $attributes['syndication_name'] );
		if(empty($listicle_id)) {
			return;
		}

		$cpt_post_object = $this->verify_post( $listicle_id, "listicle_cpt", $attributes['syndication_name'] );
		if( empty($cpt_post_object) ) {
			return;
		}

		$cpt_item_name = (array) $this->get_post_metadata_from_post( 'cpt_item_name', $cpt_post_object );
		$cpt_item_description = (array) $this->get_post_metadata_from_post( 'cpt_item_description', $cpt_post_object );
		$cpt_item_order = (array) $this->get_post_metadata_from_post( 'cpt_item_order', $cpt_post_object );
		$cpt_item_type 	= (array) $this->get_post_metadata_from_post( 'cpt_item_type', $cpt_post_object );

		remove_filter( 'the_content', 'ee_add_ads_to_content', 100 );
		$content = apply_filters( 'bbgi_listicle_content', $cpt_post_object, $cpt_item_name, $cpt_item_description, $cpt_item_order, $cpt_item_type, $post_object );
		add_filter( 'the_content', 'ee_add_ads_to_content', 100 );
		$content_updated = '';
		if ( ! empty( $content ) ) {
			$content_updated .= ee_render_trending_articles('embed_listicle');
			$content_updated .= "<h2 class=\"section-head\"><span>".$cpt_post_object->post_title."</span></h2>";
			if( !empty( $attributes['description'] ) &&  ($attributes['description'] == 'yes') ) {
				remove_filter( 'the_content', 'ee_add_ads_to_content', 100 );
				$the_content = apply_filters('the_content', $cpt_post_object->post_content);
				add_filter( 'the_content', 'ee_add_ads_to_content', 100 );
				if ( !empty($the_content) ) {
					$content_updated .= "<div class=\"listicle-embed-description\">".$the_content."</div>";
				}
			}

			$content_updated .= $this->stringify_selected_cpt( $content, "LISTICLE" );
			$cpt_embed_flag[$post_id] = true;
			return $content_updated;
		}

		return $content;
	}

}
