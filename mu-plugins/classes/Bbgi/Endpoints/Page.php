<?php
/**
 * Page endpoint for the hybrid react implementation
 *
 * @package Bbgi
 */
namespace Bbgi\Endpoints;

use Bbgi\Module;
use Bbgi\Util;

class Page extends Module {
	use Util;

	/**
	 * Register the custom rest endpoint
	 *
	 * @return void
	 */
	public function register() {

		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Register our custom routes.
	 *
	 * @return void
	 */
	public function register_routes() {
	;
		register_rest_route(
			'experience_engine/v1',
			'page',
			[
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => [ $this, 'get_page' ],
				'args' => [
					'url'       => [
						'type'     => 'string',
						'required' => true,
					],
				]
			]
		);
	}

	/**
	 * Fetches a page.
	 *
	 * @param string $url The URL to be fetched.
	 *
	 * @return string|false
	 */
	public function fetch_page( $url ) {
		$page_response = wp_remote_get(
			$url, [
				'timeout'   => 30,
				'sslverify' => defined( 'WP_DEBUG') && WP_DEBUG ? false : true,
				'headers'=> ['Cache-Tag' => 'testing',
				'X-Cache-BBGI-Tag' => 'testing for API']
			]
		);

		if ( is_wp_error( $page_response ) ) {
			return false;
		}

		if ( ! in_array( $page_response['response']['code'], [ 200, 201 ] ) ) {
			return false;
		}

		return $page_response;
	}

	/**
	 * Fetches a page for react.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function get_page( \WP_REST_Request $request ) {
		$url = $this->is_absolute_url( $request->get_param( 'url' ) ) ? $request->get_param( 'url' ) : home_url( $request->get_param( 'url' ) );

		$response = [
			'status'    => '',
			'redirect'  => [
				'internal' => false,
				'url'      => '',
			],
			'html'      => false,
			'headers' => [
				'X-Cache-BBGI-Tag' => 'API TEsting',
				'Cache-Tag' => 'testing'
			],
		];
		if ( ! $this->is_internal_url( $url ) ) {
			$response['status'] = 403;

			return $response;
		}

		/**
		 * @var \Bbgi\Redirects $redirects
		 */
		$redirects = self::get( 'redirects' );

		$matched_redirect = $redirects->match_redirect( $url );

		if ( $matched_redirect ) {
			$is_absolute = $this->is_absolute_url( $matched_redirect );

			$response['redirect']['url']      = $is_absolute ? $matched_redirect : home_url( $matched_redirect );
			$response['redirect']['internal'] = ! $is_absolute;
			$response['status']               = 301;
			$response['header']['X-Cache-BBGI-Tag'] = 'testing for API';
			$response['headers']['X-Cache-BBGI-Tag'] = 'testing for API1';
			print_r( wp_remote_retrieve_body( $response['header'] ));
		}

		// only fetch page if there's no redirect
		if ( ! $matched_redirect ) {
			$page_response = $this->fetch_page( $url );
			print_r( wp_remote_retrieve_headers( $page_response ));
			$response['html']   = wp_remote_retrieve_body( $page_response );
			$response['status'] = $page_response['response']['code'];
			$response['headers'] = ['Cache-Tag' => 'testing',
			'X-Cache-BBGI-Tag' => 'testing for API'];
		}

		return rest_ensure_response( $response );
	}
}
