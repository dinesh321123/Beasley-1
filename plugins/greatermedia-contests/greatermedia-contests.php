<?php
/*
Plugin Name: Greater Media Contests
Description: Contest Features
Version: 1.2.0
Author: 10up
Author URI: http://10up.com
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

define( 'GREATER_MEDIA_CONTESTS_URL', plugin_dir_url( __FILE__ ) );
define( 'GREATER_MEDIA_CONTESTS_PATH', dirname( __FILE__ ) );
define( 'GREATER_MEDIA_CONTESTS_VERSION', '1.2.0' );

define( 'GMR_CONTEST_CPT',         'contest' );
define( 'GMR_CONTEST_ENTRY_CPT',   'contest_entry' );
define( 'GMR_SUBMISSIONS_CPT',     'listener_submissions' );
define( 'GMR_SURVEY_CPT',          'survey' );
define( 'GMR_SURVEY_RESPONSE_CPT', 'survey_response' );

define( 'EP_GMR_CONTEST', EP_PAGES << 1 );
define( 'EP_GMR_SURVEY', EP_PAGES << 2 );

include 'inc/functions.php';
include 'inc/contests.php';
include 'inc/surveys.php';
include 'inc/class-greatermedia-contest-entry.php';
include 'inc/class-greatermedia-contest-entry-embedded-form.php';
include 'inc/class-greatermedia-formbuilder-render.php';
include 'inc/class-greatermedia-ugc.php';
include 'inc/class-greatermedia-uggallery.php';
include 'inc/class-greatermedia-ugimage.php';
include 'inc/class-greatermedia-uglink.php';
include 'inc/class-ugc-moderation-table.php';
include 'inc/class-greatermedia-surveys.php';
include 'inc/class-greatermedia-survey-entry.php';

if ( is_admin() || ( defined( 'DOING_ASYNC' ) && DOING_ASYNC ) ) {
	// include list table class files if it hasn't been included yet
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-posts-list-table.php';

	include 'inc/class-greatermedia-contests-metaboxes.php';
	include 'inc/contest-winner.php';
	include 'inc/survey-responses.php';
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	include 'inc/class-greatermedia-contests-wp-cli.php';
}

register_activation_hook( __FILE__, 'gmr_contests_activated' );
register_deactivation_hook( __FILE__, 'gmr_contests_deactivated' );

function gmr_contests_activated() {
	gmr_contests_register_post_type();
	\GreaterMediaContestEntry::contest_entry();
	\GreaterMediaUserGeneratedContent::user_generated_content();

	$surveys = new GreaterMediaSurveys();
	$surveys->register_survey_cpt();

	\GreaterMediaSurveyEntry::register_survey_response_cpt();

	load_capabilities( GMR_CONTEST_CPT );
	load_capabilities( GMR_CONTEST_ENTRY_CPT );
	load_capabilities( GMR_SUBMISSIONS_CPT );
	load_capabilities( GMR_SURVEY_CPT );
	load_capabilities( GMR_SURVEY_RESPONSE_CPT );

	$admin = get_role( 'administrator' );
	$admin->add_cap( 'export_contest_entries', true );
	$admin->add_cap( 'export_survey_responses', true );

	flush_rewrite_rules();
}

function gmr_contests_deactivated() {
	unload_capabilities( GMR_CONTEST_CPT );
	unload_capabilities( GMR_CONTEST_ENTRY_CPT );
	unload_capabilities( GMR_SUBMISSIONS_CPT );
	unload_capabilities( GMR_SURVEY_CPT );
	unload_capabilities( GMR_SURVEY_RESPONSE_CPT );

	$admin = get_role( 'administrator' );
	$admin->remove_cap( 'export_contest_entries', true );
	$admin->remove_cap( 'export_survey_responses', true );

	flush_rewrite_rules();
}
