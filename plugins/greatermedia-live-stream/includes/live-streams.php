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
add_filter( 'post_type_link', 'gmr_streams_get_stream_permalink', 10, 2 );
add_filter( 'request', 'gmr_streams_unpack_vars' );
add_filter( 'gmr_live_link_suggestion_post_types', 'gmr_streams_add_suggestion_post_type' );
add_filter( 'bbgiconfig', 'gmr_update_bbgiconfig' );

/**
 * Registers live stream post types in the live links suggestions post types array.
 *
 * @filter gmr_live_link_suggestion_post_types
 *
 * @param array $post_types The array of already registered post types.
 *
 * @return array The extended version of incoming array, which contains live stream post type.
 */
function gmr_streams_add_suggestion_post_type( $post_types ) {
	$post_types[] = GMR_LIVE_STREAM_CPT;

	return $post_types;
}

/**
 * Builds permalink for Live Stream object.
 *
 * @filter post_type_link 10 2
 *
 * @param string $post_link The initial permalink
 * @param WP_Post $post The post object.
 *
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
 *
 * @param array $query_vars The array of initial query vars.
 *
 * @return array The array of unpacked query vars.
 */
function gmr_streams_unpack_vars( $query_vars ) {
	global $gmr_last_song, $gmr_moved_song;

	// do nothing if it is wrong page
	if ( empty( $query_vars[ GMR_LIVE_STREAM_CPT ] ) ) {
		return $query_vars;
	}

	// fetch stream
	$stream_id   = false;
	$stream_sign = $query_vars[ GMR_LIVE_STREAM_CPT ];
	$query       = new WP_Query( array(
		'post_type'           => GMR_LIVE_STREAM_CPT,
		'meta_key'            => 'call_sign',
		'meta_value'          => $stream_sign,
		'posts_per_page'      => 1,
		'ignore_sticky_posts' => 1,
		'no_found_rows'       => true,
		'fields'              => 'ids',
	) );

	if ( ! $query->have_posts() && is_numeric( $stream_sign ) ) {
		$stream = get_post( $stream_sign );
		if ( $stream && GMR_LIVE_STREAM_CPT == $stream->post_type ) {
			$stream_id = $stream->ID;
		}
	} else {
		$stream_id = $query->next_post();
	}

	// unpack query vars if stream has been found
	if ( ! empty( $stream_id ) ) {
		$query_vars['post_type']      = GMR_SONG_CPT;
		$query_vars['post_parent']    = $stream_id;
		$query_vars['order']          = 'DESC';
		$query_vars['orderby']        = 'date';
		$query_vars['posts_per_page'] = 50;

		if ( ! empty( $query_vars['paged'] ) && $query_vars['paged'] > 1 ) {
			$qv = $query_vars;
			$qv['paged'] --;
			$qv['fields'] = 'ids';

			$songs = $query->query( $qv );
			if ( ! empty( $songs ) ) {
				$gmr_last_song = get_post( array_pop( $songs ) );
				$pre_last_song = get_post( array_pop( $songs ) );

				remove_filter( 'get_post_time', array( 'TribeEventsTemplates', 'event_date_to_pubDate' ), 10, 3 );

				if ( get_the_time( 'M j', $pre_last_song ) != get_the_time( 'M j', $gmr_last_song ) ) {
					$gmr_moved_song = $gmr_last_song;
					$gmr_last_song  = $pre_last_song;
				}
			}
		}
	}

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
		'public'                => true,
		'exclude_from_search'   => true,
		'publicly_queryable'    => false,
		'show_in_rest'          => true,
		'rest_base'             => 'stream',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
		'show_ui'               => true,
		'show_in_nav_menus'     => false,
		'show_in_menu'          => 'options-general.php',
		'rewrite'               => false,
		'query_var'             => false,
		'can_export'            => false,
		'hierarchical'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-format-audio',
		'supports'              => array( 'title' ),
		'register_meta_box_cb'  => 'gmr_streams_register_meta_boxes',
		'label'                 => 'Live Streams',
		'labels'                => array(
			'name'               => 'Live Streams',
			'singular_name'      => 'Live Stream',
			'menu_name'          => 'Live Streams',
			'name_admin_bar'     => 'Live Stream',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Stream',
			'new_item'           => 'New Stream',
			'edit_item'          => 'Edit Stream',
			'view_item'          => 'View Stream',
			'all_items'          => 'Live Streams',
			'search_items'       => 'Search Streams',
			'parent_item_colon'  => 'Parent Streams:',
			'not_found'          => 'No links found.',
			'not_found_in_trash' => 'No links found in Trash.',
		),
	) );

	// register rewrite rule and add query var
	$new_rules = array(
		'^stream/([^/]+)/?$'                 => 'index.php?' . GMR_LIVE_STREAM_CPT . '=$matches[1]',
		'^stream/(.+?)/page/?([0-9]{1,})/?$' => 'index.php?' . GMR_LIVE_STREAM_CPT . '=$matches[1]&paged=$matches[2]',
	);

	foreach ( $new_rules as $rule => $rewrite ) {
		$wp_rewrite->add_rule( $rule, $rewrite, 'top' );
	}

	$wp->add_query_var( GMR_LIVE_STREAM_CPT );
	$all_rules  = $wp_rewrite->wp_rewrite_rules();
	$rules_diff = array_diff_key( $new_rules, $all_rules );
	if ( ! empty( $rules_diff ) ) {
		$wp_rewrite->flush_rules();
	}
}

/**
 * Registers meta boxes for Live Stream post type.
 */
