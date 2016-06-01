<?php

// action hooks
add_action( 'init', 'gmr_contests_register_post_type' );
add_action( 'init', 'gmr_contests_register_rewrites_and_endpoints', 100 );
add_action( 'wp_enqueue_scripts', 'gmr_contests_enqueue_front_scripts', 100 );
add_action( 'template_redirect', 'gmr_contests_process_action' );
add_action( 'template_redirect', 'gmr_contests_process_submission_action' );
add_action( 'manage_' . GMR_CONTEST_CPT . '_posts_custom_column', 'gmr_contests_render_contest_column', 10, 2 );
add_action( 'before_delete_post', 'gmr_contests_prevent_hard_delete' );
add_action( 'wp_trash_post', 'gmr_contests_prevent_hard_delete' );
add_action( 'transition_post_status', 'gmr_contests_prevent_trash_transition', 10, 3 );
add_action( 'admin_enqueue_scripts', 'gmr_contests_admin_enqueue_scripts' );

add_action( 'gmr_contest_load', 'gmr_contests_render_form' );
add_action( 'gmr_contest_submit', 'gmr_contests_process_form_submission' );
add_action( 'gmr_contest_confirm-age', 'gmr_contests_confirm_user_age' );
add_action( 'gmr_contest_vote', 'gmr_contests_vote_for_submission' );
add_action( 'gmr_contest_unvote', 'gmr_contests_unvote_for_submission' );

// filter hooks
add_filter( 'map_meta_cap', 'gmr_contests_map_meta_cap', 10, 4 );
add_filter( 'ajax_query_attachments_args', 'gmr_contests_adjuste_attachments_query' );
add_filter( 'gmr_contest_submissions_query', 'gmr_contests_submissions_query' );
add_filter( 'post_type_link', 'gmr_contests_get_submission_permalink', 10, 2 );
add_filter( 'request', 'gmr_contests_unpack_vars' );
add_filter( 'wp_link_query_args', 'gmr_contests_exclude_ugc_from_editor_links_query' );
add_filter( 'gmr-homepage-curation-post-types', 'gmr_contest_register_curration_post_type' );
add_filter( 'gmr-show-curation-post-types', 'gmr_contest_register_curration_post_type' );
add_filter( 'post_thumbnail_html', 'gmr_contests_post_thumbnail_html', 10, 4 );
add_filter( 'manage_' . GMR_CONTEST_CPT . '_posts_columns', 'gmr_contests_filter_contest_columns_list' );
add_filter( 'post_row_actions', 'gmr_contests_filter_contest_actions', PHP_INT_MAX, 2 );
add_filter( 'gmr_live_link_suggestion_post_types', 'gmr_contests_extend_live_link_suggestion_post_types' );
add_filter( 'pre_get_posts', 'gmr_filter_expired_contests' );

/**
 * Enqueues admin styles.
 *
 * @action admin_enqueue_scripts.
 * @global string $typenow The current post type.
 */
function gmr_contests_admin_enqueue_scripts() {
	global $typenow;

	$types = array(
		GMR_SURVEY_CPT,
		GMR_SURVEY_RESPONSE_CPT,
		GMR_CONTEST_CPT,
		GMR_CONTEST_ENTRY_CPT,
		GMR_SUBMISSIONS_CPT,
	);

	$page = filter_input( INPUT_GET, 'page' );
	$pages = array(
		'gmr-contest-winner',
		'gmr-survey-responses',
	);

	if ( in_array( $typenow, $types ) || in_array( $page, $pages ) ) {
		wp_enqueue_style( 'greatermedia-contests-admin', trailingslashit( GREATER_MEDIA_CONTESTS_URL ) . 'css/greatermedia-contests-admin.css', null, GREATER_MEDIA_CONTESTS_VERSION );
	}
}

/**
 * Registers contest post type in the curration types list.
 *
 * @filter gmr-homepage-curation-post-types
 * @filter gmr-show-curation-post-types
 * @param array $types Array of already registered types.
 * @return array Extended array of post types.
 */
function gmr_contest_register_curration_post_type( $types ) {
	$types[] = GMR_CONTEST_CPT;
	return $types;
}

/**
 * Removes UGC from editor links query.
 *
 * @filter wp_link_query_args
 * @param array $args The array of query args.
 * @return array Adjusted array of query args.
 */
function gmr_contests_exclude_ugc_from_editor_links_query( $args ) {
	if ( ! empty( $args['post_type'] ) ) {
		$post_type = $args['post_type'];
		if ( is_string( $post_type ) ) {
			$post_type = array_map( 'trim', explode( ',', $post_type ) );
		}

		unset( $post_type[ array_search( GMR_SUBMISSIONS_CPT, $post_type ) ] );
		$args['post_type'] = $post_type;
	}
	return $args;
}

/**
 * Removes delete_post(s) capabilities for public contests or contest entries.
 *
 * @filter map_meta_cap
 * @global string $pagenow The current page.
 * @global string $typenow The current type.
 * @param array $caps The array of user capabilities.
 * @param string $cap The current capability to check against.
 * @param int $user_id The current user id.
 * @param array $args Additional parameters.
 * @return array The array of allowed capabilities.
 */
function gmr_contests_map_meta_cap( $caps, $cap, $user_id, $args ) {
	global $pagenow, $typenow;

	if ( ! in_array( $typenow, array( GMR_CONTEST_CPT, GMR_CONTEST_ENTRY_CPT ) ) ) {
		return $caps;
	}

	if ( ! in_array( $pagenow, array( 'edit.php', 'post.php' ) ) ) {
		return $caps;
	}

	if ( in_array( $cap, array( 'delete_post', 'delete_posts' ) ) ) {
		if ( is_array( $args ) && ! empty( $args ) ) {
			// let's allow removal for non public contests
			$post = get_post( current( $args ) );
			if ( $post && GMR_CONTEST_CPT == $post->post_type ) {
				$status = get_post_status_object( $post->post_status );
				if ( $status && ! $status->public ) {
					return $caps;
				}
			}
		}

		$caps[] = 'do_not_allow';

		unset( $caps[ array_search( 'delete_post', $caps ) ] );
		unset( $caps[ array_search( 'delete_posts', $caps ) ] );
	}

	return $caps;
}

