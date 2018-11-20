<?php

namespace Bbgi;

class Site extends \Bbgi\Module {

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		add_action( 'send_headers', $this( 'send_headers' ) );
	}

	/**
	 * Sends additional headers.
	 *
	 * @access public
	 * @action send_headers
	 */
	public function send_headers() {
		/**
		 * General headers
		 */

		header( 'X-UA-Compatible: IE=edge' );

		/**
		 *  Security headers (https://securityheaders.com/)
		 */

		// @see: https://scotthelme.co.uk/hardening-your-http-response-headers/#x-frame-options
		header( 'X-Frame-Options: SAMEORIGIN' );
		// @see: https://scotthelme.co.uk/hardening-your-http-response-headers/#x-xss-protection
		header( 'X-XSS-Protection: 1; mode=block' );
		// @see: https://scotthelme.co.uk/hardening-your-http-response-headers/#x-content-type-options
		header( 'X-Content-Type-Options: nosniff' );
		// @see: https://scotthelme.co.uk/a-new-security-header-referrer-policy/
		header( 'Referrer-Policy: origin-when-cross-origin' ); // do not chance, it may prevent omny player to work properly
		// @see: https://scotthelme.co.uk/a-new-security-header-feature-policy/
		header( "Feature-Policy: accelerometer 'none'; camera 'none'; geolocation 'none'; gyroscope 'none'; magnetometer 'none'; microphone 'none'; payment 'none'; usb 'none'" );
	}

}
