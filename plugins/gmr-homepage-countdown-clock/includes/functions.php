<?php
/**
 * General functions.
 *
 * @package GreaterMedia\HomepageCountdownClock
 */

namespace GreaterMedia\HomepageCountdownClock;

/**
 * Loads the specified template with variables scoped to the template.
 *
 * @param string $name Name of the template file to include including extension.
 * @param array  $args Associative array of arguments that will be extracted into the template's scope.
 */
function load_template( $name, $args = [] ) {
	$file_path = GMEDIA_HOMEPAGE_COUNTDOWN_CLOCK_PATH . '/templates/' . $name;
	if ( file_exists( $file_path ) ) {
		extract( $args );
		require $file_path;
	}
}
