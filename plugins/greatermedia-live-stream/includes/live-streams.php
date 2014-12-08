<?php

// action hooks
add_action( 'init', 'gmr_streams_register_post_type' );
add_action( 'admin_menu', 'gmr_streams_update_admin_menu' );
add_action( 'save_post', 'gmr_streams_save_meta_box_data' );
add_action( 'manage_' . GMR_LIVE_STREAM_CPT . '_posts_custom_column', 'gmr_streams_render_custom_column', 10, 2 );
add_action( 'admin_action_gmr_stream_make_primary', 'gmr_streams_make_primary' );

// filter hooks
add_filter( 'manage_' . GMR_LIVE_STREAM_CPT . '_posts_columns', 'gmr_streams_filter_columns_list' );
add_filter( 'gmr_live_player_streams', 'gmr_streams_get_public_streams' );
add_filter( 'json_endpoints', 'gmr_streams_init_api_endpoint' );
add_filter( 'determine_current_user', 'gmr_streams_json_basic_auth_handler', 20 );
add_filter( 'json_authentication_errors', 'gmr_streams_json_basic_auth_error' );
add_filter( 'post_type_link', 'gmr_streams_get_stream_permalink', 10, 2 );
add_filter( 'request', 'gmr_streams_unpack_vars' );

/**
 * Registers API endpoint.
 *
 * @filter json_endpoints
 * @param array $routes The initial array of routes.
 * @return array Extended array of API routes.
 */
