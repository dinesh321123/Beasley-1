<?php

namespace GreaterMedia\MyEmma\Ajax;

use GreaterMedia\Gigya\Ajax\AjaxHandler;
use GreaterMedia\Gigya\GigyaRequest;
use GreaterMedia\MyEmma\EmmaAPI;

class UpdateMyEmmaGroup extends AjaxHandler {

	function get_action() {
		return 'update_myemma_group';
	}

	function run( $params ) {
		$group_to_update   = sanitize_text_field( $params['group_to_update'] );
		$group_id          = trim( sanitize_text_field( $params['emma_group_id'] ) );
		$group_name        = sanitize_text_field( $params['emma_group_name'] );
		$field_key         = sanitize_text_field( $params['gigya_field_key'] );
		$group_description = sanitize_text_field( $params['emma_group_description'] );
		$group_active      = filter_var( $params['emma_group_active'], FILTER_VALIDATE_BOOLEAN );
		$opt_in_default    = filter_var( $params['emma_group_opt_in'], FILTER_VALIDATE_BOOLEAN );

		if ( empty( $group_name ) ) {
			throw new \Exception( 'Error: Emma Group name must not be empty' );
		}

		if ( empty( $group_description ) ) {
			throw new \Exception( 'Error: Emma Group Description must not be empty' );
		}

		if ( empty( $field_key ) || ! ctype_alnum( $field_key ) ) {
			throw new \Exception( 'Error: Gigya Field key can contain only letters and numbers' );
		} else {
			$this->update_schema( $field_key );
		}

		if ( $group_id === '' ) {
			$group_id = $this->create_group( $group_name );
		} else if ( ! $this->group_exists( $group_id ) ) {
			throw new \Exception( "Error: Emma Group not found - {$group_id}" );
		}

		$groups      = get_option( 'emma_groups' );
		$groups      = json_decode( $groups, true );
		$group_index = $this->find_group_index( $groups, $group_to_update );

		if ( $group_index !== -1 ) {
			$groups[ $group_index ] = array(
				'group_id'          => $group_id,
				'group_name'        => $group_name,
				'field_key'         => $field_key,
				'group_description' => $group_description,
				'group_active'      => $group_active,
				'opt_in_default' 	=> $opt_in_default,
			);

			update_option( 'emma_groups', json_encode( array_values( $groups ) ) );
		} else {
			throw new \Exception( "Group not found: {$group_id}" );
		}

		$this->update_emma_group( $group_id, $group_name );

		$params['emma_group_id']     = $group_id;
		$params['group_description'] = $group_description;
		$params['group_active']      = $group_active;

		return $params;
	}

	function find_group_index( $groups, $group_id ) {
		foreach ( $groups as $index => $group ) {
			if ( $group['group_id'] === $group_id ) {
				return $index;
			}
		}

		return -1;
	}

	function update_emma_group( $group_id, $group_name ) {
		$api      = new EmmaAPI();
		$group    = array( 'group_name' => $group_name );
		$response = $api->groupsUpdateSingle( intval( $group_id ), $group );

		return true;
	}

	function update_schema( $field_key ) {
		$schema  = $this->get_schema_for_custom_field( $field_key );
		$request = new GigyaRequest( null, null, 'accounts.setSchema' );
		$request->setParam( 'dataSchema', json_encode( $schema ) );
		$response = $request->send();

		if ( $response->getErrorCode() === 0 ) {
			return true;
		} else {
			throw new \Exception(
				'Error: Failed to update schema - ' . $response->getErrorMessage()
			);
		}
	}

	function get_schema_for_custom_field( $field_key ) {
		$schema = array(
			'fields' => array(),
			'dynamicSchema' => true,
		);

		$schema['fields'][ $field_key ] = array(
			'writeAccess' => 'clientModify',
			'required' => false,
		);

		return $schema;
	}

	function create_group( $group_name ) {
		$api = new EmmaAPI();
		$groups = array(
			'groups' => array(
				array(
					'group_name' => $group_name,
				),
			)
		);

		$response = $api->groupsAdd( $groups );
		$json     = json_decode( $response, true );
		error_log( $response );

		if ( is_array( $json ) ) {
			return strval( $json[0]['member_group_id'] );
		} else {
			return '';
		}
	}

	function group_exists( $group_id ) {
		$api = new EmmaAPI();

		try {
			$response = $api->groupsGetById( intval( $group_id ) );
			return true;
		} catch ( \Exception $e ) {
			return false;
		}
	}
}
