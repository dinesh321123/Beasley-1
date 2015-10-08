<?php

namespace GreaterMedia\Gigya\Migration;

class EmailRepairer {

	function repair( $email ) {
		$email = trim( $email );
		$email = $this->choose_first_or( $email );
		$email = $this->choose_first_and( $email );
		$email = $this->strip_leading( $email, '\.\.' );
		$email = $this->strip_leading( $email, '\.' );
		$email = $this->strip_trailing( $email, '\.\.' );
		$email = $this->strip_trailing( $email, '\.' );
		$email = $this->replace_com1( $email );
		$email = $this->replace_com2( $email );
		$email = $this->replace_com3( $email );
		$email = $this->replace_net1( $email );
		$email = $this->replace_net2( $email );
		$email = $this->replace_net3( $email );
		$email = $this->replace_bracket( $email );
		$email = $this->replace_dot_at( $email );
		$email = $this->replace_double_at( $email );
		$email = $this->replace_double_dot( $email );
		$email = $this->patch_at_msn( $email );
		$email = $this->patch_at_comcast( $email );
		$email = $this->replace_comma( $email );
		$email = $this->replace_space( $email );

		if ( $this->is_valid( $email ) ) {
			return $email;
		} else {
			return false;
		}
	}

	function strip_leading( $email, $char ) {
		return ltrim( $email, $char );
	}

	function strip_trailing( $email, $char ) {
		return rtrim( $email, $char );
	}

	function replace_with( $email, $search, $replace = '' ) {
		return str_replace( $search, $replace, $email );
	}

	function replace_com1( $email ) {
		return $this->replace_with( $email, '.c om', '.com' );
	}

	function replace_com2( $email ) {
		return $this->replace_with( $email, '.c,om', '.com' );
	}

	function replace_com3( $email ) {
		return $this->replace_with( $email, ',com', '.com' );
	}

	function replace_net1( $email ) {
		return $this->replace_with( $email, '.n et', '.net' );
	}

	function replace_net2( $email ) {
		return $this->replace_with( $email, '.n,et', '.net' );
	}

	function replace_net3( $email ) {
		return $this->replace_with( $email, ',net', '.net' );
	}

	function replace_bracket( $email ) {
		return $this->replace_with( $email, '[', '' );
	}

	function replace_double_at( $email ) {
		$count = substr_count( $email, '@' );
		if ( $count === 2 ) {
			$pattern = '~@(?!.*@)~';
			return preg_replace( $pattern, '.', $email );
		} else {
			return $email;
		}
	}

	function replace_double_dot( $email ) {
		$count = substr_count( $email, '@' );
		if ( $count === 0 ) {
			$count = substr_count( $email, '.' );
			if ( $count === 2 ) {
				return preg_replace( '/\./', '@', $email, 1 );
			} else {
				return $email;
			}
		} else {
			return $email;
		}
	}

	function replace_dot_at( $email ) {
		return $this->replace_with( $email, '.@', '@' );
	}

	function patch_at_msn( $email ) {
		return preg_replace( '/@msn$/', '@msn.com', $email );
	}

	function patch_at_comcast( $email ) {
		return preg_replace( '/@comcast$/', '@comcast.net', $email );
	}

	function choose_first_boolean( $email, $condition ) {
		if ( strstr( $email, " {$condition} " ) !== false ) {
			$parts = explode( " {$condition} ", $email );
			return $this->repair( $parts[0] );
		} else {
			return $email;
		}
	}

	function choose_first_or( $email ) {
		return $this->choose_first_boolean( $email, 'or' );
	}

	function choose_first_and( $email ) {
		return $this->choose_first_boolean( $email, 'and' );
	}

	function replace_comma( $email ) {
		return $this->replace_with( $email, ',', '' );
	}

	function replace_space( $email ) {
		return $this->replace_with( $email, ' ', '' );
	}

	function is_valid( $email ) {
		return filter_var( $email, FILTER_VALIDATE_EMAIL ) !== false;
	}


}
