<?php

// action hooks
add_action( 'init', 'gmr_contests_register_post_type' );
add_action( 'init', 'gmr_contests_register_rewrites_and_endpoints' );
add_action( 'wp_enqueue_scripts', 'gmr_contests_enqueue_front_scripts', 100 );
add_action( 'template_redirect', 'gmr_contests_process_action' );
add_action( 'template_redirect', 'gmr_contests_process_submission_action' );
add_action( 'manage_' . GMR_CONTEST_CPT . '_posts_custom_column', 'gmr_contests_render_contest_column', 10, 2 );

add_action( 'gmr_contest_load', 'gmr_contests_render_form' );
add_action( 'gmr_contest_submit', 'gmr_contests_process_form_submission' );
add_action( 'gmr_contest_confirm-age', 'gmr_contests_confirm_user_age' );
add_action( 'gmr_contest_reject-age', 'gmr_contests_reject_user_age' );
add_action( 'gmr_contest_vote', 'gmr_contests_vote_for_submission' );
add_action( 'gmr_contest_unvote', 'gmr_contests_unvote_for_submission' );

// filter hooks
add_filter( 'gmr_contest_submissions_query', 'gmr_contests_submissions_query' );
add_filter( 'post_type_link', 'gmr_contests_get_submission_permalink', 10, 2 );
add_filter( 'request', 'gmr_contests_unpack_vars' );
add_filter( 'post_thumbnail_html', 'gmr_contests_post_thumbnail_html', 10, 4 );
add_filter( 'post_row_actions', 'gmr_contests_add_table_row_actions', 10, 2 );
add_filter( 'manage_' . GMR_CONTEST_CPT . '_posts_columns', 'gmr_contests_filter_contest_columns_list' );

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
		'taxonomies'          => array( 'contest_type', 'category', 'post_tag' ),
		'public'              => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-forms',
		'can_export'          => true,
		'has_archive'         => 'contests',
		'rewrite'             => array( 'slug' => 'contest', 'ep_mask' => EP_GMR_CONTEST ),
	);

	register_post_type( GMR_CONTEST_CPT, $args );
	add_post_type_support( GMR_CONTEST_CPT, 'timed-content' );
}

/**
 * Registers rewrites and endpoints for contests related tasks.
 *
 * @action init
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
	add_rewrite_endpoint( 'action', EP_GMR_CONTEST );
	add_rewrite_endpoint( 'submission', EP_GMR_CONTEST );

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

	wp_enqueue_style( 'greatermedia-contests', "{$base_path}css/greatermedia-contests.css", array( 'datetimepicker', 'parsleyjs' ), GREATER_MEDIA_CONTESTS_VERSION );

	wp_enqueue_script( 'greatermedia-contests', "{$base_path}js/contests{$postfix}.js", array( 'jquery', 'datetimepicker', 'parsleyjs', 'parsleyjs-words', 'gmr-gallery' ), GREATER_MEDIA_CONTESTS_VERSION, true );
	wp_localize_script( 'greatermedia-contests', 'GreaterMediaContests', array(
		'selectors' => array(
			'container' => '#contest-form',
			'form'      => '.' . GreaterMediaFormbuilderRender::FORM_CLASS,
			'yes_age'   => '.min-age-yes',
			'no_age'    => '.min-age-no',
			'grid'      => '.contest__submissions--list',
			'grid_more' => '.contest__submissions--load-more',
		),
	) );
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

	$permalink = untrailingslashit( get_permalink( $post->ID ) );
	$permalink_action = "{$permalink}/action";

	$endpoints = array(
		'load'        => "{$permalink_action}/load/",
		'submit'      => "{$permalink_action}/submit/",
		'confirm-age' => "{$permalink_action}/confirm-age/",
		'reject-age'  => "{$permalink_action}/reject-age/",
		'vote'        => "{$permalink_action}/vote/",
		'unvote'      => "{$permalink_action}/unvote/",
		'infinite'    => "{$permalink}/page/",
	);

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
 * Rejects contest applying to an user which doesn't meet age requirements.
 *
 * @action gmr_contest_reject-age
 */
