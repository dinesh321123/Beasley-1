<?php
/**
 * Sets up settings page and shortcode for DML Branded Content
 */

namespace Bbgi\Integration;

class BrandedContent extends \Bbgi\Module {

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		// add shortcodes
		add_shortcode( 'dml-branded', $this( 'render_shortcode' ) );
	}

	/**
	 * Renders dml-branded shortcode.
	 *
	 * @access public
	 * @param array $attributes Array of shortcode arguments.
	 * @return string Shortcode markup.
	 */
	public function render_shortcode( $atts ) {
		$attributes = shortcode_atts( array(
			'stackid' => '',
			'layout' => ''
		), $atts, 'dml-branded' );

		if ( empty( $attributes['stackid'] ) ) {
			return '';
		}

		$embed = sprintf(
			'<div class="dmlbranded" data-stackid="%s" data-layout="%s"></div>',
			esc_attr( $attributes['stackid'] ),
			esc_attr( $attributes['layout'] )
		);

		return apply_filters( 'dml-branded_html', $embed, $attributes );
	}

}
