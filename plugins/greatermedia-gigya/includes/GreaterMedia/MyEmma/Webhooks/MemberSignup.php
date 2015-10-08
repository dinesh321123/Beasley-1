<?php

namespace GreaterMedia\MyEmma\Webhooks;

class MemberSignup extends Webhook {

	function get_event_name() {
		return 'member_signup';
	}

	function run( $params ) {
		$emma_member_id = $this->get_emma_member_id( $params );
		$gigya_user_id  = $this->get_gigya_user_id( $emma_member_id );

		$this->subscribe( $gigya_user_id );

		return true;
	}

	function subscribe( $gigya_user_id ) {
		$data           = get_gigya_user_profile_data( $gigya_user_id );
		$data['optout'] = false;

		set_gigya_user_profile_data( $gigya_user_id, $data );
	}

}

