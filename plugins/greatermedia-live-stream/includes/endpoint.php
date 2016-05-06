<?php

// filter hooks
add_filter( 'rest_api_init', 'gmr_streams_init_api_endpoint' );
add_filter( 'determine_current_user', 'gmr_streams_json_basic_auth_handler', 20 );
add_filter( 'rest_authentication_errors', 'gmr_streams_json_basic_auth_error' );

/**
 * Registers API endpoint.
 *
 * @filter json_endpoints
 *
 * @param array $routes The initial array of routes.
 *
 * @return array Extended array of API routes.
 */
function gmr_streams_init_api_endpoint( $routes ) {
	register_rest_route( 'wp/v2', '/stream/(?P<sign>\S+)',
		array(
			'methods'  => 'POST',
			'callback' => 'gmr_streams_process_endpoint',
		)
	);
}

/**
 * Processes stream endpoing submission.
 *
 * @param string $sign The stream id.
 * @param array $data The song data.
 */
function gmr_streams_process_endpoint( /*$sign,*/ $data ) {
	$sign = $data->get_param( 'sign' );

	// an example of data:
	// {"artist": "Bruce Springsteen", "title": "Born to run", "purchase_link": "http://itunes.apple.com/album/born-to-run/id192810984?i=192811017&uo=5", "timestamp": "1417788996"}
	//
	// sample of curl command to test endpoint:
	// curl -u admin:password -X POST --data '{json}' {endpoint_url}

	$params = array(
		'artist' => $data->get_param( 'artist' ),
		'title' => $data->get_param( 'title' ),
		'purchase_link' => $data->get_param( 'purchase_link' ),
		'timestamp' => $data->get_param( 'timestamp' ),
	);

	$params = filter_var_array( $params, array(
		'artist'        => FILTER_DEFAULT,
		'title'         => FILTER_DEFAULT,
		'purchase_link' => FILTER_VALIDATE_URL,
		'timestamp'     => FILTER_VALIDATE_INT,
	) );

	// validate submitted data
	$validate = array(
		'timestamp' => 'Timestamp is invalid.',
		'artist'    => 'Artist is empty. Please, send non empty artist.',
		'title'     => 'Song title is empty. Please, send non empty song title.',
	);

	foreach ( $validate as $key => $error ) {
		if ( empty( $params[ $key ] ) ) {
			return new WP_Error( 'gmr_stream_bad_request', $error, array( 'status' => 400 ) );
		}
	}

	// fetch stream
	$query = new WP_Query( array(
		'post_type'           => GMR_LIVE_STREAM_CPT,
		'meta_key'            => 'call_sign',
		'meta_value'          => $sign,
		'posts_per_page'      => 1,
		'ignore_sticky_posts' => 1,
		'no_found_rows'       => true,
	) );

	if ( ! $query->have_posts() ) {
		return new WP_Error( 'gmr_stream_bad_request', 'The stream was not found.', array( 'status' => 400 ) );
	}

	$song = array(
		'post_type'     => GMR_SONG_CPT,
		'post_status'   => 'publish',
		'post_date'     => date( DATE_ISO8601, $params['timestamp'] + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ),
		'post_date_gmt' => date( DATE_ISO8601, $params['timestamp'] ),
		'post_parent'   => $query->next_post()->ID,
		'post_title'    => $params['title'],
	);

	$song_id = wp_insert_post( $song, true );
	$created = $song_id && ! is_wp_error( $song_id );
	if ( $created ) {
		update_post_meta( $song_id, 'artist', $params['artist'] );
		update_post_meta( $song_id, 'purchase_link', $params['purchase_link'] );
	}

	return new WP_REST_Response( $created, $created ? 201 : 400 );
}

/**
 * Authorizes an user using HTTP Basic Authorization method.
 *
 * @global WP_Error $wp_json_basic_auth_error The basic authorization error object.
 *
 * @param WP_User $user The current user object.
 *
 * @return WP_User|int The user id or object on success, otherwise null;
 */
function gmr_streams_json_basic_auth_handler( $user ) {
	global $wp_json_basic_auth_error;

	$wp_json_basic_auth_error = null;

	// Don't authenticate twice
	if ( ! empty( $user ) ) {
		return $user;
	}

	// Check that we're trying to authenticate
	if ( ! isset( $_SERVER['PHP_AUTH_USER'] ) ) {
		return $user;
	}

	$username = $_SERVER['PHP_AUTH_USER'];
	$password = $_SERVER['PHP_AUTH_PW'];

	/**
	 * In multi-site, wp_authenticate_spam_check filter is run on authentication. This filter calls
	 * get_currentuserinfo which in turn calls the determine_current_user filter. This leads to infinite
	 * recursion and a stack overflow unless the current function is removed from the determine_current_user
	 * filter during authentication.
	 */
	remove_filter( 'determine_current_user', 'gmr_streams_json_basic_auth_handler', 20 );

	$user = wp_authenticate( $username, $password );

	add_filter( 'determine_current_user', 'gmr_streams_json_basic_auth_handler', 20 );

	if ( is_wp_error( $user ) ) {
		$wp_json_basic_auth_error = new WP_Error( 'gmr_stream_not_authorized', strip_tags( $user->get_error_message() ), array( 'status' => 401 ) );

		return null;
	}

	$wp_json_basic_auth_error = true;

	return $user->ID;
}

/**
 * Returns Basic Authorization errors if exists any.
 *
 * @global WP_Error $wp_json_basic_auth_error The basic authorization error object.
 *
 * @param WP_Error $error The incoming error object or null.
 *
 * @return WP_Error The error object on failure, otherwise null.
 */
function gmr_streams_json_basic_auth_error( $error ) {
	// Passthrough other errors
	if ( ! empty( $error ) ) {
		return $error;
	}

	global $wp_json_basic_auth_error;

	return $wp_json_basic_auth_error;
}