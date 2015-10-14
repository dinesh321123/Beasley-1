<?php

namespace GreaterMedia\Gigya;

/**
 * Account searcher uses the Gigya accounts api to match accounts to a
 * specified GQL query.
 *
 * @package GreaterMedia\Gigya
 */
class AccountsSearcher {

	public $totalCount;

	/**
	 * Searches for accounts using the Gigya SDK using the
	 * accounts.search method.
	 *
	 * @param string $query Escaped GQL query
	 * @param bool $count Whether to build a count query
	 * @param int $limit Optional limit the results to specified max
	 * @return string The JSON result from the Gigya API server.
	 */
	public function search( $query, $count = false, $limit = null ) {
		$query    = $this->prepare_query( $query, $count, $limit );
		//error_log( $query );
		$request  = $this->request_for( 'accounts.search', $query );
		$response = $request->send();

		if ( $response->getErrorCode() === 0 ) {
			return $this->accounts_for_response( $response, $limit );
		} else {
			throw new \Exception( $response->getErrorMessage() );
		}
	}

	/**
	 * Returns the accounts for response.
	 *
	 */
	public function accounts_for_response( $response, $limit ) {
		$json       = json_decode( $response->getResponseText(), true );
		$totalCount = $json['totalCount'];

		if ( $response->getErrorCode() === 0 ) {
			$accounts = array();
			$json     = json_decode( $response->getResponseText(), true );

			foreach ( $json['results'] as $account ) {
				$profile = $account['profile'];

				if ( array_key_exists( 'email', $profile ) ) {
					$email = $profile['email'];
				} else {
					$email = "Unknown Email ({$profile['firstName']} {$profile['lastName']})";
				}

				$accounts[] = array( 'email' => $email );
			}

			return array(
				'accounts' => $accounts,
				'total'    => $totalCount,
			);
		} else {
			throw new \Exception( $response->getErrorMessage() );
		}
	}

	/**
	 * Unescapes constants and builds a GQL query to be sent over the
	 * wire to the Gigya API.
	 *
	 * @param string $query Escaped GQL query
	 * @param bool $count Whether to build a count query
	 * @param int $limit Optional limit the results to specified max
	 * @return string
	 */
	public function prepare_query( $query, $count = false, $limit = null ) {
		$query = str_replace( 'C_SINGLE_QUOTE', "''", $query );
		$query = str_replace( 'C_DOUBLE_QUOTE',  '"', $query );
		$query = str_replace( 'C_BACKSLASH',  '\\', $query );

		if ( $count ) {
			// TODO: Regex to restrict between select and from
			$query = str_replace( '*', 'count(*)', $query );
		}

		if ( is_int( $limit ) ) {
			$query .= " limit $limit";
		}

		return $query;
	}

	/**
	 * Builds a Gigya Request object for the specified method.
	 *
	 * @access public
	 * @param string $method The api method to call.
	 * @param string $query The prepared query
	 * @return GSRequest
	 */
	public function request_for( $method, $query ) {
		$request = new GigyaRequest( null, null, $method );
		$request->setParam( 'query',  $query );

		return $request;
	}

}