function gmr_streams_register_meta_boxes() {
	add_meta_box( 'call-sign', 'Call Sign', 'gmr_streams_render_call_sign_meta_box', GMR_LIVE_STREAM_CPT, 'normal', 'high' );
	add_meta_box( 'description', 'Description', 'gmr_streams_render_description_meta_box', GMR_LIVE_STREAM_CPT, 'normal', 'high' );
	add_meta_box( 'station-id', 'Station ID', 'gmr_streams_render_stationid_meta_box', GMR_LIVE_STREAM_CPT, 'normal', 'high' );
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
 * Renders Station ID meta box.
 *
 * @param WP_Post $post The stream post object.
 */
function gmr_streams_render_stationid_meta_box( WP_Post $post ) {
	echo '<input type="text" name="station_id" class="widefat" value="', esc_attr( get_post_meta( $post->ID, 'station_id', true ) ), '">';
	echo '<p class="description">Enter the Station ID for the stream.</p>';
}

/**
 * Saves Live Stream meta box data.
 *
 * @action save_post
 *
 * @param int $post_id The post id.
 */
function gmr_streams_save_meta_box_data( $post_id ) {
	// validate nonce and user permissions
	$doing_autosave = defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;
	$valid_nonce    = wp_verify_nonce( filter_input( INPUT_POST, '_gmr_stream_nonce' ), 'gmr_stream_meta_boxes' );
	$can_edit       = current_user_can( 'edit_post', $post_id );
	if ( $doing_autosave || ! $valid_nonce || ! $can_edit ) {
		return;
	}

	// save call sign
	$call_sign = sanitize_text_field( filter_input( INPUT_POST, 'stream_call_sign' ) );
	update_post_meta( $post_id, 'call_sign', $call_sign );

	// save description
	$description = sanitize_text_field( filter_input( INPUT_POST, 'stream_description' ) );
	update_post_meta( $post_id, 'description', $description );

	$station_id = sanitize_text_field( filter_input( INPUT_POST, 'station_id' ) );
	update_post_meta( $post_id, 'station_id', $station_id );
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
 *
 * @param array $columns The columns array.
 *
 * @return array Extended array of columns.
 */
function gmr_streams_filter_columns_list( $columns ) {
	$cut_mark    = array_search( 'title', array_keys( $columns ) ) + 1;
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
 *
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
		$paged ++;
		$query = new WP_Query( array(
			'post_type'           => GMR_LIVE_STREAM_CPT,
			'post_status'         => 'any',
			'posts_per_page'      => 100,
			'paged'               => $paged,
			'ignore_sticky_posts' => true,
		) );

		while ( $query->have_posts() ) {
			$stream             = $query->next_post();
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
	$found = false;
	$key = 'public-streams';
	$streams = wp_cache_get( $key, 'bbgi', false, $found);
	if ( ! $found ) {
		$paged   = 0;

		do {
			$paged ++;
			$query = new WP_Query( array(
				'post_type'           => GMR_LIVE_STREAM_CPT,
				'post_status'         => 'publish',
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

				$streams[ $call_sign ] = array(
					'description' => get_post_meta( $stream_id, 'description', true ),
					'station_id'  => get_post_meta( $stream_id, 'station_id', true ),
				);
			}
		} while ( $paged <= $query->max_num_pages );

		wp_cache_set( $key, $streams, 'bbgi', 10 * MINUTE_IN_SECONDS );
	}

	return is_array( $streams ) ? $streams : array();
}

/**
 * Returns live stream based on its sign.
 *
 * @param string $sign The stream call sign.
 *
 * @return WP_Post The stream object on success, otherwise NULL.
 */
function gmr_streams_get_stream_by_sign( $sign ) {
	static $streams = array();

	if ( ! array_key_exists( $sign, $streams ) ) {
		$query = new WP_Query( array(
			'post_type'           => GMR_LIVE_STREAM_CPT,
			'meta_key'            => 'call_sign',
			'meta_value'          => $sign,
			'posts_per_page'      => 1,
			'ignore_sticky_posts' => 1,
			'no_found_rows'       => true,
		) );

		$streams[ $sign ] = $query->have_posts()
			? $query->next_post()
			: null;
	}

	return $streams[ $sign ];
}

/**
 * Returns primary stream.
 *
 * @return \WP_Post The primary stream if exists, otherwise FALSE.
 */
function gmr_streams_get_primary_stream() {
	$found = false;
	$key = 'primary-stream-id';
	$primary_id = wp_cache_get( $key, 'bbgi', false, $found );
	if ( ! $found ) {
		$query = new WP_Query( array(
			'post_type'           => GMR_LIVE_STREAM_CPT,
			'post_status'         => 'publish',
			'order'               => 'DESC',
			'orderby'             => 'menu_order',
			'posts_per_page'      => 1,
			'ignore_sticky_posts' => 1,
			'no_found_rows'       => true,
			'fields'              => 'ids',
		) );

		$primary_id = $query->have_posts() ? $query->next_post() : 0;
		wp_cache_set( $key, $primary_id, 'bbgi' );
	}

	return $primary_id > 0 ? get_post( $primary_id ) : null;
}

function gmr_streams_get_primary_stream_callsign() {
	static $callsign = null;

	if ( is_null( $callsign ) ) {
		$stream = gmr_streams_get_primary_stream();
		$callsign = is_a( $stream, '\WP_Post' )
			? get_post_meta( $stream->ID, 'call_sign', true )
			: '';
	}

	return $callsign;
}

function gmr_streams_get_primary_stream_vast_url() {
	static $vast_url = null;

	if ( is_null( $vast_url ) ) {
		$stream = gmr_streams_get_primary_stream();
		$vast_url = is_a( $stream, '\WP_Post' )
			? get_post_meta( $stream->ID, 'vast_url', true )
			: '';
	}

	return $vast_url;
}

function gmr_update_bbgiconfig( $settings ) {
	$settings['streams'] = gmr_streams_get_public_streams();
	return $settings;
}
