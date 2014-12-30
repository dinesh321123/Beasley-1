<?php

namespace GreaterMedia\Gigya\Sync;

use GreaterMedia\Gigya\GigyaRequest;

class GigyaUserFinder {

	function find( $user_ids ) {
		$query   = $this->query_for( $user_ids );
		$request = new GigyaRequest( null, null, 'accounts.search' );
		$request->setParam( 'query', $query );
		$response = $request->send();
		$response_text = $response->getResponseText();

		if ( $response->getErrorCode() === 0 ) {
			$json = json_decode( $response_text, true );

			if ( is_array( $json ) ) {
				return $this->results_to_users( $json['results'] );
			} else {
				return array();
			}
		} else {
			throw new \Exception(
				"GigyaUserFinder: Failed to find users {$query} - {$response_text}"
			);
		}
	}

	function results_to_users( $results ) {
		$users = array();

		foreach ( $results as $result ) {
			$users[] = $this->result_to_user( $result );
		}

		return $users;
	}

	function result_to_user( $result ) {
		$user = array();

		if ( array_key_exists( 'profile', $result ) ) {
			$profile = $result['profile'];

			if ( array_key_exists( 'email', $profile ) ) {
				$user['email'] = $profile['email'];
			}

			if ( array_key_exists( 'firstName', $profile ) && array_key_exists( 'lastName', $profile ) ) {
				$user['fields'] = array(
					'first_name' => $profile['firstName'],
					'last_name'  => $profile['lastName'],
				);
			}
		}

		return $user;
	}

	function query_for( $user_ids ) {
		$ids   = "'" . implode( "', '", $user_ids ) . "'";
		$query = "select profile.email, profile.firstName, profile.lastName, UID from accounts where UID in ($ids) and data.optout != true limit 10000";

		return $query;
	}

}
