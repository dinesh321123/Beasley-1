<?php

namespace GreaterMedia\Capabilities;

class CapabilitiesLoader {

	public $role_defaults = array(
		'administrator' => true,
		'editor'        => true,
		'author'        => false,
		'subscriber'    => false,
		'contributor'   => false,
		'dj'            => false,
	);

	function load( $post_type ) {
		$this->change_roles( $post_type, 'add' );
	}

	function unload( $post_type ) {
		$this->change_roles( $post_type, 'remove' );
	}

	function change_roles( $post_type, $action ) {
		$roles        = $this->get_roles();
		$capabilities = $this->find( $post_type );

		foreach ( $roles as $role_name => $role_info ) {
			$role         = get_role( $role_name );
			$role_default = $this->get_role_default( $role_name );

			foreach ( $capabilities as $capability ) {
				if ( $action === 'add' ) {
					//error_log( "add_cap: $post_type - $role_name - " . $capability );
					$role->add_cap( $capability, $role_default );
				} else if ( $action === 'remove' ) {
					//error_log( "remove_cap: $post_type - $role_name - " . $capability );
					$role->remove_cap( $capability );
				}
			}
		}
	}

	function find( $post_type ) {
		$post_type_obj = get_post_type_object( $post_type );

		if ( $post_type_obj && property_exists( $post_type_obj, 'cap' ) ) {
			return $post_type_obj->cap;
		} else {
			return array();
		}
	}

	function get_roles() {
		return get_editable_roles();
	}

	function get_role_default( $role_name ) {
		if ( array_key_exists( $role_name, $this->role_defaults ) ) {
			return $this->role_defaults[ $role_name ];
		} else {
			return false;
		}
	}

}
