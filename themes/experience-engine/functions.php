<?php

/*
 * Add this constant to wp-config and set value to "dev" to trigger time() as the cache buster on css/js that use this,
 * instead of the version - useful for dev, especially when cloudflare or other cdn's are involved
 */
$version = time();

// If .version.php file exists, the content of this file (timestamp) is added to the $version value set above
if ( file_exists( __DIR__ . '/../.version.php' ) ) {
	$suffix  = intval( file_get_contents( __DIR__ . '/../.version.php' ) );
	$version = $suffix;
}

define( 'GREATERMEDIA_VERSION', $version ); // using this constant for backward compatibility

require_once __DIR__ . '/includes/theme.php';
require_once __DIR__ . '/includes/experience-engine.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/assets.php';
require_once __DIR__ . '/includes/podcasts.php';
require_once __DIR__ . '/includes/contests.php';
require_once __DIR__ . '/includes/shows.php';
require_once __DIR__ . '/includes/class-primary-nav-walker.php';
require_once __DIR__ . '/includes/jacapps.php';
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/embeds.php';
require_once __DIR__ . '/includes/dfp.php';
require_once __DIR__ . '/includes/homepage.php';
require_once __DIR__ . '/includes/galleries.php';
require_once __DIR__ . '/includes/rest-api.php';
