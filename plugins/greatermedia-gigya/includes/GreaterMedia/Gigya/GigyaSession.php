<?php

namespace GreaterMedia\Gigya;

class GigyaSession {

	public $cookie_value = array();
	public $loaded       = false;

	static public $instance = null;
	static public function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new GigyaSession();
			self::$instance->load();
		}

		return self::$instance;
	}


	public function is_logged_in() {
		return ! is_null( $this->get( 'UID' ) );
	}

	public function get_user_id() {
		return $this->get_user_field( 'UID' );
	}

	public function get_user_field( $field ) {
		if ( $this->is_logged_in() ) {
			return $this->get( $field );
		} else {
			return null;
		}
	}

	public function get_user_profile() {
		$this->load();

		if ( ! $this->is_logged_in() ) {
			throw new \Exception( 'Cannot Fetch Gigya User Profile: not logged in' );
		}

		$user_id = $this->get_user_id();
		$query   = "select profile from accounts where UID = '${user_id}'";
		$request = new GigyaRequest( null, null, 'accounts.search' );
		$request->setParam( 'query', $query );
		$response = $request->send();

		if ( $response->getErrorCode() === 0 ) {
			$json = json_decode( $response->getResponseText(), true );
			$total = $json['totalCount'];

			if ( $total > 0 ) {
				return $json['results'][0]['profile'];
			} else {
				throw new \Exception( "User Profile not found: {$user_id}" );
			}
		} else {
			throw new \Exception(
				"Failed to get Gigya User Profile: {$user_id} - " . $response->getErrorMessage()
			);
		}
	}

	public function get( $key ) {
		$this->load();

		if ( array_key_exists( $key, $this->cookie_value ) ) {
			return $this->cookie_value[ $key ];
		} else {
			return null;
		}
	}

	public function get_key( $key ) {
		return $this->get( $key );
	}

	public function load() {
		if ( $this->loaded ) {
			return;
		}

		$cookie_name = $this->get_cookie_name();

		if ( array_key_exists( $cookie_name, $_COOKIE ) ) {
			$cookie_text = wp_unslash( $_COOKIE[ $cookie_name ] );
		} else {
			$cookie_text = '{}';
		}

		$this->cookie_value = $this->deserialize( $cookie_text );
		$this->loaded       = true;
	}

	public function get_cookie_name() {
		return 'gigya_profile';
	}

	public function deserialize( $cookie_text ) {
		if ( strpos( $cookie_text, '{'  ) !== 0 ) {
			$cookie_text  = base64_decode( $cookie_text );
		}

		$cookie_value = json_decode( $cookie_text, true );

		if ( ! is_array( $cookie_value ) ) {
			$cookie_value = array();
		}

		return $cookie_value;
	}

}
