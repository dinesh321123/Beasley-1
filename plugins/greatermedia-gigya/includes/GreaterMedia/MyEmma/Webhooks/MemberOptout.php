<?php

namespace GreaterMedia\MyEmma\Webhooks;

use GreaterMedia\MyEmma\Webhooks\Webhook;

class MemberOptout extends Webhook {

	function get_event_name() {
		return 'member_optout';
	}

	function run( $params ) {
		$emma_member_id = $this->get_emma_member_id( $params );
		$gigya_user_id  = $this->get_gigya_user_id( $emma_member_id );

		$this->unsubscribe( $gigya_user_id );

		return true;
	}

	function unsubscribe( $gigya_user_id ) {
		$data        = get_gigya_user_profile_data( $gigya_user_id );
		$emma_groups = $this->get_emma_groups();

		$data['optout']           = true;
		$data['subscribedToList'] = array();

		foreach ( $emma_groups as $emma_group ) {
			$field_key          = $emma_group['field_key'];
			$data[ $field_key ] = false;
		}

		set_gigya_user_profile_data( $gigya_user_id, $data );
	}

	function get_emma_groups() {
		$emma_groups = get_option( 'emma_groups' );
		$emma_groups = json_decode( $emma_groups, true );

		if ( ! is_array( $emma_groups ) ) {
			$emma_groups = array();
		}

		return $emma_groups;
	}
}
