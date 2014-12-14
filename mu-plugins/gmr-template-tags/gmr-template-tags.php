<?php
/**
 * Template tags that are probably going to be needed in all the themes, in some way, so sharing them here
 */

namespace GreaterMedia\TemplateTags;

/**
 * Generates and stores a function that just returns an arbitrary value.
 *
 * Initially created so we could have easily filter the excerpt length, but only in some cases. Needed a callable function
 * to add and remove easily from excerpt length filter.
 *
 * @param int $int
 *
 * @return callable
 */
function get_return_int_callback( $int ) {
	static $existing_closures;
	if ( ! isset( $existing_closures ) ) {
		$existing_closures = array();
	}

	if ( isset( $existing_closures[ $int ] ) ) {
		return $existing_closures[ $int ];
	}

	$closure = function() use ( $int ) {
		return $int;
	};

	$existing_closures[ $int ] = $closure;

	return $closure;
}

/**
 * Generates an excerpt of the given length. If no length is provided, we just use the default excerpt from core.
 *
 * @param int $length
 */
function the_excerpt_length( $length = null ) {
	echo get_the_excerpt_length( $length );
}

function get_the_excerpt_length( $length = null ) {
	if ( ! is_null( $length ) ) {
		$callable = get_return_int_callback( $length );
		add_filter( 'excerpt_length', $callable );
	}

	$excerpt = \get_the_excerpt();

	if ( ! is_null( $length ) ) {
		remove_filter( 'excerpt_length', $callable );
	}

	return $excerpt;
}
