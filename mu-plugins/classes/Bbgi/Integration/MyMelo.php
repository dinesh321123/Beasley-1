<?php

namespace Bbgi\Integration;

class MyMelo extends \Bbgi\Module {

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		// add action hooks
		add_action( 'bbgi_register_settings', $this( 'register_settings' ), 10, 2 );

		// add shortcodes
		add_shortcode( 'mymelo', $this( 'render_shortcode' ) );

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
		$section_id = 'beasley_mymelo_settings';

		add_settings_section( $section_id, 'MyMelo', '__return_false', $page );
		add_settings_field( 'mymelo_station_id', 'Station ID', 'bbgi_input_field', $page, $section_id, 'name=mymelo_station_id' );
		add_settings_field( 'mymelo_station_logo', 'Station Logo', 'bbgi_input_field', $page, $section_id, 'name=mymelo_station_logo' );

		register_setting( $group, 'mymelo_station_id', 'sanitize_text_field' );
		register_setting( $group, 'mymelo_station_logo', 'sanitize_text_field' );
	}

	/**
	 * Renders ss-promo shortcode.
	 *
	 * @access public
	 * @param array $attributes Array of shortcode arguments.
	 * @return string Shortcode markup.
	 */
	public function render_shortcode( $atts ) {

		if ( empty(get_option( 'mymelo_station_id' )) or empty( get_option( 'mymelo_station_logo' )) ) {
			return '';
		}


		$stationid = esc_attr(get_option( 'mymelo_station_id' ));
		$stationlogo = esc_attr(get_option( 'mymelo_station_logo' ));


		$embed = <<<EOL
		<script>
			var mymeloSetup = {
				id: '{$stationid}',
				button: '#mymelo-button',
				logo: '{$stationlogo}',
				header: {
					primary: '#foo',
					secondary: '#foo',
				},
				div: {
					selector: '#mymelo-div',
					width: '100%',
					height: '850px', // Configure widget height
				},
			};
		</script>
		<div id="mymelo-div" style="height: 850px"></div>
		<script src="https://platform.mymelo.com/client/widget.js" defer></script>
EOL;

		return $embed;
	}
}
