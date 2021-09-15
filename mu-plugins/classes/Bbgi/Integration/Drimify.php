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

		if( empty( $attributes['app_url'] ) ) {
			$attributes['app_url'] = $this->extract_app_link( $atts );
		}

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

	/**
	 * Extract APP URL if not sent as text.
	 *
	 * @access public
	 * @param array $attributes Array of shortcode arguments.
	 * @return string App URL string.
	 */
	public function extract_app_link( $atts ) {
		$atts = (array) $atts;

		if(!empty( $atts['href'] )) {
			return $atts['href'];
		}

		$string_with_app_url = "";
		if(false !== stripos( $atts[0], 'href' )) {
			$string_with_app_url = $atts[0];
		}
		else if(false !== stripos( $atts[1], 'href' )) {
			$string_with_app_url = $atts[1];
		}

		if( !empty($string_with_app_url) ) {
			$link_available = "";
			$parts = explode( '"', $string_with_app_url );
	
			foreach($parts as $part) {
				if (stripos($part, 'https://go.drimify.com') !== false && stripos($part, 'https://go.drimify.com') == 0) {
					$link_available = trim($part);
					break;
				}
			}

			if($link_available) {
				return $link_available;
			}
		}
	}
}
