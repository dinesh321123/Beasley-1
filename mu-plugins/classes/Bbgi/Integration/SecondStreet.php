<?php

namespace Bbgi\Integration;

class SecondStreet extends \Bbgi\Module {

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		// add action hooks
		add_action( 'bbgi_register_settings', $this( 'register_settings' ), 10, 2 );

		// add shortcodes
		add_shortcode( 'ss_promo', $this( 'render_shortcode' ) );
		add_shortcode( 'ss-promo', $this( 'render_shortcode' ) );
	}

	/**
	 * Registers Google Analytics and Tag Manager settings.
	 *
	 * @access public
	 * @action bbgi_register_settings
	 * @param string $group
	 * @param string $page
	 */
	public function register_settings( $group, $page ) {
		$section_id = 'beasley_secondstreet_settings';

		add_settings_section( $section_id, 'SecondStreet', '__return_false', $page );
		add_settings_field( 'secondstreet_station_id', 'Station ID', 'bbgi_input_field', $page, $section_id, 'name=secondstreet_station_id' );
		register_setting( $group, 'secondstreet_station_id', 'sanitize_text_field' );
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
			'stationid' => '',
			'op_id'     => '',
			'op_guid'   => '',
			'routing'   => ''
		), $atts, 'ss-promo' );

		if ( empty( $attributes['op_id'] ) || empty( $attributes['op_guid'] ) ) {
			return '';
		}

		if ( ! empty( $attributes['stationid'] ) && get_option( 'secondstreet_station_id' ) != $attributes['stationid'] ) {
			return '';
		}

		$embed = sprintf(
			'<div class="secondstreet-embed" src="https://embed-%s.secondstreetapp.com/Scripts/dist/embed.js" data-ss-embed="promotion" data-opguid="%s" data-routing="%s"></div>',
			esc_attr( $attributes['op_id'] ),
			esc_attr( $attributes['op_guid'] ),
			esc_attr( $attributes['routing'] )
		);

		return apply_filters( 'secondstreet_embed_html', $embed, $attributes );
	}

}
