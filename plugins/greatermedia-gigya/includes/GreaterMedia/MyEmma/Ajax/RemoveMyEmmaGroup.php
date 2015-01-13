<?php

namespace GreaterMedia\MyEmma\Ajax;

use GreaterMedia\Gigya\Ajax\AjaxHandler;
use GreaterMedia\MyEmma\EmmaAPI;

class RemoveMyEmmaGroup extends AjaxHandler {

	function get_action() {
		return 'remove_myemma_group';
	}

	function run( $params ) {
		$group_id    = sanitize_text_field( $params['group_id'] );
		$groups      = get_option( 'emma_groups' );
		$groups      = json_decode( $groups, true );
		$group_index = $this->find_group_index( $groups, $group_id );

		if ( $group_index !== -1 ) {
			unset( $groups[ $group_index ] );
			update_option( 'emma_groups', json_encode( array_values( $groups ) ) );
		} else {
			throw new \Exception( "Group not found: {$group_id}" );
		}

		$this->remove_emma_group( $group_id );

		return $group_id;
	}

	function find_group_index( $groups, $group_id ) {
		foreach ( $groups as $index => $group ) {
			if ( $group['group_id'] === $group_id ) {
				return $index;
			}
		}

		return -1;
	}

	function remove_emma_group( $group_id ) {
		$api      = new EmmaAPI();
		$response = $api->groupsRemoveSingle( intval( $group_id ) );

		return true;
	}

}
