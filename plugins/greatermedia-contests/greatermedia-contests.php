<?php
/*
Plugin Name: Greater Media Contests
Description: Contest Features
Version: 1.0.4
Author: 10up
Author URI: http://10up.com
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

define( 'GREATER_MEDIA_CONTESTS_URL', plugin_dir_url( __FILE__ ) );
define( 'GREATER_MEDIA_CONTESTS_PATH', dirname( __FILE__ ) );
define( 'GREATER_MEDIA_CONTESTS_VERSION', '1.0.4' );

define( 'GMR_CONTEST_CPT',         'contest' );
define( 'GMR_CONTEST_ENTRY_CPT',   'contest_entry' );
define( 'GMR_SUBMISSIONS_CPT',     'listener_submissions' );
define( 'GMR_SURVEY_CPT',          'survey' );
define( 'GMR_SURVEY_RESPONSE_CPT', 'survey_response' );

define( 'EP_GMR_CONTEST', EP_PAGES << 1 );
define( 'EP_GMR_SURVEY', EP_PAGES << 2 );

include 'inc/contests.php';
include 'inc/surveys.php';
include 'inc/class-greatermedia-contests.php';
include 'inc/class-greatermedia-contest-entry.php';
include 'inc/class-greatermedia-contest-entry-embedded-form.php';
include 'inc/class-greatermedia-formbuilder-render.php';
include 'inc/class-greatermedia-ugc.php';
include 'inc/class-greatermedia-uggallery.php';
include 'inc/class-greatermedia-ugimage.php';
include 'inc/class-greatermedia-uglink.php';
include 'inc/class-ugc-moderation-table.php';
include 'inc/class-greatermedia-surveys.php';
include 'inc/class-greatermedia-survey-form-render.php';
include 'inc/class-greatermedia-survey-entry.php';

if ( is_admin() ) {
	include 'inc/class-greatermedia-contests-metaboxes.php';
	include 'inc/class-greatermedia-contests-template-actions.php';
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	include 'inc/class-greatermedia-contests-wp-cli.php';
}

register_activation_hook( __FILE__, 'flush_rewrite_rules' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
