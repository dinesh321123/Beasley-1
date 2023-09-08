<?php
/*
Plugin Name: Listicle Selection
Plugin URI:
Description: Import Existing listicles in the post as short code
Version: 0.0.1
Author: Surjit Vala (SV)
Author URI: https://bbgi.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}


define( 'LISTICLE_SELECTION_VERSION', '1.0.0' );
define( 'LISTICLE_SELECTION_URL', plugin_dir_url( __FILE__ ) );

include __DIR__ . '/includes/select-listicle.php';


