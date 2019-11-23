<?php
/**
 * Module responsible for managing redirects sitewide
 *
 * @package Bbgi
 */

namespace Bbgi;

use SRM_Redirect;

class Redirects extends \Bbgi\Module {

	/**
	 * Caches the found redirects in a given request
	 *
	 * @var array
	 */
	protected $redirects_map = [];

	/**
	 * Register actions and hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_filter( 'post_link', [ $this, 'expand_redirect' ] );
		add_filter( 'page_link', [ $this, 'expand_redirect' ] );
		add_filter( 'post_type_link', [ $this, 'expand_redirect' ] );
		add_filter( 'category_link', [ $this, 'expand_redirect' ] );
		add_filter( 'author_link', [ $this, 'expand_redirect' ] );
		add_filter( 'day_link', [ $this, 'expand_redirect' ] );
		add_filter( 'month_link', [ $this, 'expand_redirect' ] );
		add_filter( 'year_link', [ $this, 'expand_redirect' ] );
		add_filter( 'nav_menu_link_attributes', [ $this, 'expand_nav_menu_links_redirects' ] );

		// maybe expand links in the_content
	}

	/**
	 * Tries to match a url for a redirect
	 *
	 * @see https://github.com/10up/safe-redirect-manager/blob/develop/inc/classes/class-srm-redirect.php#L58
	 *
	 * @param string $url The url we're checking.
	 *
	 * @return mixed
	 */
	public function match_redirect( $url ) {
		$redirects = srm_get_redirects();

		// If we have no redirects, there is no need to continue
		if ( empty( $redirects ) ) {
			return false;
		}

		$requested_path = untrailingslashit( stripslashes( $url ) );

		/**
		 * If WordPress resides in a directory that is not the public root, we have to chop
		 * the pre-WP path off the requested path.
		 */
		if ( function_exists( 'wp_parse_url' ) ) {
			$parsed_home_url = wp_parse_url( home_url() );
		} else {
			$parsed_home_url = parse_url( home_url() );
		}

		if ( isset( $parsed_home_url['path'] ) && '/' !== $parsed_home_url['path'] ) {
			$requested_path = preg_replace( '@' . $parsed_home_url['path'] . '@i', '', $requested_path, 1 );
		}
		if ( empty( $requested_path ) ) {
			$requested_path = '/';
		}

		// Allow redirects to be filtered
		$redirects = apply_filters( 'srm_registered_redirects', $redirects, $requested_path );

		// Allow for case insensitive redirects
		$case_insensitive = apply_filters( 'srm_case_insensitive_redirects', true );

		if ( $case_insensitive ) {
			$regex_flag = 'i';
			// normalized path is used for matching but not for replace
			$normalized_requested_path = strtolower( $requested_path );
		} else {
			$regex_flag                = '';
			$normalized_requested_path = $requested_path;
		}

		foreach ( (array) $redirects as $redirect ) {
			$redirect_from = untrailingslashit( $redirect['redirect_from'] );
			if ( empty( $redirect_from ) ) {
				$redirect_from = '/'; // this only happens in the case where there is a redirect on the root
			}
			$redirect_to  = $redirect['redirect_to'];
			$status_code  = $redirect['status_code'];
			$enable_regex = ( isset( $redirect['enable_regex'] ) ) ? $redirect['enable_regex'] : false;
			// check if the redirection destination is valid, otherwise just skip it
			if ( empty( $redirect_to ) ) {
				continue;
			}

			// check if requested path is the same as the redirect from path
			if ( $enable_regex ) {
				$matched_path = preg_match( '@' . $redirect_from . '@' . $regex_flag, $requested_path );
			} else {
				if ( $case_insensitive ) {
					$redirect_from = strtolower( $redirect_from );
				}
				$matched_path = ( $normalized_requested_path === $redirect_from );

				// check if the redirect_from ends in a wildcard
				if ( ! $matched_path && ( strrpos( $redirect_from, '*' ) === strlen( $redirect_from ) - 1 ) ) {
					$wildcard_base = substr( $redirect_from, 0, strlen( $redirect_from ) - 1 );
					// Remove the trailing slash from the wildcard base, matching removal from request path.
					$wildcard_base = untrailingslashit( $wildcard_base );
					// Mark as path match if requested path matches the base of the redirect from.
					$matched_path = ( substr( $normalized_requested_path, 0, strlen( $wildcard_base ) ) === $wildcard_base );
					if ( ( strrpos( $redirect_to, '*' ) === strlen( $redirect_to ) - 1 ) ) {
						$redirect_to = rtrim( $redirect_to, '*' ) . ltrim( substr( $requested_path, strlen( $wildcard_base ) ), '/' );
					}
				}
			}

			if ( $matched_path ) {
				/**
				 * Whitelist redirect host
				 */
				if ( function_exists( 'wp_parse_url' ) ) {
					$parsed_redirect = wp_parse_url( $redirect_to );
				} else {
					$parsed_redirect = parse_url( $redirect_to );
				}

				if ( is_array( $parsed_redirect ) && ! empty( $parsed_redirect['host'] ) ) {
					$this->whitelist_host = $parsed_redirect['host'];
					add_filter( 'allowed_redirect_hosts', array( $this, 'filter_allowed_redirect_hosts' ) );
				}

				// Allow for regex replacement in $redirect_to
				if ( $enable_regex ) {
					$redirect_to = preg_replace( '@' . $redirect_from . '@' . $regex_flag, $redirect_to, $requested_path );
				}

				$sanitized_redirect_to = esc_url_raw( apply_filters( 'srm_redirect_to', $redirect_to ) );

				do_action( 'srm_do_redirect', $requested_path, $sanitized_redirect_to, $status_code );

				return $sanitized_redirect_to;
			}

			return false;
		}
	}

	/**
	 * Expand the url if there's a redirect avaliable
	 *
	 * @param string $url The url to be expanded.
	 *
	 * @return string
	 */
	public function expand_redirect( $url ) {
		if ( $this->has_cached_redirect( $url ) ) {
			return $this->get_cached_redirect( $url );
		}

		if ( ! class_exists( 'SRM_Redirect' ) ) {
			return $url;
		}

		$matched_redirect = $this->match_redirect( $url );

		if ( $matched_redirect ) {
			return $matched_redirect;
		}

		return $url;
	}

	/**
	 * Expand nav menu links
	 *
	 * @param array $atts
	 *
	 * @return array
	 */
	public function expand_nav_menu_links_redirects( $atts ) {
		$url = $atts['href'];

		$new_url = $this->match_redirect( $url );

		if ( $new_url ) {
			$atts['href'] = $new_url;
		}

		return $atts;
	}

	/**
	 * Checks if there's a cached redirect for this URL.
	 *
	 * @param string $url The url we're checking.
	 *
	 * @return boolean
	 */
	protected function has_cached_redirect( $url ) {
		return isset( $this->redirects_map[ $url ] );
	}

	/**
	 * Returns the cached redirect if any.
	 *
	 * @param string $url The url we're checking.
	 *
	 * @return string
	 */
	protected function get_cached_redirect( $url ) {
		if ( $this->has_cached_redirect( $url ) ) {
			return $this->redirects_map[ $url ];
		}

		return $url;
	}

}