/**
 * Prevents started contest or contest entry deletion.
 *
 * @action before_delete_post
 * @param int $post The post id, which will be deleted.
 * @param string $post_status The actuall post status before removal.
 */
function gmr_contests_prevent_hard_delete( $post, $post_status = null ) {
	// do nothing if a post doesn't exist
	$post = get_post( $post );
	if ( ! $post ) {
		return;
	}

	// check contest
	if ( GMR_CONTEST_CPT == $post->post_type ) {
		if ( empty( $post_status ) ) {
			$post_status = $post->post_status;
		}

		$status = get_post_status_object( $post_status );
		if ( $status && $status->public ) {
			wp_die( 'You can not delete or trash already started contest.', '', array( 'back_link' => true ) );
		}
		return;
	}

	// check entry
	if ( GMR_CONTEST_ENTRY_CPT == $post->post_type ) {
		wp_die( 'You can not delete or trash contest entry.', '', array( 'back_link' => true ) );
	}
}

/**
 * Prevent started contests or contest entries transition to trash.
 *
 * @action transition_post_status 10 3
 * @param string $new_status The new status.
 * @param string $old_status The old status.
 * @param WP_Post $post The post object.
 */
function gmr_contests_prevent_trash_transition( $new_status, $old_status, $post ) {
	if ( 'trash' == $new_status ) {
		gmr_contests_prevent_hard_delete( $post, $old_status );
	}
}

/**
 * Removes "private" post status from query args for ajax request which returns
 * images for media popup.
 *
 * @filter ajax_query_attachments_args
 *
 * @param array $args The initial array of query arguments.
 * @return array Adjusted array of query arguments which doesn't contain "private" post status.
 */
function gmr_contests_adjuste_attachments_query( $args ) {
	if ( isset( $args['post_status'] ) ) {
		$post_status = is_array( $args['post_status'] )
			? $args['post_status']
			: explode( ',', $args['post_status'] );

		unset( $post_status[ array_search( 'private', $post_status ) ] );

		if ( empty( $post_status ) ) {
			$post_status = array( 'inherit' );
		}

		$args['post_status'] = $post_status;
	}

	return $args;
}

/**
 * Registers custom post types related to contests area.
 *
 * @action init
 */
function gmr_contests_register_post_type() {
	$labels = array(
		'name'               => 'Contests',
		'singular_name'      => 'Contest',
		'menu_name'          => 'Contests',
		'parent_item_colon'  => 'Parent Contest:',
		'all_items'          => 'All Contests',
		'view_item'          => 'View Contest',
		'add_new_item'       => 'Add New Contest',
		'add_new'            => 'Add New',
		'edit_item'          => 'Edit Contest',
		'update_item'        => 'Update Contest',
		'search_items'       => 'Search Contests',
		'not_found'          => 'Not found',
		'not_found_in_trash' => 'Not found in Trash'
	);

	$args   = array(
		'label'               => 'Contests',
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'taxonomies'          => array( 'category', 'post_tag' ),
		'public'              => true,
		'menu_position'       => 32,
		'menu_icon'           => 'dashicons-forms',
		'can_export'          => true,
		'has_archive'         => 'contests',
		'rewrite'             => array( 'slug' => 'contests', 'ep_mask' => EP_GMR_CONTEST ),
		'capability_type'     => array( 'contest', 'contests' ),
		'map_meta_cap'        => true,
	);

	register_post_type( GMR_CONTEST_CPT, $args );
	add_post_type_support( GMR_CONTEST_CPT, 'timed-content' );
}

/**
 * Registers rewrites and endpoints for contests related tasks.
 *
 * @action init 100
 * @global WP_Rewrite $wp_rewrite The rewrite API object.
 */
function gmr_contests_register_rewrites_and_endpoints() {
	global $wp_rewrite;

	$rewrite_rules = array(
		'^contest/type/([^/]*)/?$' => 'index.php?post_type=contest&contest_type=$matches[1]',
	);

	// add rewrite rules
	foreach ( $rewrite_rules as $rewrite_regex => $rewrite_target ) {
		$wp_rewrite->add_rule( $rewrite_regex, $rewrite_target, 'top' );
	}

	// add endpoints
	add_rewrite_endpoint( 'action', EP_GMR_CONTEST | EP_GMR_SURVEY );
	add_rewrite_endpoint( 'submission', EP_GMR_CONTEST | EP_GMR_SURVEY );

	// flush rewrite rules only if our rules is not registered
	$all_registered_rules = $wp_rewrite->wp_rewrite_rules();
	$registered_rules = array_intersect( $rewrite_rules, $all_registered_rules );
	if ( count( $registered_rules ) != count( $rewrite_rules ) ) {
		$wp_rewrite->flush_rules( true );
	}
}

/**
 * Registers contests related scripts.
 *
 * @action wp_enqueue_scripts 100
 */
function gmr_contests_enqueue_front_scripts() {
	// @NOTE: we have to always load frontend script, because we would have troubles when pjax is enabled
	$base_path = trailingslashit( GREATER_MEDIA_CONTESTS_URL );
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

	//wp_enqueue_style( 'greatermedia-contests', "{$base_path}css/greatermedia-contests.css", array( 'datetimepicker' ), GREATER_MEDIA_CONTESTS_VERSION );

	wp_enqueue_script( 'greatermedia-contests', "{$base_path}js/contests{$postfix}.js", array( 'modernizr', 'jquery-waypoints', 'jquery', 'datetimepicker', 'parsleyjs', 'gmr-gallery' ), GREATER_MEDIA_CONTESTS_VERSION, true );
	wp_rocketloader_script( 'greatermedia-contests' );
}

