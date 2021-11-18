<?php
/**
 * Sets up settings page and shortcode for Second Street
 */

namespace Bbgi\Integration;

class ListicleSelection extends \Bbgi\Module {

	// track index of the app
	private static $total_index = 0;

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
	 * Renders ss-promo shortcode.
	 *
	 * @access public
	 * @param array $attributes Array of shortcode arguments.
	 * @return string Shortcode markup.
	 */
	public function render_shortcode( $atts ) {
		$attributes = shortcode_atts( array(
			'listicle_id' => '',
			'syndication_name' => ''
		), $atts, 'select-listicle' );

		if( !empty( $attributes['syndication_name'] ) ) {
			$meta_query_args = array(
				'meta_key'    => 'syndication_old_name',
				'meta_value'  => $attributes['syndication_name'],
				'post_status' => 'any',
				'post_type'   => 'listicle_cpt'
			);
	
			$existing = get_posts( $meta_query_args );

			if ( !empty( $existing ) ) {
				$existing_post = current( $existing );
				$listicle_id = intval( $existing_post->ID );
			}
		}

		if(empty($listicle_id) && !empty( $attributes['listicle_id'] ) && !empty( get_post( $attributes['listicle_id'] ) ) ) {
			$listicle_id = $attributes['listicle_id'];
		}

		if(empty($listicle_id)) {
			return;
		}
		
		$post_object = get_queried_object();

		$cpt_post_object = $this->verify_post( $listicle_id, $attributes['syndication_name'] );
		$cpt_item_name = $this->get_post_metadata_from_post( 'cpt_item_name', $cpt_post_object );
		if ( ! is_array( $cpt_item_name ) ) {
			$cpt_item_name = array();
		}
		$cpt_item_description = $this->get_post_metadata_from_post( 'cpt_item_description', $cpt_post_object );
		if ( ! is_array( $cpt_item_description ) ) {
			$cpt_item_description = array();
		}
		$cpt_item_order = $this->get_post_metadata_from_post( 'cpt_item_order', $cpt_post_object );
		if ( ! is_array( $cpt_item_order ) ) {
			$cpt_item_order = array();
		}

		$content = apply_filters( 'bbgi_listicle_cotnent', $cpt_post_object, $cpt_item_name, $cpt_item_description, $cpt_item_order, $post_object );
		if ( ! empty( $content ) ) {
			return $content;
		}

		return $content;
	}

	/**
	 * Gets an array of meta data for the Affiliate Marketing
	 * @param $post
	 * @return Array
	 */
	function get_post_metadata_from_post( $value, $post ) {
		$field = get_post_meta( $post->ID, $value, true );
		
		if ( ! empty( $field ) ) {
            return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
        } else {
            return false;
        }
	}

	/**
	 * Verify post is valid or not
	 * @param $post
	 * @return Array
	 */
	public function verify_post( $post, $syndication_name ) {
		$ids = array();

		$post = get_post( $post );
		if( $post->post_type !== 'listicle_cpt' || $post->post_name !== $syndication_name ) {
			return null;
		}
		
		if ( !empty( $post ) ) {
			return $post;
		}
		return null;
	}
}