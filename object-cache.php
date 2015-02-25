<?php

/*
 * Don't run object cache under WP-CLI
 */

if ( ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require_once( __DIR__ . '/object-cache-real.php' );
}
