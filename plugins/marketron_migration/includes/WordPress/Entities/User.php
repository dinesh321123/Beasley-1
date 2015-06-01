<?php

namespace WordPress\Entities;

class User extends BaseEntity {

	public $default_password;

	function add( &$fields ) {
		$display_name = $fields['display_name'];

		if ( ! array_key_exists( 'user_pass', $fields ) ) {
			$fields['user_pass'] = $this->get_default_password();
		}

		if ( ! array_key_exists( 'user_registered', $fields ) ) {
			$fields['user_registered'] = gmdate( 'Y-m-d H:i:s' );
		}

		$fields['user_login']    = $this->get_login( $display_name );
		$fields['user_email']    = $this->get_email( $display_name );
		$fields['user_nicename'] = $this->get_nicename( $display_name );
		$fields['user_url']      = $this->get_url( $display_name );

		$fields['user_activation_key'] = '';
		$fields['user_status']         = 0;

		if ( ! array_key_exists( 'existing_id', $fields ) ) {
			$fields['usermeta'] = $this->get_usermeta( $display_name );
		}

		$table  = $this->get_table( 'users' );
		$fields = $table->add( $fields );

		return $fields;
	}

	function get_default_password() {
		if ( is_null( $this->default_password ) ) {
			$pwd                    = $this->container->config->get_site_option( 'default_author_password' );
			$this->default_password = wp_hash_password( $pwd );
		}

		return $this->default_password;
	}

	function get_login( $display_name ) {
		return preg_replace( '/\s+/', '', $display_name );
	}

	function get_email( $display_name ) {
		$login        = $this->get_login( $display_name );
		$email_domain = $this->get_site_option( 'email_domain' );
		$email        = $login . '@' . $email_domain;

		return strtolower( $email );
	}

	function get_nicename( $display_name ) {
		return $display_name;
	}

	function get_url( $display_name ) {
		return '';
	}

	function get_roles() {
		return array(
			'subscriber' => true,
		);
	}

	function get_usermeta( $display_name ) {
		global $wpdb;

		$parts       = explode( ' ', $display_name );
		$first_name  = $parts[0];
		$last_name   = implode( ' ', array_slice( $parts, 1 ) );
		$description = '';

		$usermeta = array(
			'nickname'             => $this->get_nicename( $display_name ),
			'first_name'           => $first_name,
			'last_name'            => $last_name,
			'description'          => '',
			'rich_editing'         => 'true',
			'comment_shortcuts'    => 'false',
			'use_ssl'              => 0,
			'show_admin_bar_front' => true,
		);

		$usermeta[ $wpdb->prefix . 'capabilities' ] = serialize( $this->get_roles() );
		$usermeta[ $wpdb->prefix . 'user_level' ]   = 0;

		return $usermeta;
	}

}
