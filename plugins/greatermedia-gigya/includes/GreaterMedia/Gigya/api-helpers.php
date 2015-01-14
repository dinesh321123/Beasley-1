<?php

/* Session Helpers */
function get_gigya_session() {
	return \GreaterMedia\Gigya\GigyaSession::get_instance();
}

function is_gigya_user_logged_in() {
	return get_gigya_session()->is_logged_in();
}

function get_gigya_user_id() {
	return get_gigya_session()->get_user_id();
}

function get_gigya_user_field( $field ) {
	return get_gigya_session()->get_user_field( $field );
}

function get_gigya_user_profile( $user_id = null ) {
	return get_gigya_session()->get_user_profile( $user_id );
}

/* Action Helpers */
function get_gigya_action_dispatcher() {
	return \GreaterMedia\Gigya\Action\Dispatcher::get_instance();
}

function save_gigya_action( $action ) {
	get_gigya_action_dispatcher()->save_action( $action );
}

function save_gigya_actions( $actions ) {
	get_gigya_action_dispatcher()->save_actions( $actions );
}

/* Profile Path Helpers */
function gigya_profile_path( $action_name, $params = null ) {
	$profile_path = \GreaterMedia\Gigya\ProfilePath::get_instance();
	return $profile_path->path_for( $action_name, $params );
}

/* Contest Helpers */
function has_user_entered_contest( $contest_id ) {
	$handler = new \GreaterMedia\Gigya\Ajax\HasParticipatedAjaxHandler();
	$params = array(
		'contest_id' => $contest_id,
	);

	return $handler->run( $params, 'contest' );
}

/* Survey Helpers */
function has_user_entered_survey( $survey_id ) {
	$handler = new \GreaterMedia\Gigya\Ajax\HasParticipatedAjaxHandler();
	$params = array(
		'contest_id' => $survey_id,
	);

	return $handler->run( $params, 'survey' );
}

function has_email_entered_contest( $contest_id, $email ) {
	$handler = new \GreaterMedia\Gigya\Ajax\HasParticipatedAjaxHandler();
	$params = array(
		'contest_id' => $contest_id,
		'email' => $email,
	);

	return $handler->run( $params );
}

/* Profile Data helpers ( ie:- account.data not DS.Store ) */
function get_gigya_user_data_field( $user_id, $field ) {
	return get_gigya_session()->get_user_data_field( $user_id, $field );
}

function set_gigya_user_data_field( $user_id, $field, $value ) {
	return get_gigya_session()->set_user_data_field( $user_id, $field, $value );
}

function get_gigya_user_profile_data( $user_id ) {
	return get_gigya_session()->get_user_profile_data( $user_id );
}

function set_gigya_user_profile_data( $user_id, $data ) {
	return get_gigya_session()->set_user_profile_data( $user_id, $data );
}
