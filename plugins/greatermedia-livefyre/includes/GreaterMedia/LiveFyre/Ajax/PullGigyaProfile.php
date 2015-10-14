<?php

namespace GreaterMedia\LiveFyre\Ajax;

use GreaterMedia\Gigya\Ajax\AjaxHandler;

class PullGigyaProfile extends AjaxHandler {

	function get_action() {
		return 'pull_gigya_profile';
	}

	function is_public() {
		return true;
	}

	function handle_ajax() {
		$this->authorize();
		return $this->run( $_GET );
	}

	function get_url() {
		$ajax_url = admin_url( 'admin-ajax.php' );
		$params   = array(
			'action'                   => $this->get_action(),
			'ping_for_pull_auth_token' => $this->get_ping_for_pull_auth_token(),
			'gigya_user_id'            => '__gigya_user_id__',
		);

		$query = http_build_query( $params );
		$query = str_replace( '__gigya_user_id__', '{id}', $query );

		return $ajax_url . '?' . $query;
	}

	function run( $params ) {
		$gigya_user_id = sanitize_text_field( $params['gigya_user_id'] );
		$pull_profile  = $this->get_pull_profile( $gigya_user_id );

		wp_send_json( $pull_profile );
	}

	function authorize() {
		if ( array_key_exists( 'ping_for_pull_auth_token', $_GET ) ) {
			$auth_token          = sanitize_text_field ( $_GET['ping_for_pull_auth_token'] );
			$required_auth_token = $this->get_ping_for_pull_auth_token();

			if ( $auth_token === $required_auth_token ) {
				return true;
			} else {
				wp_send_json_error( 'not authorized' );
			}
		} else {
			wp_send_json_error( 'not authorized' );
		}
	}

	/* Ping For Pull Template Helpers */
	function get_gigya_profile( $user_id ) {
		try {
			$gigya_user_id = base64_decode( $user_id );
			$user = get_gigya_user_profile( $gigya_user_id );
			$user['UID'] = $gigya_user_id;
		} catch ( \Exception $e ) {
			$user = new \WP_Error(
				'error', 'Failed to fetch ping for pull profile for: ' . $user_id
			);
		}

		return $user;
	}

	function get_pull_profile( $user_id ) {
		$user         = $this->get_gigya_profile( $user_id );
		$first_name   = array_key_exists( 'firstName', $user ) ? $user['firstName'] : '';
		$last_name    = array_key_exists( 'lastName', $user ) ? $user['lastName'] : '';
		$display_name = $first_name . ' ' . $last_name;

		if ( ! is_wp_error( $user ) ) {
			$pull_profile = array(
				'id'           => $user_id,
				'display_name' => $display_name,
				'email'        => $user['email'],
				'settings_url' => get_site_url() . '/members/account',
			);

			if ( array_key_exists( 'thumbnailURL', $user ) ) {
				$pull_profile['image_url'] = $user['thumbnailURL'];
			}
		} else {
			$pull_profile = array( 'error' => $user->get_error_message() );
		}

		return $pull_profile;
	}

	function get_ping_for_pull_auth_token() {
		$settings      = $this->get_livefyre_settings();
		$settings_json = json_encode( $settings );

		return md5( $settings_json );
	}

	function get_livefyre_settings() {
		$settings = get_option( 'livefyre_settings' );
		$settings = json_decode( $settings, true );

		return $settings;
	}
}

