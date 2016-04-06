<?php
/*
Plugin Name: Greater Media Gigya
Description: Greater Media Gigya
Author: 10up
*/

define( 'GMR_GIGYA_URL', plugin_dir_url( __FILE__ ) );
define( 'GMR_GIGYA_PATH', dirname( __FILE__ ) . '/' );
define( 'GMR_GIGYA_PLUGIN_FILE', __FILE__ );
define( 'GMR_GIGYA_VERSION', '0.30.2' );

/* JOB DB details - behind an ifdef to allow wp-config to override in production */
if ( ! defined( 'GMR_JOB_DB' ) ) {
	define( 'GMR_JOB_DB', false );

	/* if JOB_DB = true, the following constants MUST be provided */
	/*
	define( 'GMR_JOB_DB_USER', 'gmr_job_db_user' );
	define( 'GMR_JOB_DB_PASSWORD', '1234' );
	define( 'GMR_JOB_DB_NAME', 'gmr_job_db_test' );
	define( 'GMR_JOB_DB_HOST', 'localhost' );
	*/
}

function gmr_gigya_main() {
	$plugin = new \GreaterMedia\Gigya\Plugin( __FILE__ );
	$plugin->enable();

	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		$gigya_wp_cli_loader = new \GreaterMedia\Gigya\Commands\Loader();
		$gigya_wp_cli_loader->load();
	}
}

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
	gmr_gigya_main();
} else {
	error_log(
		'Error: Composer packages not found, Please run $ composer install.'
	);
}
