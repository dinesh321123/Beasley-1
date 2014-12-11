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
