<?php

namespace GreaterMedia\MyEmma\Webhooks;

use GreaterMedia\MyEmma\Webhooks\Webhook;

class MemberOptout extends Webhook {

	function get_event_name() {
		return 'member_optout';
	}

	function run( $params ) {
		$data               = $params['data'];
		$member_id          = $data['member_id'];
		$email              = $this->get_emma_member_email( $member_id );
		$gigya_profile      = $this->get_gigya_profile_data( $email );
		$gigya_user_id      = $gigya_profile['UID'];
		$gigya_profile_data = $gigya_profile['data'];

		$gigya_profile_data['optout'] = true;
		$this->update_gigya_profile_data( $gigya_user_id, $gigya_profile_data );

		error_log( "Updated optout for: $email" );
		return true;
	}

	function get_emma_member_email( $member_id ) {
		$api      = new EmmaAPI();
		$response = $api->membersListById( intval( $member_id ) );
		$json     = json_decode( $response, true );

		return $json['email'];
	}

	function get_gigya_profile_data( $email ) {
		$query   = "select UID, data from accounts where profile.email = '$email' limit 1";
		$request = new GigyaRequest( null, null, 'accounts.search' );
		$request->setParam( 'query', $query );
		$response = $request->send();

		if ( $response->getErrorCode() === 0 ) {
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

		if ( $response->getErrorCode() === 0 ) {
			return true;
		} else {
			error_log(
				"Failed to update gigya profile data: $user_id - " . $response->getResponseText()
			);
			return false;
		}
	}

}
