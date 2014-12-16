<?php
/*
Plugin Name: Greater Media LiveFyre
Description: LiveFyre integration with Gigya
Author: 10up
*/

define( 'GMR_LIVEFYRE_PATH', dirname( __FILE__ ) );
define( 'GMR_LIVEFYRE_URL', plugin_dir_url( __FILE__ ) );
define( 'GMR_LIVEFYRE_PLUGIN_FILE', __FILE__ );
define( 'GMR_LIVEFYRE_VERSION', '0.1.0' );

function gmr_livefyre_main_real() {
	require_once __DIR__ . '/vendor/autoload.php';

	$plugin = new \GreaterMedia\LiveFyre\Plugin();
	$plugin->enable();
}

function gmr_livefyre_main() {
	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		gmr_livefyre_main_real();
	} else {
		$install_dir = GMR_LIVEFYRE_PATH;
		error_log(
			"Error: Composer packages not found, Please run $ composer install in {$install_dir}."
		);
	}
}

gmr_livefyre_main();

