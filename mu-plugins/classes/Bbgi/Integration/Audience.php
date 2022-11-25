<?php
/**
 * Sets up settings page and shortcode for Audience.io
 */

namespace Bbgi\Integration;

class Audience extends \Bbgi\Module {

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		// add shortcodes
		add_shortcode( 'audience_promo', $this( 'audience_render_shortcode' ) );
		add_shortcode( 'audience-promo', $this( 'audience_render_shortcode' ) );
	}

	/**
	 * Renders audience-promo shortcode.
	 *
	 * @access public
	 * @param array $attributes Array of shortcode arguments.
	 * @return string Shortcode markup.
	 */
	public function audience_render_shortcode( $atts ) {
		$attributes = shortcode_atts( array(
			'widget-id' => ''
		), $atts, 'audience-promo' );

		if ( empty( $attributes['widget-id'] ) ) {
			return '';
		}

		$embed = sprintf(
			'<div class="audience-embed" data-widgetid="%s"></div>',
			esc_attr( $attributes['widget-id'] )
		);

		return apply_filters( 'audience_embed_html', $embed, $attributes );
	}

}
