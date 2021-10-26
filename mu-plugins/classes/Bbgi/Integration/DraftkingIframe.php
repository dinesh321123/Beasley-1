<?php
/**
 * Sets up settings page and shortcode for Second Street
 */

namespace Bbgi\Integration;

class DraftkingIframe extends \Bbgi\Module {

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		$iframe_height =  get_option( 'configurable_iframe_height', '0' );
		if ( ! empty( $iframe_height ) ) :
			add_filter( 'the_content', $this( 'render_draftking' ) );
		endif;
	}

	public function render_draftking( $content ) {
		$IframePostType = 1;
		$current_post_object = get_queried_object();
		$embed = '';
		
		if( !empty($current_post_object) )
		{
			$hide_draftking_iframe = get_field( 'hide_draftking_iframe', $current_post_object );
			if ( isset( $hide_draftking_iframe ) && $hide_draftking_iframe == 0 && !is_front_page() ) :
				$IframePostType = 0;
			endif;

			$embed = sprintf(
				'<div class="draftking-iframe" data-postid="%s" data-ishidden="%s"></div>',
				esc_attr( $current_post_object->ID ),
				esc_attr( $IframePostType )
			);
		}
		
		$content .= $embed;
		return $content;
	}
}
