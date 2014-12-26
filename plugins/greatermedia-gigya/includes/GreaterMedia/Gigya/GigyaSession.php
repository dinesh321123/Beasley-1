<?php

namespace GreaterMedia\Gigya;

class GigyaSession {

	public $cookie_value     = array();
	public $loaded           = false;

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
		$this->loaded = true;

		if ( function_exists( 'vary_cache_on_function' ) ) {
			$this->register_cache_variant();
		}
	}

	/**
	 * Returns the batcache variant for the current page request. This
	 * function is the unrolled implementation of GigyaSession. It does
	 * base64 decoding, json decoding, etc.
	 *
	 * The body of this function must be stringified and copied and
	 * pasted into the register_cache_variant function below.
	 *
	 * This function is used for testing, and stringified version is
	 * used in batcache. Hence it is important that changes to the
	 * function must be reflected in the corresponding copied version in
	 * register_cache_variant.
	 *
	 * This function uses a single vary_cache_on_function to prevent
	 * duplication of cookie parsing code. Else the cookie parsing code
	 * would need to be implemented in each vary_cache function.
	 *
	 * It returns an underscore delimited variant name depending on the
	 * current user's Gigya session cookie.
	 *
	 * +----------------------------------------+---------------+
	 * |               Condition                | Cache Variant |
	 * +----------------------------------------+---------------+
	 * | No Gigya cookie                        | no            |
	 * | Invalid Gigya Cookie                   | no            |
	 * | Valid Gigya Cookie without age         | yes_0         |
	 * | Valid Gigya Cookie with age < 18       | yes_0         |
	 * | Valid Gigya Cookie with age >= 18 < 21 | yes_18        |
	 * | Valid Gigya Cookie with age >= 21      | yes_21        |
	 * +----------------------------------------+---------------+
	 *
	 * @access public
	 * @return string
	 */
	function get_cache_variant() {
		if ( array_key_exists( 'gigya_profile', $_COOKIE ) ) {
			/* Has gigya session cookie */
			$cookie_text = $_COOKIE['gigya_profile'];

			if ( strpos( $cookie_text, '{' ) !== 0 ) {
				/* Cookie text is base64 encoded */
				$cookie_text = base64_decode( $cookie_text );
			};

			/* if wp_unslash, json may be slashed */
			if ( function_exists( 'wp_unslash' ) ) {
				$cookie_text = wp_unslash( $cookie_text );
			}

			/* Decode cookie json */
			$cookie_value = json_decode( $cookie_text, true );

			/* Is cookie json valid? */
			if ( ! is_array( $cookie_value ) ) {
				$cookie_value = array();
			};

			/* Cookie json is valid if Gigya UID exists */
			if ( array_key_exists( 'UID', $cookie_value ) ) {
				/* Valid gigya cookie, hence logged in */
				/* If age exists use it, else use 0 */
				$age = array_key_exists( 'age', $cookie_value ) ? intval( $cookie_value['age'] ) : 0;

				/* Convert age to age bracket */
				if ( $age >= 21 ) {
					$age_bracket = '21';
				} else if ( $age < 21 && $age >= 18 ) {
					$age_bracket = '18';
				} else {
					$age_bracket = '0';
				};

				/* Return composite cache variant based on logged in
				 * status and age bracket */
				return 'yes_' . $age_bracket;
			} else {
				/* invalid gigya cookie, not logged in */
				return 'no';
			};
		} else {
			/* Gigya session cookie not found */
			return 'no';
		};
	}

	/**
	 * This is the copy-pasted version of the above get_cache_variant
	 * function body.
	 *
	 * IMPORTANT: Must be an exact copy of the above function.
	 *
	 * @access public
	 * @return string
	 */
	function get_cache_variant_func() {
		$cache_variant_func = <<<'FUNC'
		if ( array_key_exists( 'gigya_profile', $_COOKIE ) ) {
			/* Has gigya session cookie */
			$cookie_text = $_COOKIE['gigya_profile'];

			if ( strpos( $cookie_text, '{' ) !== 0 ) {
				/* Cookie text is base64 encoded */
				$cookie_text = base64_decode( $cookie_text );
			};

			/* if wp_unslash, json may be slashed */
			if ( function_exists( 'wp_unslash' ) ) {
				$cookie_text = wp_unslash( $cookie_text );
			}

			/* Decode cookie json */
			$cookie_value = json_decode( $cookie_text, true );

			/* Is cookie json valid? */
			if ( ! is_array( $cookie_value ) ) {
				$cookie_value = array();
			};

			/* Cookie json is valid if Gigya UID exists */
			if ( array_key_exists( 'UID', $cookie_value ) ) {
				/* Valid gigya cookie, hence logged in */
				/* If age exists use it, else use 0 */
				$age = array_key_exists( 'age', $cookie_value ) ? intval( $cookie_value['age'] ) : 0;

				/* Convert age to age bracket */
				if ( $age >= 21 ) {
					$age_bracket = '21';
				} else if ( $age < 21 && $age >= 18 ) {
					$age_bracket = '18';
				} else {
					$age_bracket = '0';
				};

				/* Return composite cache variant based on logged in
				 * status and age bracket */
				return 'yes_' . $age_bracket;
			} else {
				/* invalid gigya cookie, not logged in */
				return 'no';
			};
		} else {
			/* Gigya session cookie not found */
			return 'no';
		};
FUNC;

		return $cache_variant_func;
	}

	function register_cache_variant() {
		vary_cache_on_function( $this->get_cache_variant_func() );
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
