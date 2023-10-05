<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class NationalContestGating {
	// static init function
	public static function init() {
		// add action to call engueue scripts function when enqueue scripts action is called
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
	}

	// enqueue script function that loads a single script called gate-national-contest.js
	public static function enqueue_scripts() {
		wp_enqueue_script( 'gate-national-contest', GENERAL_SETTINGS_CPT_URL . "assets/js/gate-national-contest.js", array('jquery'), GENERAL_SETTINGS_CPT_VERSION, true);
	}
}

// call init function
NationalContestGating::init();

