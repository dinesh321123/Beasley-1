<?php
/**
 * Utility trait
 *
 * @package Bbgi
 */

namespace Bbgi;

trait Util {
	/**
	 * Checks if a URL is absoltue or not
	 *
	 * @param string $url
	 *
	 * @return boolean
	 */
	protected function is_absolute_url( $url ) {
		$pattern = "/^(?:ftp|https?|feed)?:?\/\/(?:(?:(?:[\w\.\-\+!$&'\(\)*\+,;=]|%[0-9a-f]{2})+:)*
		(?:[\w\.\-\+%!$&'\(\)*\+,;=]|%[0-9a-f]{2})+@)?(?:
		(?:[a-z0-9\-\.]|%[0-9a-f]{2})+|(?:\[(?:[0-9a-f]{0,4}:)*(?:[0-9a-f]{0,4})\]))(?::[0-9]+)?(?:[\/|\?]
		(?:[\w#!:\.\?\+\|=&@$'~*,;\/\(\)\[\]\-]|%[0-9a-f]{2})*)?$/xi";

		return (bool) preg_match( $pattern, $url );
	}

	/**
	 * Checks if the provided URL is internal or not.
	 *
	 * @param string $url The URL to check for.
	 *
	 * @return boolean
	 */
	protected function is_internal_url( $url ) {
		$parsed_home_url = parse_url( home_url() );
		$parsed_url      = parse_url( $url );

		return apply_filters(
			'bbgi_page_endpoint_is_internal_url',
			$parsed_home_url['host'] === $parsed_url['host']
		);
	}

	/**
	 * Checks if the date is future date or not.
	 *
	 * @param string $typenow Current queried post object.
	 *
	 * @return boolean
	 */
	protected function is_future_date($typenow) {
		$post_types = array( 'listicle_cpt', 'affiliate_marketing', 'gmr_gallery'  );

		$today = new \DateTime();
		$today = $today->format("Y-m-d");
		$effective_date = new \DateTime("2022-12-06");
		$effective_date = $effective_date->format("Y-m-d");

	   	// If Current Date is Future Date
	   	if (in_array( $typenow, $post_types ) && $today > $effective_date) {
			return true;
	   	}
	   	return false;
	}	
}
