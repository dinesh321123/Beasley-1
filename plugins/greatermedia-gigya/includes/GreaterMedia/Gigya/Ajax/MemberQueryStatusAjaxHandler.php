<?php

namespace GreaterMedia\Gigya\Ajax;

use GreaterMedia\Gigya\Sync\Sentinel;

class MemberQueryStatusAjaxHandler extends AjaxHandler {

	function get_action() {
		return 'member_query_status';
	}

	function run( $params ) {
		$member_query_id = $params['member_query_id'];
		$sentinel        = new Sentinel( $member_query_id, array( 'mode' => 'export' ) );
		$result          = $sentinel->get_status_meta();

		return $result;
	}

}
