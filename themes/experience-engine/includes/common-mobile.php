<?php

if ( ! function_exists( 'ee_is_common_mobile' ) ) :
	function ee_is_common_mobile() {
		static $jacapps_pos = null,
				$whiz_pos = null;

		if ( $jacapps_pos === null ) {
			$jacapps_pos = stripos( $_SERVER['HTTP_USER_AGENT'], 'jacapps' );

			// Allow way to toggle jacapps through URL querystring
			if ( isset( $_GET['jacapps'] ) ) {
				$jacapps_pos = 1;
			}
		}

		if($whiz_pos === null ) {
			$whiz_pos = stripos( $_SERVER['HTTP_USER_AGENT'], 'whiz' );

			// Allow way to toggle whiz through URL querystring
			if ( isset( $_GET['whiz'] ) ) {
				$whiz_pos = 1;
			}
		}

		return false !== $jacapps_pos || false !== $whiz_pos;
	}
endif;
