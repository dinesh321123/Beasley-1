<?php
/**
 * Sets up settings page and shortcode for Second Street
 */

namespace Bbgi\Integration;

class AffiliateMarketingSelection extends \Bbgi\Module {

	// track index of the app
	private static $total_index = 0;

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		// add shortcodes
		add_shortcode( 'select-am', $this( 'render_shortcode' ) );
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
			'am_id' => '',
			'syndication_name' => ''
		), $atts, 'select-am' );

		if( !empty( $attributes['syndication_name'] ) ) {
			$meta_query_args = array(
				'meta_key'    => 'syndication_old_name',
				'meta_value'  => $attributes['syndication_name'],
				'post_status' => 'any',
				'post_type'   => 'affiliate_marketing'
			);
	
			$existing = get_posts( $meta_query_args );

			if ( !empty( $existing ) ) {
				$existing_post = current( $existing );
				$am_id = intval( $existing_post->ID );
			}
		}

		if(empty($am_id) && !empty( $attributes['am_id'] ) && !empty( get_post( $attributes['am_id'] ) ) ) {
			$am_id = $attributes['am_id'];
		}

		if(empty($am_id)) {
			return;
		}
		
		$post_object = get_queried_object();

		$affiliatemarketing_post_object = $this->verify_post( $am_id, $attributes['syndication_name'] );

		$am_item_name 			=	$this->get_post_metadata_from_post( 'am_item_name', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_name ) ) {
			$am_item_name = array();
		}
		$am_item_description 	= $this->get_post_metadata_from_post( 'am_item_description', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_description ) ) {
			$am_item_description = array();
		}
		$am_item_photo 			= $this->get_post_metadata_from_post( 'am_item_photo', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_photo ) ) {
			$am_item_photo = array();
		}
		$am_item_imagetype 		= $this->get_post_metadata_from_post( 'am_item_imagetype', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_imagetype ) ) {
			$am_item_imagetype = array();
		}
		$am_item_imagecode 		= $this->get_post_metadata_from_post( 'am_item_imagecode', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_imagecode ) ) {
			$am_item_imagecode = array();
		}
		$am_item_order 			= $this->get_post_metadata_from_post( 'am_item_order', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_order ) ) {
			$am_item_order = array();
		}
		$am_item_unique_order 	= $this->get_post_metadata_from_post( 'am_item_unique_order', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_unique_order ) ) {
			$am_item_oram_item_unique_orderder = array();
		}
		$am_item_getitnowtext	= $this->get_post_metadata_from_post( 'am_item_getitnowtext', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_getitnowtext ) ) {
		$am_item_getitnowtext = array();
		}
		$am_item_buttontext 	= $this->get_post_metadata_from_post( 'am_item_buttontext', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_buttontext ) ) {
			$am_item_buttontext = array();
		}
		$am_item_buttonurl 		= $this->get_post_metadata_from_post( 'am_item_buttonurl', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_buttonurl ) ) {
			$am_item_buttonurl = array();
		}
		$am_item_getitnowfromname 	= $this->get_post_metadata_from_post( 'am_item_getitnowfromname', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_getitnowfromname ) ) {
			$am_item_getitnowfromname = array();
		}
		$am_item_getitnowfromurl 	= $this->get_post_metadata_from_post( 'am_item_getitnowfromurl', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_getitnowfromurl ) ) {
			$am_item_getitnowfromurl = array();
		}
		$am_item_type 	= $this->get_post_metadata_from_post( 'am_item_type', $affiliatemarketing_post_object );
		if ( ! is_array( $am_item_type ) ) {
			$am_item_type = array();
		}

		$content = apply_filters( 'bbgi_am_cotnent', $affiliatemarketing_post_object, $am_item_name, $am_item_description, $am_item_photo, $am_item_imagetype, $am_item_imagecode, $am_item_order, $am_item_unique_order, $am_item_getitnowtext, $am_item_buttontext, $am_item_buttonurl, $am_item_getitnowfromname, $am_item_getitnowfromurl, $am_item_type, $post_object );
		if ( ! empty( $content ) ) {
			$content_updated = "<h2 class=\"section-head\"><span>".$affiliatemarketing_post_object->post_title."</span></h2>";
			$the_content = apply_filters('the_content', $affiliatemarketing_post_object->post_content);
			if ( !empty($the_content) ) {
				$content_updated .= "<div class=\"am-embed-description\">".$the_content."</p>";
			}
			$content_updated .= $content."<p>&nbsp;</p><h6><em>Please note that items are in stock and prices are accurate at the time we published this list. Have an idea for a fun theme for a gift idea list you’d like us to create?&nbsp; Drop us a line at <a href=\"mailto:shopping@bbgi.com\" data-uri=\"98cfaf73989c872d3384892acc280543\">shopping@bbgi.com</a>.&nbsp;</em></h6>";
			return $content_updated;
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
		if( $post->post_type !== 'affiliate_marketing' || $post->post_name !== $syndication_name ) {
			return null;
		}
		
		if ( !empty( $post ) ) {
			return $post;
		}
		return null;
	}
}