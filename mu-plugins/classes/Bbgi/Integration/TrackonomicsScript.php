<?php
/**
 * Add post type into content for Trackonomics Script
 */

namespace Bbgi\Integration;

class TrackonomicsScript extends \Bbgi\Module {

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		add_filter( 'the_content', $this( 'render_trackonomics_script' ) );
	}

	public function render_trackonomics_script( $content ) {
		// Add condition to add home, Category and tag section 
		$current_queried_post_type	= get_post_type( get_queried_object_id() );
		$current_post_object		= get_queried_object();
		
		
		$validPostTypeArray	= (array) apply_filters( 'trackonomics-script-valid-post-types', array( 'affiliate_marketing' )  );
		// $trackonomicsScript	= 0;
		$trackonomicsScript	= in_array( $current_queried_post_type, $validPostTypeArray ) ? 1 : 0 ;
		/* if( in_array( $current_queried_post_type, $validPostTypeArray ) ) {
			$trackonomicsScript = 1;
		} */

		if ( has_shortcode( $current_post_object->post_content, 'select-am' ) ) {
			$trackonomicsScript = 1;	
		}

		$embed = sprintf(
			'<div class="trackonomics-script" data-postid="%s" data-posttype="%s" data-trackonomicsscript="%s"></div>',
			esc_attr( $current_post_object->ID ),
			esc_attr( $current_queried_post_type ),
			esc_attr( $trackonomicsScript )
		);

		$content .= $embed;
		return $content;
	}
}
