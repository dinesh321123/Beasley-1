<?php

namespace GreaterMedia\MyEmma\Ajax;

use GreaterMedia\Gigya\Ajax\AjaxHandler;
use GreaterMedia\Gigya\GigyaRequest;
use GreaterMedia\MyEmma\EmmaAPI;

class AddMyEmmaGroup extends AjaxHandler {

	function get_action() {
		return 'add_myemma_group';
	}

	function run( $params ) {
		$group_id          = sanitize_text_field( $params['emma_group_id'] );
		$group_name        = sanitize_text_field( $params['emma_group_name'] );
		$field_key         = sanitize_text_field( $params['gigya_field_key'] );
		$group_description = sanitize_text_field( $params['emma_group_description'] );
		$group_active      = filter_var( $params['emma_group_active'], FILTER_VALIDATE_BOOLEAN );
		$opt_in_default    = filter_var( $params['emma_group_opt_in'], FILTER_VALIDATE_BOOLEAN );

		if ( empty( $group_name ) ) {
			throw new \Exception( 'Error: Emma Group Name must not be empty' );
		}

		if ( empty( $group_description ) ) {
			throw new \Exception( 'Error: Emma Group Description must not be empty' );
		}

		if ( empty( $field_key ) || ! ctype_alnum( $field_key ) ) {
			throw new \Exception( 'Error: Gigya Field key can contain only letters and numbers' );
		} else {
			$this->update_schema( $field_key );
		}

		if ( empty( $group_id ) ) {
			$group_id = $this->create_group( $group_name );
		} else if ( ! $this->group_exists( $group_id ) ) {
			throw new \Exception( "Error: Emma Group not found - {$group_id}" );
		}

		$group = array(
			'group_id'          => $group_id,
			'group_name'        => $group_name,
			'field_key'         => $field_key,
			'group_description' => $group_description,
			'group_active'      => $group_active,
			'opt_in_default' 	=> $opt_in_default,
		);

		$groups = get_option( 'emma_groups' );
		if ( $groups !== false ) {
			$groups = json_decode( $groups, true );
			if ( ! is_array( $groups ) ) {
				$group = array();
			}
		}

		$groups[] = $group;
		update_option( 'emma_groups', json_encode( array_values( $groups ) ) );

		return $group;
	}

	function create_group( $group_name, $opts = null ) {
		if ( is_null( $opts ) ) {
			$api = new EmmaAPI();
		} else {
			$api = new EmmaAPI(
				$opts['account_id'],
				$opts['public_key'],
				$opts['private_key']
			);
		}

		$groups = array(
			'groups' => array(
				array(
					'group_name' => $group_name,
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

	function group_exists( $group_id ) {
		$api = new EmmaAPI();

		try {
			$response = $api->groupsGetById( intval( $group_id ) );
			return true;
		} catch ( \Exception $e ) {
			return false;
		}
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

}
