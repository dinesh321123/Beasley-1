<?php

namespace GreaterMedia\Gigya\Ajax;

use GreaterMedia\Gigya\GigyaRequest;
use GreaterMedia\MyEmma\EmmaAPI;

class EmmaMemberOptoutAjaxHandler extends AjaxHandler {

	function is_public() {
		return true;
	}

	function get_action() {
		return 'emma_member_optout';
	}

	/*
	 * 2014/12/30 14:15:51 [error] 27134#0: *791363 FastCGI sent in
	 * stderr: "PHP message: Handle optout - $_GET = Array
	 * (
	 *     [action] => emma_member_optout
	 *     )
	 *
	 *     PHP message: Handle optout - $_POST = Array
	 *     (
	 *     )
	 *
	 *     PHP message: Handle optout - json = {"event_name":
	 *     "member_optout", "resource_url":
	 *     "https://api.e2ma.net/1745171/members/1368668435", "data":
	 *     {"timestamp": "@D:2014-12-30T08:15:49", "mailing_id":
	 *     12094739, "subject": "Welcome to the Club a2", "account_id":
	 *     1745171, "member_id": 1368668435}}"
	 * */
	function handle_ajax() {
		/*
		error_log( 'Handle optout - $_GET = ' . print_r( $_GET, true ) );
		error_log( 'Handle optout - $_POST = ' . print_r( $_POST, true ) );
		error_log( 'Handle optout - json = ' . print_r( file_get_contents( 'php://input' ), true ) );
		*/

		$json               = file_get_contents( 'php://input' );
		$params             = json_decode( $json, true );
		$data               = $params['data'];
		$member_id          = $data['member_id'];
		$email              = $this->get_emma_member_email( $member_id );
		$gigya_profile      = $this->get_gigya_profile_data( $email );
		$gigya_user_id      = $gigya_profile['UID'];
		$gigya_profile_data = $gigya_profile['data'];

		$gigya_profile_data['optout'] = true;
		$this->update_gigya_profile_data( $gigya_user_id, $gigya_profile_data );

		error_log( "Updated optout for: $email" );
		wp_send_json_success( true );
	}

	function get_emma_member_email( $member_id ) {
		$api      = new EmmaAPI();
		$response = $api->membersListById( intval( $member_id ) );
		$json     = json_decode( $response, true );

		return $json['email'];
	}

	function get_gigya_profile_data( $email ) {
		$query   = "select UID, data from accounts where profile.email = '$email' limit 1";
		$request = new GigyaRequest( null, null, 'accounts.getAccountInfo' );
		$request->setParam( 'query', $query );
		$response = $request->send();

		if ( $response->getErrorCode() !== 0 ) {
			$json = json_decode( $response->getResponseText(), true );
			return $json['results'][0];
		} else {
			error_log( 'Failed to get Gigya Profile: ' . $response->getResponseText() );
			return false;
		}
	}

	function update_gigya_profile_data( $user_id, $data ) {
		$request = new GigyaRequest( null, null, 'accounts.setAccountInfo' );
		$request->setParam( 'UID', $user_id );
		$request->setParam( 'data', json_encode( $data ) );
		$response = $request->send();

		if ( $response->getErrorCode() !== 0 ) {
			return true;
		} else {
			error_log(
				"Failed to update gigya profile data: $user_id - " . $response->getResponseText()
			);
			return false;
		}
	}

	function run( $params ) {
		// no op
	}

}
