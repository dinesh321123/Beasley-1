<?php
/**
 * Sets up settings page and shortcode for Second Street
 */

namespace Bbgi\Integration;

class SecondStreetContest extends \Bbgi\Module {

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		// add shortcodes
		add_shortcode( 'ss-contest', $this( 'render_shortcode' ) );
	}

	/**
	 * Renders ss-contest shortcode.
	 *
	 * @access public
	 * @param array $attributes Array of shortcode arguments.
	 * @return string Shortcode markup.
	 */
	public function render_shortcode( $atts ) {
		$attributes = shortcode_atts( array(
			'contest_url' => '',
			'routing' => '',
			'contest_id' => ''
		), $atts, 'ss-contest' );
		
		if ( empty( $attributes['contest_url'] )  || empty( $attributes['contest_id'] ) || empty( $attributes['routing'] ) ) {
			return '';
		}

		$embed = sprintf(
			'<div class="secondstreet-contest" data-contest_url="%s" data-routing="%s" data-contest_id="%s"></div>',
			esc_attr( $attributes['contest_url'] ),
			esc_attr( $attributes['routing'] ),
			esc_attr( $attributes['contest_id'] )
		);

		return apply_filters( 'secondstreetcontest_html', $embed, $attributes );
	}

}
