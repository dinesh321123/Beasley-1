<?php

namespace GreaterMedia\Gigya\Ajax;

use GreaterMedia\Gigya\GigyaRequest;

class HasParticipatedAjaxHandler extends AjaxHandler {

	function get_action() {
		return 'has_participated';
	}

	function is_public() {
		return true;
	}

	function run( $params ) {
		$contest_id = $params['contest_id'];

		if ( is_gigya_user_logged_in() ) {
			$user_id = get_gigya_user_id();
			return $this->has_user_entered_contest( $contest_id, $user_id );
		} else if ( array_key_exists( 'email', $params ) ) {
			$email = $params['email'];

			if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) === false ) {
				return false;
			}
			return $this->has_email_entered_contest( $contest_id, $email );
		} else {
			return false;
		}
	}

	function has_user_entered_contest( $contest_id, $user_id ) {
		$query   = <<<GQL
select UID
from actions
where
	data.actions.actionType = 'action:contest' and
	data.actions.actionID = '{$contest_id}'    and
	UID = '{$user_id}'
GQL;
		$request = new GigyaRequest( null, null, 'ds.search' );
		$request->setParam( 'query', $query );

		return $this->execute_request( $request );
	}

	// we are making a guess here that a form field with a value = the
	// email corresponds to an email field
	// And hence don't need to figure out the field name that stores
	// emails
	function has_email_entered_contest( $contest_id, $email ) {
		$query = <<<GQL
select UID
from actions
where
	data.actions.actionType = 'action:contest'   and
	data.actions.actionID = '{$contest_id}'      and
	data.actions.actionData.value_s = '{$email}'
GQL;

		$request = new GigyaRequest( null, null, 'ds.search' );
		$request->setParam( 'query', $query );

		return $this->execute_request( $request );
	}

	function execute_request( $request ) {
		$response      = $request->send();
		$response_text = $response->getResponseText();

		if ( $response->getErrorCode() === 0 ) {
			$json = json_decode( $response_text, true );
			if ( is_array( $json ) ) {
				return array_key_exists( 'objectsCount', $json ) && $json['objectsCount'] > 0;
			} else {
				error_log( 'Gigya returned invalid JSON: ' . $response_text );
				return false;
			}
		} else {
			error_log( "Failed to query ds.search: $response_text" );
			return false;
		}
	}

}