/**
 * Displays contest container attributes required for proper work of contest JS.
 *
 * @param WP_Post|int $post The contest id or object.
 */
function gmr_contest_container_attributes( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return;
	}

	$endpoints = array();

	if ( is_preview() ) {
		$endpoints = array(
			'load'        => add_query_arg( 'action', 'load' ),
			'confirm-age' => add_query_arg( 'action', 'confirm-age' ),
			'vote'        => add_query_arg( 'action', 'vote' ),
			'unvote'      => add_query_arg( 'action', 'unvote' ),
			'infinite'    => add_query_arg( 'page', '' ),
		);
	} else {
		$permalink = untrailingslashit( get_permalink( $post->ID ) );
		$permalink_action = "{$permalink}/action";

		$endpoints = array(
			'load'        => "{$permalink_action}/load/",
			'confirm-age' => "{$permalink_action}/confirm-age/",
			'vote'        => "{$permalink_action}/vote/",
			'unvote'      => "{$permalink_action}/unvote/",
			'infinite'    => "{$permalink}/page/",
		);
	}

	foreach ( $endpoints as $attribute => $value ) {
		echo sprintf( ' data-%s="%s"', $attribute, esc_url( $value ) );
	}
}

/**
 * Processes contest submission page request via AJAX.
 *
 * @action template_redirect
 */
function gmr_contests_process_submission_action() {
	// do nothing if it is a regular request
	if ( ! is_singular( GMR_SUBMISSIONS_CPT ) ) {
		return;
	}

	if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) {
		// disble HTTP cache
		nocache_headers();

		add_filter( 'gmr_gallery_use_hash', '__return_false' );

		the_post();
		get_template_part( 'partials/submission', 'preview' );
		exit;
	}
}

/**
 * Processes contest actions triggered from front end.
 *
 * @action template_redirect
 * @global int $submission_paged The submissions archive page number.
 */
function gmr_contests_process_action() {
	global $submission_paged;

	// do nothing if it is a regular request
	if ( ! is_singular( GMR_CONTEST_CPT ) ) {
		return;
	}

	$doing_ajax = ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest';
	if ( ! empty( $submission_paged ) && $doing_ajax ) {
		$query = gmr_contests_submissions_query( get_the_ID() );
		if ( ! $query->have_posts() ) {
			exit;
		}

		while ( $query->have_posts() ) :
			$query->the_post();
			get_template_part( 'partials/submission', 'tile' );
		endwhile;
		exit;
	}

	$action = get_query_var( 'action' );
	if ( ! empty( $action ) ) {
		// disable batcache if it is activated
		if ( function_exists( 'batcache_cancel' ) ) {
			batcache_cancel();
		}

		// disble HTTP cache
		nocache_headers();

		// do contest action
		do_action( "gmr_contest_{$action}" );
		exit;
	}
}

/**
 * Shows contest form after user confirmed his age.
 *
 * @action gmr_contest_confirm-age
 */
function gmr_contests_confirm_user_age() {
	gmr_contests_render_form( true );
}

/**
 * Returns a submission if for a voting action. Sends json error if a submission has not been found.
 *
 * @return WP_Post The submission object.
 */
function _gmr_contests_get_submission_for_voting_actions() {
	nocache_headers();

	// do nothing if a submission slug is empty
	$submission_slug = filter_input( INPUT_POST, 'ugc' );
	if ( empty( $submission_slug ) ) {
		wp_send_json_error();
	}

	$query = new WP_Query();
	$submissions = $query->query( array(
		'posts_per_page'      => 1,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'post_type'           => GMR_SUBMISSIONS_CPT,
		'fields'              => 'ids',
		'name'                => $submission_slug,
	) );

	// do nothing if a submission has not been found
	if ( empty( $submissions ) ) {
		wp_send_json_error();
	}

	$submission = get_post( current( $submissions ) );

	// Do nothing if voting is closed or the user isn't logged in.
	if (
		gmr_contests_is_voting_open( get_post()->post_parent ) &&
		(
			gmr_contests_allow_anonymous_votes( get_post()->post_parent ) ||
			( function_exists( 'is_gigya_user_logged_in' ) && is_gigya_user_logged_in() )
		)
	) {
		wp_send_json_error();
	}

	return $submission;
}

/**
 * Returns voting key.
 *
 * @param  int $contest_id ID of the contest being voted for.
 *
 * @return string The voting key.
 */
