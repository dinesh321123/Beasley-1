<?php
/**
 * Sets up settings page and shortcode for Second Street
 */

namespace Bbgi\Integration;

class SecondStreetSignup extends \Bbgi\Module {

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		// add shortcodes
		add_shortcode( 'ss-signup', $this( 'render_shortcode' ) );
	}

	/**
	 * Renders ss-signup shortcode.
	 *
	 * @access public
	 * @param array $attributes Array of shortcode arguments.
	 * @return string Shortcode markup.
	 */
	public function render_shortcode( $atts ) {
		$attributes = shortcode_atts( array(
			'design_id' => ''
		), $atts, 'ss-signup' );

		if ( empty( $attributes['design_id'] ) ) {
			return '';
		}

		$embed = sprintf(
			'<div class="secondstreet-signup" data-designid="%s"></div>',
			esc_attr( $attributes['design_id'] )
		);

		return apply_filters( 'secondstreetsignup_html', $embed, $attributes );
	}

}
