<?php

namespace Bbgi;

class Cache {

	/**
	 * Returns cache key based on incoming arguments.
	 *
	 * @static
	 * @access protected
	 * @param string|array $key
	 * @return string
	 */
	protected static function _get_cache_key( $key ) {
		return is_array( $key ) ? implode( '-', $key ) : $key;
	}

	/**
	 * Returns cached value or populates it if it hasn't been found.
	 *
	 * @static
	 * @access public
	 * @param string $key
	 * @param callable $callback
	 * @param int $ttl
	 * @return mixed
	 */
	public static function get( $key, $callback, $ttl = 0 ) {
		$found = false;
		$key = self::_get_cache_key( $key );
		$results = wp_cache_get( $key, 'bbgi', false, $found );
		if ( ! $found ) {
			$results = call_user_func( $callback );
			wp_cache_set( $key, $results, 'bbgi', $ttl );
		}

		return $results;
	}

}
