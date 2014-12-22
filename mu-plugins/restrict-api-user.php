<?php
/**
 * Ensure the API user cant access the front end of the site.
 *
 * @todo This needs to be full-on roles/caps before launch
 */

function gmr_restrict_api_user_access() {
	$user = wp_get_current_user();

	$api_user_name = 'nowplayingwmgk';

	if ( ! defined( 'JSON_REQUEST' ) && $api_user_name == $user->data->user_login ) {
		wp_die("The current user is only authorized for API access");
	}
}
