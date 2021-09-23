<?php
/**
 * Sets up settings page and shortcode for Second Street
 */

namespace Bbgi\Integration;

class Drimify extends \Bbgi\Module {

	// track index of the app
	private static $total_index = 0;

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		// add shortcodes
		add_shortcode( 'drimify', $this( 'render_shortcode' ) );
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
			'app_url' => '',
			'app_style' => ''
		), $atts, 'drimify' );

		if($attributes['app_url'] != '' && false === stripos( $attributes['app_url'], 'https://go.drimify.com' )) {
			return;
		}

		$embed = sprintf(
			'<div class="drimify" data-app_url="%s" data-app_style="%s"></div>',
			esc_attr( $attributes['app_url']),
			esc_attr( $attributes['app_style'])
		);
		
		self::$total_index = self::$total_index + 1;
		$attributes['total_index'] = self::$total_index;

		return apply_filters( 'drimify_html', $embed, $attributes );
	}

}
