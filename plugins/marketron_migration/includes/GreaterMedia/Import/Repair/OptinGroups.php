<?php

namespace GreaterMedia\Import\Repair;

class OptinGroups {

	public $groups = array();

	function load( $config ) {
		$json   = file_get_contents( $config );
		$json   = json_decode( $json, true );
		$groups = $json['groups'];

		if ( ! empty( $groups ) ) {
			$this->groups = array();

			foreach ( $groups as $group ) {
				$this->groups[ $group['old_id'] ] = $group;
			}
		} else {
			\WP_CLI::error( "Invalid Optin Groups JSON: $config" );
		}
	}

	function has_group( $group_id ) {
		return array_key_exists( $group_id, $this->groups );
	}

	function replacement_for( $group_id ) {
		return $this->groups[ $group_id ];
	}

}
