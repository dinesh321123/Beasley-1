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

	return $handler->run( $params );
}

function get_gigya_user_contest_data( $field = null, $default = null ) {
	static $contest_data = null;

	if ( is_null( $contest_data ) ) {
		$contest_data = array();
		if ( is_gigya_user_logged_in() ) {
			$user_id = get_gigya_user_id();
			$query = "SELECT * FROM actions WHERE data.actions.actionType = 'action:contest' AND UID = '{$user_id}' ORDER BY createdTime DESC LIMIT 20";

			$request = new \GreaterMedia\Gigya\GigyaRequest( null, null, 'ds.search' );
			$request->setParam( 'query', $query );

			$response = $request->send();
			$response = @json_decode( $response->getResponseText(), true );
			if ( ! $response || ! empty( $response['results'] ) ) {
				foreach ( $response['results'] as $result ) {
					if ( ! empty( $result['data']['actions'] ) ) {
						foreach ( $result['data']['actions'] as $action ) {
							if ( ! empty( $action['actionData'] ) ) {
								foreach ( $action['actionData'] as $data ) {
									if ( count( $data ) == 2 ) {
										$data = array_values( $data );
										if ( ! isset( $contest_data[ $data[0] ] ) ) {
											$contest_data[ $data[0] ] = $data[1];
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}

	if ( empty( $field ) ) {
		return $contest_data;
	}

	if ( isset( $contest_data[ $field ] ) ) {
		return $contest_data[ $field ];
	}

	return $default;
}

function has_email_entered_contest( $contest_id, $email ) {
	$handler = new \GreaterMedia\Gigya\Ajax\HasParticipatedAjaxHandler();
	$params = array(
		'contest_id' => $contest_id,
		'email' => $email,
	);

	return $handler->run( $params );
}
