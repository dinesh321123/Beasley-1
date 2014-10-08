<?php

function get_gigya_session() {
	return \GreaterMedia\Gigya\GigyaSession::get_instance();
}

function is_gigya_user_logged_in() {
	return get_gigya_session()->is_logged_in();
}

function gigya_user_id() {
	return get_gigya_session()->get_user_id();
}