function _gmr_contests_get_vote_key( $contest_id ) {
	$key = false;
	// Get the gigya user id if they're logged in.
	if ( function_exists( 'get_gigya_user_id' ) ) {
		$key = get_gigya_user_id();
	}

	// If we don't have a key and anonymous voting is allowed get a
	// unique id.
	if ( empty( $key ) && gmr_contests_allow_anonymous_votes( $contest_id ) ) {
		// Check a cookie first.
		$key = isset( $_COOKIE['_gmrvk'] ) ? $_COOKIE['_gmrvk'] : false;

		// If we don't have a key generate new one.
		if ( empty( $key ) ) {
			$key = uniqid();
		}

		// Save for future sessions.
		setcookie( '_gmrvk', $key, time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
	}

	return 'vote_' . $key;
}

/**
 * Determines whether an user voted for a submission or not.
 *
 * @param int|WP_Post $submission The submission id or object to check against.
 * @return boolean TRUE if current gigya user voted for a submission, otherwise FALSE.
 */
function gmr_contests_is_user_voted_for_submission( $submission = null ) {
	if ( ! gmr_contests_allow_anonymous_votes( get_post()->post_parent ) && ! ( function_exists( 'is_gigya_user_logged_in' ) && is_gigya_user_logged_in() ) ) {
		return false;
	}

	if ( is_null( $submission ) || is_int( $submission ) ) {
		$submission = get_post( get_the_ID() );
	}

	$vote_key = _gmr_contests_get_vote_key( $submission->post_parent );
	if ( empty( $vote_key ) ) {
		return false;
	}

	$voted = get_post_meta( $submission->ID, $vote_key, true );

	return ! empty( $voted );
}

/**
 * Records user vote action for a submission.
 *
 * @action gmr_contest_vote
 */
function gmr_contests_vote_for_submission() {
	// grab submission object
	$submission = _gmr_contests_get_submission_for_voting_actions();

	// do nothing if an user has already voted for this submission
	$vote_key = _gmr_contests_get_vote_key( $submission->post_parent );
	$voted = get_post_meta( $submission->ID, $vote_key, true );
	if ( ! empty( $voted ) ) {
		wp_send_json_error();
	}

	// increment votes count and record current vote
	add_post_meta( $submission->ID, $vote_key, current_time( 'timestamp', 1 ) );
	$submission->menu_order += 1;
	wp_update_post( $submission->to_array() );

	wp_send_json_success();
}

/**
 * Records user unvote action for a submission.
 *
 * @action gmr_contest_unvote
 */
function gmr_contests_unvote_for_submission() {
	// grab submission object
	$submission = _gmr_contests_get_submission_for_voting_actions();

	// do nothing if an user has not voted for this submission yet
	$vote_key = _gmr_contests_get_vote_key( $submission->post_parent );
	$voted = get_post_meta( $submission->ID, $vote_key, true );
	if ( empty( $voted ) ) {
		wp_send_json_error();
	}

	// decrement votes count and delete current vote
	delete_post_meta( $submission->ID, $vote_key );
	$submission->menu_order -= 1;
	if ( $submission->menu_order < 0 ) {
		$submission->menu_order = 0;
	}
	wp_update_post( $submission->to_array() );

	wp_send_json_success();
}

/**
 * Determines whether contest is started or not.
 *
 * @param int|WP_Post $contest_id The contest object or id.
 * @return boolean TRUE if contest has not been started yet, otherwise FALSE.
 */
function gmr_contest_is_not_started( $contest_id = null ) {
	if ( ! $contest_id ) {
		$contest_id = get_the_ID();
	}

	$now = current_time( 'timestamp', 1 );
	$start = (int) get_post_meta( $contest_id, 'contest-start', true );

	return $start > 0 && $start > $now;
}

/**
 * Determines whether contest is finished or not.
 *
 * @param int|WP_Post $contest_id The contest object or id.
 * @return boolean TRUE if contest is finished, otherwise FALSE.
 */
function gmr_contest_is_finished( $contest_id = null ) {
	if ( ! $contest_id ) {
		$contest_id = get_the_ID();
	}

	$now = current_time( 'timestamp', 1 );
	$end = (int) get_post_meta( $contest_id, 'contest-end', true );

	return $end > 0 && $now > $end;
}

/**
 * Determines whether contest reached the maximum entries or not.
 *
 * @param int|WP_Post $contest_id The contest object or id.
 * @return boolean TRUE if contest reached the maximum entries, otherwise FALSE.
 */
function gmr_contest_has_max_entries( $contest_id = null ) {
	if ( ! $contest_id ) {
		$contest_id = get_the_ID();
	}

	$max_entries = get_post_meta( $contest_id, 'contest-max-entries', true );
	$current_entries = gmr_contests_get_entries_count( $contest_id );

	return $max_entries > 0 && $current_entries >= $max_entries;
}

/**
 * Determines whether contest requires only signed in users or not.
 *
 * @param int|WP_Post $contest_id The contest object or id.
 * @return boolean TRUE if contest requires signed in users, otherwise FALSE.
 */
function gmr_contest_allows_members_only( $contest_id = null ) {
	if ( ! $contest_id ) {
		$contest_id = get_the_ID();
	}

	return filter_var( get_post_meta( $contest_id, 'contest-members-only', true ), FILTER_VALIDATE_BOOLEAN );
}

/**
 * Renders contest form.
 *
 * @action gmr_contest_load
 * @param boolean $skip_age Determines whether to check user age or not.
 */
function gmr_contests_render_form( $skip_age = false ) {
	$contest_id = get_the_ID();

	// check start date
	if ( gmr_contest_is_not_started( $contest_id ) ) {
		wp_send_json_error( array( 'restriction' => 'not-started' ) );
	}

	// check end date
	if ( gmr_contest_is_finished( $contest_id ) ) {
		wp_send_json_error( array( 'restriction' => 'finished' ) );
	}

	// check the max entries limit
	if ( gmr_contest_has_max_entries( $contest_id ) ) {
		wp_send_json_error( array( 'restriction' => 'max-entries' ) );
	}

	// check if user has to be logged in
	$gigya_logged_in_exists = function_exists( 'is_gigya_user_logged_in' );
	if ( gmr_contest_allows_members_only( $contest_id ) && $gigya_logged_in_exists && ! is_gigya_user_logged_in() ) {
		wp_send_json_error( array( 'restriction' => 'signin' ) );
	}

	// check if user can submit multiple entries
	$single_entry = get_post_meta( $contest_id, 'contest-single-entry', true );
	if ( $single_entry ) {
		$contests = isset( $_COOKIE['__cs'] ) ? $_COOKIE['__cs'] : '';
		$contests = wp_parse_id_list( base64_decode( $contests ) );
		if ( in_array( $contest_id, $contests ) ) {
			wp_send_json_error( array( 'restriction' => 'one-entry' ) );
		}

		if ( function_exists( 'has_user_entered_contest' ) && has_user_entered_contest( $contest_id ) ) {
			wp_send_json_error( array( 'restriction' => 'one-entry' ) );
		}
	}

	// check min age restriction
	if ( ! $skip_age ) {
		$min_age = (int) get_post_meta( $contest_id, 'contest-min-age', true );
		if ( $min_age > 0 ) {
			if ( $gigya_logged_in_exists && is_gigya_user_logged_in() ) {
				$current_age = get_gigya_user_field( 'age' );
				if ( $current_age < $min_age ) {
					wp_send_json_error( array( 'restriction' => 'age-fails' ) );
				}
			} else {
				wp_send_json_error( array( 'restriction' => 'age' ) );
			}
		}
	}

	// render the form
	wp_send_json_success( array(
		'contest_id' => $contest_id,
		'html'       => GreaterMediaFormbuilderRender::render( $contest_id ),
	) );
}

/**
 * Returns login URL for a contest page.
 *
 * @param string $redirect The redirect URL.
 * @return string The login page URL.
 */
function gmr_contests_get_login_url( $redirect = null ) {
	if ( is_null( $redirect ) ) {
		$redirect = parse_url( get_permalink(), PHP_URL_PATH );
	}

	return function_exists( 'gigya_profile_path' )
		? gigya_profile_path( 'login', array( 'dest' => $redirect ) )
		: '#';
}

/**
 * Verifies form submission.
 */
function gmr_contests_verify_form_submission( $form ) {
	_deprecated_function( 'gmr_contests_verify_form_submission', '1.1.3', 'gmr_verify_form_submission' );
	gmr_verify_form_submission( $form );
}

/**
 * Processes contest submission.
 *
 * @action gmr_contest_submit
 */
function gmr_contests_process_form_submission() {
	if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
		return;
	}

	$contest_id = get_the_ID();
	$submitted_values = $submitted_files  = array();

	if ( function_exists( 'is_gigya_user_logged_in' ) && ! is_gigya_user_logged_in() ) {
		$entrant_email = filter_input( INPUT_POST, 'userinfo_email', FILTER_VALIDATE_EMAIL );
		if ( ! $entrant_email || ( function_exists( 'has_email_entered_contest' ) && has_email_entered_contest( $contest_id, $entrant_email ) ) ) {
			echo '<html>';
				echo '<head></head>';
				echo '<body><b style="color:red">Sorry, but you can enter the contest only once.</b></body>';
			echo '</html>';
			exit;
		}
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	
	$form = @json_decode( get_post_meta( $contest_id, 'embedded_form', true ) );
	gmr_verify_form_submission( $form );

	foreach ( $form as $field ) {
		$field_key = 'form_field_' . $field->cid;
		if ( 'file' === $field->field_type ) {

			if ( isset( $_FILES[ $field_key ] ) && file_is_valid_image( $_FILES[ $field_key ]['tmp_name'] ) ) {
				$file_id = media_handle_upload( $field_key, $contest_id, array( 'post_status' => 'private' ) );
				$submitted_files[ $field->cid ] = $submitted_values[ $field->cid ] = $file_id;
			}

		} else if ( isset( $_POST[ $field_key ] ) ) {

			if ( is_scalar( $_POST[ $field_key ] ) ) {

				$value = $_POST[ $field_key ];
				if ( 'radio' == $field->field_type && 'other' == $value ) {
					if ( empty( $_POST[ "{$field_key}_other_value" ] ) ) {
						continue;
					}

					$value = $_POST[ "{$field_key}_other_value" ];
				}

				$submitted_values[ $field->cid ] = sanitize_text_field( $value );

			} else if ( is_array( $_POST[ $field_key ] ) ) {

				$array_data = array();
				foreach ( $_POST[ $field_key ] as $value ) {
					if ( 'checkboxes' == $field->field_type && 'other' == $value ) {
						if ( empty( $_POST[ "{$field_key}_other_value" ] ) ) {
							continue;
						}

						$value = $_POST[ "{$field_key}_other_value" ];
					}

					$array_data[] = sanitize_text_field( $value );
				}

				$submitted_values[ $field->cid ] = $array_data;

			}
		}
	}

	$entry = ContestEntryEmbeddedForm::create_for_data( $contest_id, json_encode( $submitted_values ) );
	$entry->save();

	gmr_contests_handle_submitted_files( $submitted_files, $entry );

	do_action( 'greatermedia_contest_entry_save', $entry );
	delete_transient( 'contest_entries_' . $contest_id );

	if ( ! headers_sent() ) {
		$contests = isset( $_COOKIE['__cs'] ) ? $_COOKIE['__cs'] : '';
		$contests = wp_parse_id_list( base64_decode( $contests ) );
		$contests[] = $contest_id;
		$contests = array_filter( array_unique( $contests ) );
		$contests = base64_encode( implode( ',', $contests ) );

		setcookie( '__cs', $contests, current_time( 'timestamp', 1 ) + YEAR_IN_SECONDS, '/', parse_url( home_url(), PHP_URL_HOST ) );
	}

	echo '<html>';
		echo '<head></head>';
		echo '<body>';
			echo wpautop( get_post_meta( $contest_id, 'form-thankyou', true ) );

			$fields = GreaterMediaFormbuilderRender::parse_entry( $contest_id, $entry->post_id(), null, true );
			if ( ! empty( $fields ) ) :
				?><h4 class="contest__submission--entries-title">Here is your submission:</h4>
				<dl class="contest__submission--entries">
					<?php foreach ( $fields as $field ) : ?>
						<?php if ( 'file' != $field['type'] ) : ?>
							<dt>
								<?php echo esc_html( $field['label'] ); ?>
							</dt>
							<dd>
								<?php echo esc_html( is_array( $field['value'] ) ? implode( ', ', $field['value'] ) : $field['value'] ); ?>
							</dd>
						<?php endif; ?>
					<?php endforeach; ?>
				</dl><?php
			endif;
		echo '</body>';
	echo '</html>';
}

/**
 * Saves contest submitted files.
 *
 * @param array $submitted_files
 * @param GreaterMediaContestEntry $entry
 */
function gmr_contests_handle_submitted_files( array $submitted_files, GreaterMediaContestEntry $entry ) {
	if ( empty( $submitted_files ) ) {
		return;
	}

	$thumbnail = null;
	$data_type = count( $submitted_files ) == 1 ? 'image' : 'gallery';

	$ugc = GreaterMediaUserGeneratedContent::for_data_type( $data_type );
	$ugc->post->post_parent = $entry->post->post_parent;

	reset( $submitted_files );
	$thumbnail = current( $submitted_files );

	switch ( $data_type ) {
		case 'image':
			$ugc->post->post_content = wp_get_attachment_image( current( $submitted_files ), 'full' );
			break;
		case 'gallery':
			$ugc->post->post_content = '[gallery ids="' . implode( ',', $submitted_files ) . '"]';
			break;
	}

	$ugc->save();

	set_post_thumbnail( $ugc->post->ID, $thumbnail );

	add_post_meta( $ugc->post->ID, 'contest_entry_id', $entry->post->ID );
	if ( function_exists( 'get_gigya_user_id' ) ) {
		add_post_meta( $ugc->post->ID, 'gigya_user_id', get_gigya_user_id() );
	}

	update_post_meta( $entry->post->ID, 'submission_id', $ugc->post->ID );
}

/**
 * Returns submission associated with contest entry.
 *
 * @param WP_Post|int $entry The contest entry object or id.
 * @return WP_Post The submission object on success, otherwise NULL.
 */
function get_contest_entry_submission( $entry = null ) {
	$entry = get_post( $entry );
	if ( $entry && GMR_CONTEST_ENTRY_CPT == $entry->post_type ) {
		$submission_id = get_post_meta( $entry->ID, 'submission_id', true );
		if ( $submission_id && ( $submission = get_post( $submission_id ) ) && GMR_SUBMISSIONS_CPT == $submission->post_type ) {
			return $submission;
		}
	}

	return null;
}

/**
 * Returns the amount of contest entries.
 *
 * @param int $contest_id The contest id.
 * @return int The contest entries amount.
 */
function gmr_contests_get_entries_count( $contest_id ) {
	$transient = 'contest_entries_' . $contest_id;
	$contest_entries_count = get_transient( $transient );
	if ( false === $contest_entries_count ) {
		$query = new WP_Query( array(
			'post_type'      => GMR_CONTEST_ENTRY_CPT,
			'post_status'    => 'any',
			'post_parent'    => $contest_id,
			'posts_per_page' => 1,
			'fields'         => 'ids',
		) );

		$contest_entries_count = $query->found_posts;
		set_transient( $transient, $contest_entries_count, DAY_IN_SECONDS );
	}

	return $contest_entries_count;
}

/**
 * Returns contest entries query.
 *
 * @filter gmr_contest_submissions_query
 * @global int $submission_paged The submissions archive page number.
 * @param int|WP_Query The contest id or submissions query.
 * @return WP_Query The entries query.
 */
function gmr_contests_submissions_query( $contest_id = null ) {
	global $submission_paged;

	if ( is_a( $contest_id, 'WP_Query' ) ) {
		return $contest_id;
	}

	if ( is_null( $contest_id ) ) {
		$contest_id = get_the_ID();
	}

	return new WP_Query( array(
		'post_type'      => GMR_SUBMISSIONS_CPT,
		'post_parent'    => $contest_id,
		'posts_per_page' => 500,
		'paged'          => $submission_paged,
	) );
}

/**
 * Builds permalink for contest submission object.
 *
 * @filter post_type_link 10 2
 * @param string $post_link The initial permalink
 * @param WP_Post $post The post object.
 * @return string The submission permalink.
 */
function gmr_contests_get_submission_permalink( $post_link, $post ) {
	if ( GMR_SUBMISSIONS_CPT == $post->post_type && ! empty( $post->post_parent ) ) {
		$contest_link = get_permalink( $post->post_parent );
		if ( $contest_link ) {
			return trailingslashit( $contest_link ) . 'submission/' . $post->post_name . '/';
		}
	}

	return $post_link;
}

/**
 * Unpacks query vars for contest submission page.
 *
 * @filter request
 * @global int $submission_paged The submissions archive page number.
 * @param array $query_vars The array of initial query vars.
 * @return array The array of unpacked query vars.
 */
function gmr_contests_unpack_vars( $query_vars ) {
	global $submission_paged;

	if ( empty( $query_vars[ GMR_CONTEST_CPT ] ) ) {
		return $query_vars;
	}

	if ( ! empty( $query_vars['paged'] ) ) {
		$submission_paged = $query_vars['paged'];
		unset( $query_vars['paged'] );
	}

	if ( ! empty( $query_vars['submission'] ) ) {
		$query = new WP_Query( array(
			'post_type'           => GMR_CONTEST_CPT,
			'name'                => $query_vars[ GMR_CONTEST_CPT ],
			'fields'              => 'ids',
			'posts_per_page'      => 1,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		) );

		if ( ! $query->have_posts() ) {
			return $query_vars;
		}

		return array(
			GMR_SUBMISSIONS_CPT => $query_vars['submission'],
			'post_type'         => GMR_SUBMISSIONS_CPT,
			'name'              => $query_vars['submission'],
			'post_parent'       => $query->next_post(),
		);
	}

	return $query_vars;
}

/**
 * Substitutes original thumbnail on a special thumbnail for contest submissions.
 *
 * @filter post_thumbnail_html 10 4
 * @param string $html Original thumbnail html.
 * @param int $post_id The contest submission id.
 * @param int $post_thumbnail_id The thumbnail id.
 * @param string $size The size of thumbnail.
 * @return string The html of a thumbnail.
 */
function gmr_contests_post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size ) {
	$post = get_post( $post_id );
	if ( GMR_SUBMISSIONS_CPT != $post->post_type ) {
		return $html;
	}

	$image = wp_get_attachment_image_src( $post_thumbnail_id, $size );
	if ( empty( $image ) ) {
		return $html;
	}

	return sprintf( '<div class="contest__submission--thumbnail" style="background-image:url(%s)"></div>', $image[0] );
}

