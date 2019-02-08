<?php

namespace Bbgi\Integration;

class Facebook extends \Bbgi\Module {

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		add_action( 'bbgi_register_settings', $this( 'register_settings' ), 10, 2 );
		add_action( 'wp_head', $this( 'display_pixel_script' ) );
		add_action( 'beasley_after_body', $this( 'display_pixel_noscript' ) );
	}

	/**
	 * Registers Facebook settings.
	 *
	 * @access public
	 * @action bbgi_register_settings
	 * @param string $group
	 * @param string $page
	 */
	public function register_settings( $group, $page ) {
		$section_id = 'beasley_facebook';

		add_settings_section( $section_id, 'Facebook', '__return_false', $page );
		add_settings_field( 'bbgi_facebook_pixel_id', 'Facebook Pixel ID', 'bbgi_input_field', $page, $section_id, 'name=bbgi_facebook_pixel_id' );
		register_setting( $group, 'bbgi_facebook_pixel_id', 'sanitize_text_field' );
	}

	/**
	 * Displays Facebook pixel script.
	 * 
	 * @access public
	 * @action wp_head
	 */
	public function display_pixel_script() {
		$pixel_id = trim( get_option( 'bbgi_facebook_pixel_id' ) );
		if ( ! empty( $pixel_id ) ) {
			printf(
				"<script>
				!function(f,b,e,v,n,t,s)
				{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
				n.callMethod.apply(n,arguments):n.queue.push(arguments)};
				if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
				n.queue=[];t=b.createElement(e);t.async=!0;
				t.src=v;s=b.getElementsByTagName(e)[0];
				s.parentNode.insertBefore(t,s)}(window, document,'script',
				'https://connect.facebook.net/en_US/fbevents.js');
				fbq('init', '%s');
				fbq('track', 'PageView');
				</script>",
				esc_js( $pixel_id )
			);
		}
	}

	/**
	 * Displays Faceboook pixel noscript.
	 * 
	 * @access public
	 * @action beasley_after_body
	 */
	public function display_pixel_noscript() {
		$pixel_id = trim( get_option( 'bbgi_facebook_pixel_id' ) );
		if ( ! empty( $pixel_id ) ) {
			printf(
				'<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=%s&ev=PageView&noscript=1"></noscript>',
				urlencode( $pixel_id )
			);
		}
	}

}
