<?php

namespace GreaterMedia\MyEmma\Ajax;

use GreaterMedia\Gigya\Ajax\AjaxHandler;
use GreaterMedia\MyEmma\EmmaAPI;

class AddMyEmmaGroup extends AjaxHandler {

	function get_action() {
		return 'add_myemma_group';
	}

	function run( $params ) {
		// TODO: Validation
		$group_id   = $params['emma_group_id'];
		$group_name = $params['emma_group_name'];
		$field_key  = $params['gigya_field_key'];

		$group = array(
			'group_id' => $group_id,
			'group_name' => $group_name,
			'field_key' => $field_key,
		);

		if ( $group_id === '' ) {
			$group['group_id'] = $this->create_group( $group_name );
		}

		$groups = get_option( 'emma_groups' );
		if ( $groups !== false ) {
			$groups = json_decode( $groups, true );
			if ( ! is_array( $groups ) ) {
				$group = array();
			}
		}

		$groups[] = $group;
		update_option( 'emma_groups', json_encode( $groups ) );

		return $group;
	}

	function create_group( $group_name ) {
		$api = new EmmaAPI();
		$groups = array(
			'groups' => array(
				array(
					'group_name' => $group_name
				),
			)
		);

		$response = $api->groupsAdd( $groups );
		$json     = json_decode( $response, true );

		if ( is_array( $json ) ) {
			return strval( $json[0]['member_group_id'] );
		} else {
			return '';
		}
	}

}