/**
 * Return contest submission author.
 *
 * @param int|WP_Post $submission The contest submission id or object.
 * @return string The author name if available.
 */
function gmr_contest_submission_get_author( $submission = null ) {
	$submission = get_post( $submission );
	if ( $submission ) {
		$display_name = gmr_contest_get_fields( $submission->ID, 'display_name' );
		if ( ! empty ( $display_name = $display_name[0] ) ) {
			return $display_name['value'];
		} else {
			$entry = get_post_meta( $submission->ID, 'contest_entry_id', true );
			if ( $entry ) {
				return gmr_contest_get_entry_author( $entry );
			}
		}
	}

	return 'guest';
}

/**
 * Return contest entry author name.
 *
 * @param int $entry_id The contest entry id.
 * @return string The author name.
 */
function gmr_contest_get_entry_author( $entry_id, $return = 'string' ) {
	if ( function_exists( 'get_gigya_user_profile' ) ) {
		try {
			$gigya_id = get_post_meta( $entry_id, 'entrant_reference', true );
			if ( $gigya_id && ( $profile = get_gigya_user_profile( $gigya_id ) ) ) {
				if ( 'string' == $return ) {
					return trim( sprintf(
						'%s %s',
						isset( $profile['firstName'] ) ? $profile['firstName'] : '',
						isset( $profile['lastName'] ) ? $profile['lastName'] : ''
					) );
				} else {
					return array(
						isset( $profile['firstName'] ) ? $profile['firstName'] : '',
						isset( $profile['lastName'] ) ? $profile['lastName'] : ''
					);
				}
			}
		} catch( Exception $e ) {}
	}

	$username = trim( get_post_meta( $entry_id, 'entrant_name', true ) );
	if ( ! $username ) {
		$username = 'guest';
	}

	if ( 'string' == $return ) {
		return $username;
	}

	$username = explode( ' ', $username );
	$last_name = array_pop( $username );
	$first_name = implode( ' ', $username );

	return array( $first_name, $last_name );
}

