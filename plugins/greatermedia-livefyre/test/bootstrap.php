<?php

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../vendor/autoload.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';

define( 'PHPUNIT_RUNNER', true );
define( 'INCLUDES', __DIR__ . '/../includes' );

define( 'GMR_LIVEFYRE_PLUGIN_FILE', __DIR__ . '/../greatermedia-livefyre.php' );
define( 'GMR_LIVEFYRE_PATH', dirname( GMR_LIVEFYRE_PLUGIN_FILE ) );
define( 'GMR_LIVEFYRE_URL', plugin_dir_url( GMR_LIVEFYRE_PLUGIN_FILE ) );
define( 'GMR_LIVEFYRE_VERSION', '0.1.0' );

require __DIR__ . '/../vendor/autoload.php';
