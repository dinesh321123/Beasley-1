<?php

namespace GreaterMedia\Gigya\Ajax;

use GreaterMedia\MyEmma\EmmaAPI;
use GreaterMedia\Gigya\Sync\Sentinel;

class ChangeMemberQuerySegmentAjaxHandler extends AjaxHandler {

	function get_action() {
		return 'change_member_query_segment';
	}

	function run( $params ) {
		$email_segment_id = intval( $params['email_segment_id'] );
		$member_query_id  = $params['member_query_id'];

		if ( $this->has_remote_segment( $email_segment_id ) ) {
			$sentinel = new Sentinel( $member_query_id, array( 'mode' => 'export' ) );
			$sentinel->set_email_segment_id( $email_segment_id );

			return $email_segment_id;
		} else {
			throw new \Exception( 'Invalid Group ID' );
		}
	}

	function has_remote_segment( $segment_id ) {
		$api = new EmmaAPI();

		try {
			$response = $api->groupsGetById( $segment_id );
			return true;
		} catch ( \Exception $e ) {
			return false;
		}
	}

}