/**
 * Returns gigya user email.
 *
 * @param int|WP_Post $entry_id Contest post object or id.
 * @return string The email address.
 */
function gmr_contest_get_entry_author_email( $entry_id ) {
	$email = get_post_meta( $entry_id, 'entrant_email', true );
	if ( function_exists( 'get_gigya_user_profile' ) ) {
		try {
			$gigya_id = get_post_meta( $entry_id, 'entrant_reference', true );
			if ( $gigya_id && ( $profile = get_gigya_user_profile( $gigya_id ) ) && isset( $profile['email'] ) ) {
				$email = $profile['email'];
			}
		} catch( Exception $e ) {}
	}

	return filter_var( $email, FILTER_VALIDATE_EMAIL );
}

/**
 * Returns classes string for a submission.
 *
 * @param string|array $class The default class for a submission block.
 * @return string The submission classes.
 */
function gmr_contests_submission_class( $class ) {
	$classes = array();
	$post = get_post();

	if ( gmr_contests_is_user_voted_for_submission( $post ) ) {
		$classes[] = 'voted';
	}

	if ( gmr_contests_is_submission_winner( $post ) ) {
		$classes[] = 'winner';
	}

	if ( ! empty( $class ) ) {
		$classes = array_merge( $classes, ! is_array( $class ) ? $class = preg_split( '#\s+#', $class ) : $class );
	}

	return implode( ' ', array_unique( $classes ) );
}

