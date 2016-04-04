<?php

/*
Plugin Name: Greater Media Homepage Countdown Clock
Description: Homepage Countdown Clock Plugin
Version: 1.0
Author: Greater Media
Author URI: http://greatermedia.com
Text Domain: greatermedia-homepage-countdown-clock
*/

/**
* Themes *must* declare support for homepage countdown clock using add_theme_support( 'homepage-countdown-clock' )
*/

namespace GreaterMedia\HomepageCountdownClock;

define( 'GMEDIA_HOMEPAGE_COUNTDOWN_CLOCK_VERSION', '1.0.0' );
define( 'GMEDIA_HOMEPAGE_COUNTDOWN_CLOCK_URL', plugin_dir_url( __FILE__ ) );
define( 'GMEDIA_HOMEPAGE_COUNTDOWN_CLOCK_PATH', plugin_dir_path( __FILE__ ) );
define( 'GMR_COUNTDOWN_CLOCK_CPT', 'gmr_countdown_clock' );

function load() {
	if ( ! current_theme_supports( 'homepage-countdown-clock' ) ) {
		return;
	}

	require GMEDIA_HOMEPAGE_COUNTDOWN_CLOCK_PATH . '/includes/functions.php';
	include __DIR__ . '/includes/homepage_countdown_clock_cpt.php';
	include __DIR__ . '/includes/queries.php';
	include __DIR__ . '/includes/class-greatermedia-countdown-clock-metaboxes.php';
}
\add_action ( 'after_setup_theme', __NAMESPACE__ . '\load' );
