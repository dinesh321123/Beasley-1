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

function get_gigya_user_profile() {
	return get_gigya_session()->get_user_profile();
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

	return $handler->run( $params );
}

function has_email_entered_contest( $contest_id, $email ) {
	$handler = new \GreaterMedia\Gigya\Ajax\HasParticipatedAjaxHandler();
	$params = array(
		'contest_id' => $contest_id,
		'email' => $email,
	);

	return $handler->run( $params );
}
