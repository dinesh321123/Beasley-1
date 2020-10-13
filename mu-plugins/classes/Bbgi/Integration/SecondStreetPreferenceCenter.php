<?php
/**
 * Sets up settings page and shortcode for Second Street
 */

namespace Bbgi\Integration;

class SecondStreetPreferenceCenter extends \Bbgi\Module {

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		// add shortcodes
		add_shortcode( 'ss-preferences', $this( 'render_shortcode' ) );
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
			'organization_id' => ''
		), $atts, 'ss-preferences' );

		if ( empty( $attributes['organization_id'] ) ) {
			return '';
		}

		$embed = sprintf(
			'<div class="secondstreet-prefcenter" data-orgid="%s"></div>',
			esc_attr( $attributes['organization_id'] )
		);

		return apply_filters( 'secondstreetpref_html', $embed, $attributes );
	}

}
