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
		$user['fields'] = array();

		if ( array_key_exists( 'profile', $result ) ) {
			$profile = $result['profile'];

			if ( array_key_exists( 'email', $profile ) ) {
				$user['email'] = $profile['email'];
			}

			if ( array_key_exists( 'firstName', $profile ) ) {
				$user['fields']['first_name'] = $profile['firstName'];
			}

			if ( array_key_exists( 'firstName', $profile ) ) {
				$user['fields']['last_name']  = $profile['lastName'];
			}

			if ( $this->has_birth_day( $profile ) ) {
				$user['fields']['birthday'] = $this->get_birth_day( $profile );
			}
		}

		return $user;
	}

	function has_birth_day( $profile ) {
		return
			array_key_exists( 'birthYear', $profile ) &&
			array_key_exists( 'birthMonth', $profile ) &&
			array_key_exists( 'birthDay', $profile );
	}

	function get_birth_day( $profile ) {
		return $profile['birthMonth'] . '/' . $profile['birthDay'] . '/' . $profile['birthYear'];
	}

	function query_for( $user_ids ) {
		$ids   = "'" . implode( "', '", $user_ids ) . "'";
		$query = <<<GQL
select
	profile.email,
	profile.firstName,
	profile.lastName,
	profile.birthYear, profile.birthMonth, profile.birthDay,
	UID
from accounts
	where UID in ($ids) and
	data.optout != true
limit 10000;
GQL;

		return $query;
	}

}
