<?php
/*
Plugin Name: CPT - General admin settings
Description: Settings to on/off draft kings iFrame, footer description, dashboard activity, whiz, newsletter signup form etc...
Version: 0.0.1
Author: Rupesh Jorkar (RJ)
Author URI: https://bbgi.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
define( 'GENERAL_SETTINGS_CPT_VERSION', '0.0.2' );
define( 'GENERAL_SETTINGS_CPT_URL', plugin_dir_url( __FILE__ ) );
define( 'GENERAL_SETTINGS_CPT_PATH', dirname( __FILE__ ) );
define( 'GENERAL_SETTINGS_CPT_TEXT_DOMAIN', 'general_settings_textdomain' );

$iframe_height =  get_option( 'configurable_iframe_height', '0' );

if ( ! empty( $iframe_height ) ) :
	include __DIR__ . '/includes/draftking-iframe-settings.php';
endif;

include __DIR__ . '/includes/embed_videourl.php';	// Code to add Embed Video URL Field in media

include __DIR__ . '/includes/settings.php';
include __DIR__ . '/includes/rendering.php';
include __DIR__ . '/includes/footer-description-settings.php';
include __DIR__ . '/includes/dashboard-activity.php';
include __DIR__ . '/includes/whiz-changes.php' ;
include __DIR__ . '/includes/duplicate-listicle.php';
include __DIR__ . '/includes/newsletter-signup-form.php';

/** This should be removed after we confirm if logging the shutdown hook is successful */
include __DIR__ . '/includes/shutdown-elasticsearch-log.php';

include __DIR__ . '/includes/bbgi-station-settings.php';	

/* Include the trending-article.php file for the trending article shortcode functionality. */
include __DIR__ . '/includes/trending-article.php';