function gmr_contests_reject_user_age() {
	$min_age = (int) get_post_meta( get_the_ID(), 'contest-min-age', true );
	echo '<p>Sorry, you must be at least ', $min_age, ' years old to enter the contest!</p>';
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

	// do nothing if an user is not logged in
	if ( ! function_exists( 'is_gigya_user_logged_in' ) || ! is_gigya_user_logged_in() ) {
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

	return get_post( current( $submissions ) );
}

/**
 * Returns voting key.
 *
 * @return string The voting key.
 */
function _gmr_contests_get_vote_key() {
	return function_exists( 'get_gigya_user_id' ) 
		? 'vote_' . get_gigya_user_id()
		: false;
}

/**
 * Determines whether an user voted for a submission or not.
 *
 * @param int|WP_Post $submission The submission id or object to check against.
 * @return boolean TRUE if current gigya user voted for a submission, otherwise FALSE.
 */
function gmr_contests_is_user_voted_for_submission( $submission = null ) {
	if ( ! function_exists( 'is_gigya_user_logged_in' ) || ! is_gigya_user_logged_in() ) {
		return false;
	}

	$vote_key = _gmr_contests_get_vote_key();
	if ( empty( $vote_key ) ) {
		return false;
	}
	
	$submission = get_post( $submission );
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
	$vote_key = _gmr_contests_get_vote_key();
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
	$vote_key = _gmr_contests_get_vote_key();
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
 * Renders contest form.
 *
 * @action gmr_contest_load
 * @param boolean $skip_age Determines whether to check user age or not.
 */
function gmr_contests_render_form( $skip_age = false ) {
	$contest_id = get_the_ID();

	// check start date
	$now = current_time( 'timestamp', 1 );
	$start = (int) get_post_meta( $contest_id, 'contest-start', true );
	if ( $start > 0 && $start > $now ) {
		echo '<p>The contest is not started yet.</p>';
		return;
	}

	// check end date
	$end = (int) get_post_meta( $contest_id, 'contest-end', true );
	if ( $end > 0 && $now > $end ) {
		echo '<p>The contest is already finished.</p>';
		return;
	}

	// check the max entries limit
	$max_entries = get_post_meta( $contest_id, 'contest-max-entries', true );
	if ( $max_entries > 0 && gmr_contests_get_entries_count( $contest_id ) >= $max_entries ) {
		echo '<p>This contest has reached maximum number of entries!</p>';
		return;
	}

	// check if user has to be logged in
	$gigya_logged_in_exists = function_exists( 'is_gigya_user_logged_in' );
	$members_only = get_post_meta( $contest_id, 'contest-members-only', true );
	if ( $members_only && $gigya_logged_in_exists && ! is_gigya_user_logged_in() ) {
		echo '<p>You must be signed in to enter the contest! <a href="', esc_url( gmr_contests_get_login_url() ), '">Sign in here</a>.</p>';
		return;
	}

	// check if user can submit multiple entries
	$single_entry = get_post_meta( $contest_id, 'contest-single-entry', true );
	if ( $single_entry && function_exists( 'has_user_entered_contest' ) && has_user_entered_contest( $contest_id ) ) {
		echo '<p>You have already entered this contest!</p>';
		return;
	}

	// check min age restriction
	if ( ! $skip_age ) {
		$min_age = (int) get_post_meta( $contest_id, 'contest-min-age', true );
		if ( $min_age > 0 ) {
			if ( $gigya_logged_in_exists && is_gigya_user_logged_in() ) {
				$current_age = get_gigya_user_field( 'age' );
				if ( $current_age < $min_age ) {
					echo '<p>You must be at least ', $min_age, ' years old to enter the contest!</p>';
					return;
				}
			} else {
				echo '<p>Please, <a href="', esc_url( gmr_contests_get_login_url() ), '">sign in</a> or confirm that you are at least ', $min_age, ' years old.</p>';
				echo '<p><a class="min-age-yes" href="#">Yes, I am</a> &#8212; <a class="min-age-no" href="#">No, I am not</a></p>';
				return;
			}
		}
	}

	// render the form
	GreaterMediaFormbuilderRender::render( $contest_id );
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
 * Processes contest submission.
 * 
 * @action gmr_contest_submit
 */
function gmr_contests_process_form_submission() {
	if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';

	$submitted_values = $submitted_files  = array();
	
	$contest_id = get_the_ID();
	$form = @json_decode( get_post_meta( $contest_id, 'embedded_form', true ) );
	foreach ( $form as $field ) {
		$post_array_key = 'form_field_' . $field->cid;
		if ( 'file' === $field->field_type ) {
			if ( isset( $_FILES[ $post_array_key ] ) && file_is_valid_image( $_FILES[ $post_array_key ]['tmp_name'] ) ) {
				$file_id = media_handle_upload( $post_array_key, $contest_id, array( 'post_status' => 'private' ) );
				$submitted_files[ $field->cid ] = $submitted_values[ $field->cid ] = $file_id;
			}
		} else if ( isset( $_POST[ $post_array_key ] ) ) {
			if ( is_scalar( $_POST[ $post_array_key ] ) ) {
				$submitted_values[ $field->cid ] = sanitize_text_field( $_POST[ $post_array_key ] );
			} else if ( is_array( $_POST[ $post_array_key ] ) ) {
				$submitted_values[ $field->cid ] = array_map( 'sanitize_text_field', $_POST[ $post_array_key ] );
			}
		}
	}

	list( $entrant_reference, $entrant_name ) = gmr_contests_get_gigya_entrant_id_and_name();

	$entry = GreaterMediaContestEntryEmbeddedForm::create_for_data( $contest_id, $entrant_name, $entrant_reference, GreaterMediaContestEntry::ENTRY_SOURCE_EMBEDDED_FORM, json_encode( $submitted_values ) );
	$entry->save();

	gmr_contests_handle_submitted_files( $submitted_files, $entry );

	do_action( 'greatermedia_contest_entry_save', $entry );
	delete_transient( 'contest_entries_' . $contest_id );

	echo wpautop( get_post_meta( $contest_id, 'form-thankyou', true ) );

	$fields = GreaterMediaFormbuilderRender::parse_entry( $contest_id, $entry->post_id() );
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
	
	add_post_meta( $ugc->post->ID, 'contest_entry_id', $entry->post_id() );
	if ( function_exists( 'get_gigya_user_id' ) ) {
		add_post_meta( $ugc->post->ID, 'gigya_user_id', get_gigya_user_id() );
	}
}

/**
 * Get Gigya ID and build name, from Gigya session data if available
 *
 * @return array
 */
function gmr_contests_get_gigya_entrant_id_and_name() {
	$entrant_name = 'Anonymous Listener';
	$entrant_reference = null;
	
	if ( class_exists( '\GreaterMedia\Gigya\GigyaSession' ) ) {
		$gigya_session = \GreaterMedia\Gigya\GigyaSession::get_instance();
		$gigya_id = $gigya_session->get_user_id();
		if ( ! empty( $gigya_id ) ) {
			$entrant_reference = $gigya_id;
			$entrant_name      = $gigya_session->get_key( 'firstName' ) . ' ' . $gigya_session->get_key( 'lastName' );
		}
	}

	return array( $entrant_reference, $entrant_name );
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
		'posts_per_page' => 20,
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
 * @param int $submission_id The contest submission id.
 * @return string The author name if available.
 */
function gmr_contest_submission_get_author( $submission_id = null ) {
	if ( empty( $submission_id ) ) {
		$submission_id = get_the_ID();
	}
	
	$author = 'guest';
	if ( function_exists( 'get_gigya_user_profile' ) && ( $gigya_uid = get_post_meta( get_the_ID(), 'gigya_user_id', true ) ) ) {
		$profile = get_gigya_user_profile( $gigya_uid );
		if ( ! empty( $profile ) ) {
			$profile = filter_var_array( $profile, array(
				'firstName' => FILTER_DEFAULT,
				'lastName'  => FILTER_DEFAULT,
			) );

			$author = "{$profile['firstName']} {$profile['lastName']}";
		}
	}

	return $author;
}

/**
 * Adds table row actions to contest records.
 *
 * @filter post_row_actions
 * @param array $actions The initial array of post actions.
 * @param WP_Post $post The post object.
 * @return array The array of post actions.
 */
function gmr_contests_add_table_row_actions( $actions, WP_Post $post ) {
	// do nothing if it is not a contest object
	if ( GMR_CONTEST_CPT != $post->post_type ) {
		return $actions;
	}

	// add contest winners action
	$link = admin_url( 'edit.php?post_type=contest_entry&contest_id=' . $post->ID );
	$actions['gmr-contest-winner'] = '<a href="' . esc_url( $link ) . '">Winners</a>';

	return $actions;
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