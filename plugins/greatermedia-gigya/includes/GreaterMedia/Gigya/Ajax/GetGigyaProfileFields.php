<?php

namespace GreaterMedia\Gigya\Ajax;

class GetGigyaProfileFields extends AjaxHandler {

	function get_action() {
		return 'get_gigya_profile_fields';
	}

	function is_public() {
		return true;
	}

	function run( $params ) {
		$fields = $params['fields'];

		if ( is_gigya_user_logged_in() ) {
			$gigya_profile = get_gigya_user_profile();
			$result        = array();

			foreach ( $fields as $field ) {
				if ( $field === 'dateOfBirth' ) {
					if ( $this->has_birth_day( $gigya_profile ) ) {
						$result['dateOfBirth'] = $gigya_profile['birthMonth'] . '/' . $gigya_profile['birthDay'] . '/' . $gigya_profile['birthYear'];
					} else {
						$result['dateOfBirth'] = 'N/A';
					}
				} else if ( array_key_exists( $field, $gigya_profile ) ) {
					$result[ $field ] = $gigya_profile[ $field ];
				}
			}

			return $result;
		} else {
			return array();
		}
	}

	function has_birth_day( $profile ) {
		return
			array_key_exists( 'birthYear', $profile ) &&
			array_key_exists( 'birthMonth', $profile ) &&
			array_key_exists( 'birthDay', $profile );
	}

}