function gmr_streams_init_api_endpoint( $routes ) {
	$routes['/stream/(?P<sign>\S+)'] = array(
		array( 'gmr_streams_process_endpoint', WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
	);

	return $routes;
}

/**
 * Builds permalink for Live Stream object.
 *
 * @filter post_type_link 10 2
 * @param string $post_link The initial permalink
 * @param WP_Post $post The post object.
 * @return string The live stream permalink.
 */
function gmr_streams_get_stream_permalink( $post_link, $post ) {
	// do nothing if it is not a live stream post
	if ( GMR_LIVE_STREAM_CPT != $post->post_type ) {
		return $post_link;
	}

	// build permalink using call sign if available,
	// if call sign is unavailable, then use post id to build a permalink
	$call_sign = trim( get_post_meta( $post->ID, 'call_sign', true ) );
	if ( empty( $call_sign ) ) {
		$call_sign = $post->ID;
	}

	return home_url( "/stream/{$call_sign}/" );
}

/**
 * Unpacks query vars for live stream page.
 *
 * @filter request
 * @param array $query_vars The array of initial query vars.
 * @return array The array of unpacked query vars.
 */
function gmr_streams_unpack_vars( $query_vars ) {
	// do nothing if it is wrong page
	if ( empty( $query_vars[GMR_LIVE_STREAM_CPT] ) ) {
		return $query_vars;
	}

	// get stream object
	$query = new WP_Query( array(
		'post_type'           => GMR_LIVE_STREAM_CPT,
		'meta_key'            => 'call_sign',
		'meta_value'          => $query_vars[GMR_LIVE_STREAM_CPT],
		'posts_per_page'      => 1,
		'ignore_sticky_posts' => 1,
		'no_found_rows'       => true,
		'fields'              => 'ids',
	) );

	// do nothing if a stream has not been found
	if ( ! $query->have_posts() ) {
		return $query_vars;
	}

	// unpack query vars
	$query_vars['post_type'] = GMR_SONG_CPT;
	$query_vars['post_parent'] = $query->next_post();
	$query_vars['order'] = 'DESC';
	$query_vars['orderby'] = 'date';
	$query_vars['posts_per_page'] = 50;

	return $query_vars;
}

/**
 * Registers Live Stream post type.
 *
 * @action init
 * @global WP_Rewrite $wp_rewrite The rewrite rules object.
 * @global WP $wp The WP object.
 */
function gmr_streams_register_post_type() {
	global $wp_rewrite, $wp;

	// register post type
	register_post_type( GMR_LIVE_STREAM_CPT, array(
		'public'               => true,
		'exclude_from_search'  => true,
		'publicly_queryable'   => false,
		'show_ui'              => true,
		'show_in_nav_menus'    => false,
		'rewrite'              => false,
		'query_var'            => false,
		'can_export'           => false,
		'hierarchical'         => true,
		'menu_position'        => 5,
		'menu_icon'            => 'dashicons-format-audio',
		'supports'             => array( 'title' ),
		'register_meta_box_cb' => 'gmr_streams_register_meta_boxes',
		'label'                => 'Live Streams',
		'labels'               => array(
			'name'               => 'Live Streams',
			'singular_name'      => 'Live Stream',
			'menu_name'          => 'Live Streams',
			'name_admin_bar'     => 'Live Stream',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Stream',
			'new_item'           => 'New Stream',
			'edit_item'          => 'Edit Stream',
			'view_item'          => 'View Stream',
			'all_items'          => 'Streams',
			'search_items'       => 'Search Streams',
			'parent_item_colon'  => 'Parent Streams:',
			'not_found'          => 'No links found.',
			'not_found_in_trash' => 'No links found in Trash.',
		),
	) );

	// register rewrite rule and add query var
	$regex = '^stream/([^/]+)/?$';
	$wp_rewrite->add_rule( $regex, 'index.php?' . GMR_LIVE_STREAM_CPT . '=$matches[1]', 'top' );

	$wp->add_query_var( GMR_LIVE_STREAM_CPT );
	$rules = $wp_rewrite->wp_rewrite_rules();
	if ( ! isset( $rules[ $regex ] ) ) {
		flush_rewrite_rules();
	}
}

/**
 * Registers meta boxes for Live Stream post type.
 */
function gmr_streams_register_meta_boxes() {
	add_meta_box( 'call-sign', 'Call Sign', 'gmr_streams_render_call_sign_meta_box', GMR_LIVE_STREAM_CPT, 'normal', 'high' );
	add_meta_box( 'description', 'Description', 'gmr_streams_render_description_meta_box', GMR_LIVE_STREAM_CPT, 'normal' );
}

/**
 * Renders Call Sign meta box.
 *
 * @param WP_Post $post The stream post object.
 */
function gmr_streams_render_call_sign_meta_box( WP_Post $post ) {
	wp_nonce_field( 'gmr_stream_meta_boxes', '_gmr_stream_nonce', false );

	echo '<input type="text" name="stream_call_sign" class="widefat" value="', esc_attr( get_post_meta( $post->ID, 'call_sign', true ) ), '">';
	echo '<p class="description">Enter stream call sign, for instance WRIF-FM.</p>';
}

/**
 * Renders Description meta box.
 *
 * @param WP_Post $post The stream post object.
 */
function gmr_streams_render_description_meta_box( WP_Post $post ) {
	echo '<input type="text" name="stream_description" class="widefat" value="', esc_attr( get_post_meta( $post->ID, 'description', true ) ), '">';
	echo '<p class="description">Enter short description of the stream, for instance ', esc_html( '"Detroit\'s best rock all day and night, plus Dave and Chuck the Freak in the morning"' ), '.</p>';
}

/**
 * Saves Live Stream meta box data.
 *
 * @action save_post
 * @param int $post_id The post id.
 */
function gmr_streams_save_meta_box_data( $post_id ) {
	// validate nonce and user permissions
	$doing_autosave = defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;
	$valid_nonce = wp_verify_nonce( filter_input( INPUT_POST, '_gmr_stream_nonce' ), 'gmr_stream_meta_boxes' );
	$can_edit = current_user_can( 'edit_post', $post_id );
	if ( $doing_autosave || ! $valid_nonce || ! $can_edit ) {
		return;
	}

	// save call sign
	$call_sign = sanitize_text_field( filter_input( INPUT_POST, 'stream_call_sign' ) );
	update_post_meta( $post_id, 'call_sign', $call_sign );

	// save description
	$description = sanitize_text_field( filter_input( INPUT_POST, 'stream_description' ) );
	update_post_meta( $post_id, 'description', $description );
}

/**
 * Removes "Add New" sub menu item from "Live Streams" group.
 *
 * @action admin_menu
 */
function gmr_streams_update_admin_menu() {
	remove_submenu_page( 'edit.php?post_type=' . GMR_LIVE_STREAM_CPT, 'post-new.php?post_type=' . GMR_LIVE_STREAM_CPT );
}

/**
 * Adds Call Sign column to the Streams table.
 *
 * @fitler manage_gmr-live-stream_posts_columns
 * @param array $columns The columns array.
 * @return array Extended array of columns.
 */
function gmr_streams_filter_columns_list( $columns ) {
	$cut_mark = array_search( 'title', array_keys( $columns ) ) + 1;
	$new_columns = array(
		'call_sign' => 'Call Sign',
		'primary'   => 'Primary',
	);

	if ( defined( 'JSON_API_VERSION' ) ) {
		$new_columns['endpoint'] = 'Endpoint';
	}

	return array_merge(
		array_slice( $columns, 0, $cut_mark ),
		$new_columns,
		array_slice( $columns, $cut_mark )
	);
}

/**
 * Renders Call Sign column at the Streams table.
 *
 * @action manage_gmr-live-stream_posts_custom_column
 * @param string $column_name The column name to render.
 * @param int $post_id The current stream id.
 */
function gmr_streams_render_custom_column( $column_name, $post_id ) {
	switch ( $column_name ) {
		case 'call_sign':
			echo '<b>', esc_html( get_post_meta( $post_id, 'call_sign', true ) ), '</b>';
			break;
		case 'primary':
			$post = get_post( $post_id );
			if ( $post->menu_order == 1 ) {
				echo '<span class="dashicons dashicons-star-filled"></span>';
			} else {
				echo '<a href="', wp_nonce_url( 'admin.php?action=gmr_stream_make_primary&stream=' . $post_id, 'gmr_mark_primary_stream' ), '" title="Make Primary">';
					echo '<span class="dashicons dashicons-star-empty"></span>';
				echo '</a>';
			}
			break;
		case 'endpoint':
			$call_sign = trim( get_post_meta( $post_id, 'call_sign', true ) );
			if ( ! empty( $call_sign ) && function_exists( 'json_get_url_prefix' ) ) {
				$endpoint = home_url( sprintf( '/%s/stream/%s', json_get_url_prefix(), urlencode( $call_sign ) ) );
				printf( '<a href="%s" target="_blank">%s</a>', esc_url( $endpoint ), parse_url( $endpoint, PHP_URL_PATH ) );
			} else {
				echo '&#8212;';
			}
			break;
	}
}

/**
 * Marks a stream as primary.
 *
 * @action admin_action_gmr_stream_make_primary
 */
function gmr_streams_make_primary() {
	check_admin_referer( 'gmr_mark_primary_stream' );

	$stream_id = filter_input( INPUT_GET, 'stream', FILTER_VALIDATE_INT );
	if ( ! $stream_id || ! ( $stream = get_post( $stream_id ) ) || GMR_LIVE_STREAM_CPT != $stream->post_type ) {
		wp_die( 'Stream was not found.' );
	}

	$paged = 0;

	do {
		$paged++;
		$query = new WP_Query( array(
			'post_type'           => GMR_LIVE_STREAM_CPT,
			'post_status'         => 'any',
			'posts_per_page'      => 100,
			'paged'               => $paged,
			'ignore_sticky_posts' => true,
		) );

		while ( $query->have_posts() ) {
			$stream = $query->next_post();
			$stream->menu_order = $stream->ID == $stream_id ? 1 : 0;
			wp_update_post( $stream->to_array() );
		}
	} while ( $paged <= $query->max_num_pages );
	
	wp_redirect( wp_get_referer() );
	exit;
}

/**
 * Returns public streams.
 *
 * @filter gmr_live_player_streams
 * @return array The array of public streams.
 */
function gmr_streams_get_public_streams() {
	$paged = 0;
	$streams = array();

	do {
		$paged++;
		$query = new WP_Query( array(
			'post_type'           => GMR_LIVE_STREAM_CPT,
			'posts_per_page'      => 100,
			'paged'               => $paged,
			'ignore_sticky_posts' => true,
			'orderby'             => 'menu_order',
			'fields'              => 'ids',
		) );

		while ( $query->have_posts() ) {
			$stream_id = $query->next_post();

			$call_sign = get_post_meta( $stream_id, 'call_sign', true );
			if ( empty( $call_sign ) ) {
				continue;
			}

			$streams[ $call_sign ] = get_post_meta( $stream_id, 'description', true );
		}
	} while ( $paged <= $query->max_num_pages );
	
	return $streams;
}

/**
 * Processes stream endpoing submission.
 *
 * @param string $sign The stream id.
 * @param array $data The song data.
 */
function gmr_streams_process_endpoint( $sign, $data ) {
	// an example of data:
	// {"artist": "Bruce Springsteen", "title": "Born to run", "purchase_link": "http://itunes.apple.com/album/born-to-run/id192810984?i=192811017&uo=5", "timestamp": "1417788996"}
	//
	// sample of curl command to test endpoint:
	// curl -u admin:password -X POST --data '{json}' {endpoint_url}

	$data = filter_var_array( $data, array(
		'artist'        => FILTER_DEFAULT,
		'title'         => FILTER_DEFAULT,
		'purchase_link' => FILTER_VALIDATE_URL,
		'timestamp'     => FILTER_VALIDATE_INT,
	) );

	if ( empty( $data['timestamp'] ) ) {
		return new WP_Error( 'gmr_stream_wrong_aired_at', 'Timestamp is invalid.', array( 'status' => 400 ) );
	}

	$query = new WP_Query( array(
		'post_type'           => GMR_LIVE_STREAM_CPT,
		'meta_key'            => 'call_sign',
		'meta_value'          => $sign,
		'posts_per_page'      => 1,
		'ignore_sticky_posts' => 1,
		'no_found_rows'       => true,
	) );

	if ( ! $query->have_posts() ) {
		return new WP_Error( 'gmr_stream_not_found', 'The stream was not found.', array( 'status' => 404 ) );
	}

	$song = array(
		'post_type'     => GMR_SONG_CPT,
		'post_status'   => 'publish',
		'post_date'     => date( DATE_ISO8601, $data['timestamp'] + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ),
		'post_date_gmt' => date( DATE_ISO8601, $data['timestamp'] ),
		'post_parent'   => $query->next_post()->ID,
		'post_title'    => $data['title'],
	);

	$song_id = wp_insert_post( $song, true );
	$created = $song_id && ! is_wp_error( $song_id );
	if ( $created ) {
		update_post_meta( $song_id, 'artist', $data['artist'] );
		update_post_meta( $song_id, 'purchase_link', $data['purchase_link'] );
	}

	$response = new WP_JSON_Response();
	$response->set_status( $created ? 201 : 400 );
	
	return $response;
}

/**
 * Authorizes an user using HTTP Basic Authorization method.
 *
 * @global WP_Error $wp_json_basic_auth_error The basic authorization error object.
 * @param WP_User $user The current user object.
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
	if ( !isset( $_SERVER['PHP_AUTH_USER'] ) ) {
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
		$wp_json_basic_auth_error = $user;
		return null;
	}

	$wp_json_basic_auth_error = true;

	return $user->ID;
}

/**
 * Returns Basic Authorization errors if exists any.
 * 
 * @global WP_Error $wp_json_basic_auth_error The basic authorization error object.
 * @param WP_Error $error The incoming error object or null.
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