<?php

namespace WordPress\Tables;

class Users extends BaseTable {

	public $columns = array(
		'ID',
		'user_login',
		'user_pass',
		'user_nicename',
		'user_email',
		'user_url',
		'user_registered',
		'user_activation_key',
		'user_status',
		'display_name',
	);

	public $indices = array(
		'user_email',
		'user_login',
		'display_name',
	);

	function get_table_name() {
		return 'users';
	}

	function is_multisite_table() {
		return false;
	}

	function add( &$fields ) {
		$display_name = $fields['display_name'];

		if ( ! $this->has_user( $display_name ) ) {
			parent::add( $fields );
			$user_id = $fields['ID'];

			if ( array_key_exists( 'usermeta', $fields ) ) {
				$this->add_user_meta( $user_id, $fields['usermeta'] );
			}

			return $fields;
		} else {
			return $this->get_user( $display_name );
		}
	}

	function add_user_meta( $user_id, $fields ) {
		$meta_fields = $this->to_meta_fields( $user_id, $fields );
		$table       = $this->get_table( 'usermeta' );

		foreach ( $meta_fields as $meta_field ) {
			$table->add( $meta_field );
		}
	}

	function to_meta_fields( $user_id, $fields ) {
		$meta_fields = array();

		foreach ( $fields as $field_name => $field_value ) {
			$meta_field = array(
				'user_id'    => $user_id,
				'meta_key'   => $field_name,
				'meta_value' => $field_value,
			);

			$meta_fields[] = $meta_field;
		}

		//var_dump( $meta_fields );
		return $meta_fields;
	}

	function has_user( $display_name ) {
		return $this->has_row_with_field( 'display_name', $display_name );
	}

	function get_user( $display_name ) {
		$table = $this->get_table( 'users' );
		return $this->get_row_with_field( 'display_name', $display_name );
	}

	function get_user_id( $display_name ) {
		$user = $this->get_user( $display_name );

		if ( ! is_null( $user ) ) {
			return $user['ID'];
		} else {
			return null;
		}
	}

}