/**
 * Determines whether submission has been selected as a winner.
 *
 * @param WP_Post|int $submission The submission object or id.
 * @return boolean TRUE if submission has been selected as a winner, otherwise FALSE.
 */
function gmr_contests_is_submission_winner( $submission = null ) {
	$submission = get_post( $submission );
	if ( ! $submission || GMR_SUBMISSIONS_CPT != $submission->post_type ) {
		return false;
	}

	$entry_id = get_post_meta( $submission->ID, 'contest_entry_id', true );
	if ( ! $entry_id ) {
		return false;
	}

	$gigya_id = trim( get_post_meta( $entry_id, 'entrant_reference', true ) );
	if ( empty( $gigya_id ) ) {
		return false;
	}

	return in_array( "{$entry_id}:{$gigya_id}", get_post_meta( $submission->post_parent, 'winner' ) );
}

/**
 * Returns contest type label of a certain contest.
 *
 * @param int|WP_Post $contest The contest post object or id.
 * @return string The contest type label.
 */
function gmr_contest_get_type_label( $contest = null ) {
	$contest = get_post( $contest );

	switch ( get_post_meta( get_the_ID(), 'contest_type', true ) ) {
		case 'onair':
			return 'On Air';
		case 'both':
			return 'On Air & Online';
		case 'online':
		default:
			return 'Online';
	}

	return '';
}

/**
 * Adds columns to the contests table.
 *
 * @filter manage_contest_posts_columns
 * @param array $columns Initial array of columns.
 * @return array The array of columns.
 */
function gmr_contests_filter_contest_columns_list( $columns ) {
	// put just after the title column
	$cut_mark = array_search( 'title', array_keys( $columns ) ) + 1;

	$columns = array_merge(
		array_slice( $columns, 0, $cut_mark ),
		array(
			'start_date'  => 'Start Date',
			'finish_date' => 'Finish Date',
		),
		array_slice( $columns, $cut_mark )
	);

	$columns['date'] = 'Created Date';

	return $columns;
}

/**
 * Renders custom columns for the contests table.
 *
 * @param string $column_name The column name which is gonna be rendered.
 * @param int $post_id The post id.
 */
function gmr_contests_render_contest_column( $column_name, $post_id ) {
	if ( 'start_date' == $column_name ) {
		$timestamp = (int) get_post_meta( $post_id, 'contest-start', true );
		echo ! empty( $timestamp ) ? date( get_option( 'date_format' ), $timestamp ) : '&#8212;';
	} elseif ( 'finish_date' == $column_name ) {
		$timestamp = (int) get_post_meta( $post_id, 'contest-end', true );
		echo ! empty( $timestamp ) ? date( get_option( 'date_format' ), $timestamp ) : '&#8212;';
	}
}

