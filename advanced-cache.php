<?php

/*
 * Don't run advanced cache under Gearman
 */

if ( ! ( defined( 'DOING_ASYNC' ) && DOING_ASYNC ) ) {
	require_once( __DIR__ . '/advanced-cache-real.php' );
} else {
	global $batcache;
	$batcache = null;
}
