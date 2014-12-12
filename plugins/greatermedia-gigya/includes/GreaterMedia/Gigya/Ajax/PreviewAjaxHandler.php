<?php

namespace GreaterMedia\Gigya\Ajax;

use GreaterMedia\Gigya\AccountsSearcher;
use GreaterMedia\Gigya\MemberQuery;

/**
 * PreviewAjaxHandler is the ajax handler invoked by the client to
 * preview a member query.
 *
 * It receives the client generated GQL query as a parameter. And
 * returns the corresponding user accounts using the Gigya API.
 *
 * DEPRECATED
 *
 * @namespace GreaterMedia\Gigya
 */
class PreviewAjaxHandler extends AjaxHandler {

	/**
	 * The name of the ajax action. Corresponds to the WordPress action,
	 * wp_ajax_preview_member_query.
	 *
	 * @access public
	 * @return string
	 */
	public function get_action() {
		return 'preview_member_query';
	}

	/**
	 * Runs a GQL query through the Gigya accounts search API and
	 * returns its result to the client.
	 *
	 * Preview results are limited to 5 results for better performance.
	 *
	 * @access public
	 * @param array $params The JSON stringified parameters from the client.
	 * @return array
	 */
	public function run( $params ) {
		$constraints  = json_encode( $params['constraints'] );
		$member_query = new MemberQuery( null, $constraints );
		$query        = $member_query->to_gql();

		if ( $query === '' ) {
			return array(
				'accounts' => array(),
				'total' => 0,
			);
		}

		$searcher = new AccountsSearcher();
		$accounts = $searcher->search( $query, false, 5 );

		return $accounts;
	}

}
