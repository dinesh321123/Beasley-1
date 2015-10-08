<?php

$_tests_dir = getenv('WP_TESTS_DIR');
if ( !$_tests_dir ) $_tests_dir = '/tmp/wordpress-tests-lib';

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
  require dirname(__FILE__) . '/../vendor/autoload.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';

define('PHPUNIT_RUNNER', true);
define('INCLUDES', __DIR__ . '/../includes');
define( 'GMR_GIGYA_URL', plugin_dir_url( __DIR__ . '/../greatermedia-gigya.php' ) );
define( 'GMR_GIGYA_PATH', realpath( dirname( __FILE__ ) . '/../' ) . '/' );
define( 'GMR_GIGYA_VERSION', '0.1.0' );

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/MockAsyncTask.php';