/**
 * Filters contest actions at the contests table.
 *
 * @filter post_row_actions PHP_INT_MAX 2
 * @param array $actions The initial array of actions.
 * @param WP_Post $post The actual contest object.
 * @return array Filtered array of actions.
 */
function gmr_contests_filter_contest_actions( $actions, WP_Post $post ) {
	// do nothing if it is not a contest post
	if ( GMR_CONTEST_CPT != $post->post_type ) {
		return $actions;
	}

	// unset redundant actions
	unset( $actions['inline hide-if-no-js'], $actions['edit_as_new_draft'], $actions['clone'] );

	// move trash/delete link to the end of actions list if it exists
	foreach ( array( 'trash', 'delete' ) as $key ) {
		if ( isset( $actions[ $key ] ) ) {
			$link = $actions[ $key ];
			unset( $actions[ $key ] );
			$actions[ $key ] = $link;
		}
	}

	if ( ! current_user_can( 'edit_contest_entries' ) ) {
		unset( $actions['gmr-contest-winner'] );
	}

	return $actions;
}

/**
 * Determines whether a contest has file fields or not.
 *
 * @param WP_Post|int $contest The contest object or id.
 * @return boolean TRUE if a contest has file fields, otherwise FALSE.
 */
function gmr_contest_has_files( $contest ) {
	$contest = get_post( $contest );
	if ( ! $contest || GMR_CONTEST_CPT != $contest->post_type ) {
		return false;
	}

	$form = get_post_meta( $contest->ID, 'embedded_form', true );
	if ( empty( $form ) ) {
		return array();
	}

	if ( is_string( $form ) ) {
		$clean_form = trim( $form, '"' );
		$form = json_decode( $clean_form );
	}

	foreach ( $form as $field ) {
		if ( 'file' == $field->field_type ) {
			return true;
		}
	}

	return false;
}

/**
 * Extends live link suggestion post types.
 *
 * @param array $post_types The array of already registered post types.
 * @return array The array of extended post types.
 */
function gmr_contests_extend_live_link_suggestion_post_types( $post_types ) {
	$post_types[] = GMR_CONTEST_CPT;
	return $post_types;
}

/**
 * Filters out expired contests
 */
global $did_filter_expired_contests;
$did_filter_expired_contests = false;

function gmr_filter_expired_contests( $query ) {
	global $did_filter_expired_contests;

	if ( ! is_admin() && ( is_search() || is_post_type_archive( GMR_CONTEST_CPT ) ) && ! $did_filter_expired_contests ) {
		$now           = time();
		$query_params = array(
			'relation' => 'OR',
			/* This is a contest with an valid end timestamp */
			array(
				'key'     => 'contest-end',
				'type'    => 'NUMERIC',
				'value'   => $now,
				'compare' => '>',
			),
			/* any other post/type which matches the search query */
			array(
				'key'     => 'contest-end',
				'type'    => 'NUMERIC',
				'value'   => '',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => 'contest-end',
				'type'    => 'NUMERIC',
				'value'   => 0,
			),
		);

		$query->set( 'meta_query', $query_params );
		$did_filter_expired_contests = true;

		return $query;
	} else {
		return $query;
	}
}

/**
 * Check whether or not voting for the contest is open.
 *
 * @param int $contest_id ID of contest to check.
 *
 * @return bool
 */
function gmr_contests_is_voting_open( $contest_id ) {
	$vote_start   = gmr_contests_get_vote_start_date( $contest_id );
	$vote_end     = gmr_contests_get_vote_end_date( $contest_id );
	$current_time = time();

	return ( $vote_start <= $current_time && $current_time < $vote_end );
}

/**
 * Get contest's vote start date.
 *
 * @param $contest_id
 *
 * @return int
 */
function gmr_contests_get_vote_start_date( $contest_id ) {
	return (int) get_post_meta( $contest_id, 'contest-vote-start', true ) ?:
		get_post_meta( $contest_id, 'contest-start', true );;
}

/**
 * Get contest's vote end date.
 *
 * @param $contest_id
 *
 * @return int
 */
function gmr_contests_get_vote_end_date( $contest_id ) {
	return (int) get_post_meta( $contest_id, 'contest-vote-end', true ) ?:
		get_post_meta( $contest_id, 'contest-end', true );
}

/**
 * Determines if we can show vote counts or not.
 *
 * @param  int|WP_Post $submission The post ID or object.
 * @return boolean true/false if the vote counts should be displayed.
 */
function gmr_contests_can_show_vote_count( $submission = null ) {
	if ( is_null( $submission ) || is_int( $submission ) ) {
		$submission = get_post( get_the_ID() );
	}

	if ( $submission->post_parent ) {
		return get_post_meta( $submission->post_parent, 'contest_show_vote_counts', true ) ? true : false;
	}
}

/**
 * Returns true if anonymous voting is allowed for a contest, and false if it's not.
 * 
 * @param int $contest_id
 *
 * @return bool
 */
function gmr_contests_allow_anonymous_votes( $contest_id = 0 ) {
	return (bool) get_post_meta( $contest_id, 'contest-allow-anonymous-voting', true );
}

/*
 * Return the custom fields associated with an entry; field => value.
 */
function gmr_contest_get_fields( $submission = null, $field_type = 'entry_field' ) {
	$contest_fields = array();
	if ( is_null( $submission ) ) {
		$submission = get_the_ID();
	}

	$entry_id = get_post_meta( $submission, 'contest_entry_id', true );

	$entry = get_post( $entry_id );
	
	$entry_reference = get_post_meta( $entry->ID, 'entry_reference', true );

	$fields = GreaterMediaFormbuilderRender::parse_entry( $entry->post_parent, $entry->ID, null, true );

	foreach ( $fields as $field ) {
		if ( false === $field[ $field_type ] || ( 'file' === $field['type'] || 'email' === $field['type'] ) ) {
			continue;
		}

		$contest_fields[] = $field;
	}

	return $contest_fields;
}
