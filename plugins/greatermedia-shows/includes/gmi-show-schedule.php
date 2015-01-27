<?php

// action hooks
add_action( 'admin_menu', 'gmrs_register_episode_page' );
add_action( 'admin_enqueue_scripts', 'gmrs_enqueue_episode_scripts' );
add_action( 'admin_action_gmr_add_show_episode', 'gmrs_add_show_episode' );
add_action( 'admin_action_gmr_delete_show_episode', 'gmrs_delete_show_episode' );
add_action( 'future_to_publish', 'gmrs_prolong_show_episode' );
add_action( 'admin_bar_menu', 'gmrs_add_admin_bar_items', 100 );

// filter hooks
add_filter( 'gmr_blogroll_widget_item', 'gmrs_get_blogroll_widget_episode_item' );

/**
 * Adds view/edit show schedule links to the admin bar menu.
 *
 * @action admin_bar_menu 100
 * @param WP_Admin_Bar $admin_bar The admin bar object.
 */
function gmrs_add_admin_bar_items( WP_Admin_Bar $admin_bar ) {
	if ( is_admin() ) {
		if ( isset( $_REQUEST['page'] ) && 'episode-schedule' == $_REQUEST['page'] ) {
			$admin_bar->add_menu( array(
				'id' => 'view-show-schedule',
				'title' => 'View Show Schedule',
				'href' => get_post_type_archive_link( ShowsCPT::SHOW_CPT ),
				'meta' => array(
					'title' => 'View Show Schedule',
				),
			) );
		}
	} else {
		if ( is_post_type_archive( ShowsCPT::SHOW_CPT ) ) {
			$admin_bar->add_menu( array(
				'id' => 'edit-show-schedule',
				'title' => 'Edit Show Schedule',
				'href' => admin_url( '/edit.php?page=episode-schedule&post_type=' . ShowsCPT::SHOW_CPT ),
				'meta' => array(
					'title' => 'Edit Show Schedule',
				),
			) );
		}
	}
}

/**
 * Creates new episode each time the current one is published.
 *
 * @param WP_Post $post Currently published episode.
 */
function gmrs_prolong_show_episode( $post ) {
	if ( ShowsCPT::EPISODE_CPT != $post->post_type || get_post_meta( $post->ID, 'repeat-episode', true ) < 1 ) {
		return;
	}

	// disable Edit Flow custom statuse influence on post_date_gmt field
	gmrs_disable_editflow_custom_status_influence();

	// create new episode
	$new_post = $post->to_array();
	unset( $new_post['ID'] );

	$new_post['post_date'] = date( DATE_ISO8601, strtotime( $post->post_date ) + WEEK_IN_SECONDS );
	$new_post['post_date_gmt'] = date( DATE_ISO8601, strtotime( $post->post_date_gmt ) + WEEK_IN_SECONDS );
	$new_post['post_status'] = 'future';

	wp_insert_post( $new_post );

	// enable back Edit Flow influence
	gmrs_enable_ediflow_custom_status_influence();
}

/**
 * Enqueues scripts and styles required for show episode page.
 *
 * @action admin_enqueue_scripts
 * @global string $gmrs_show_episode_page The show episode page slug.
 * @param string $current_page The current page slug.
 */
function gmrs_enqueue_episode_scripts( $current_page ) {
	global $gmrs_show_episode_page;
	if ( $gmrs_show_episode_page == $current_page ) {
		$postfix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'meta_box', GMEDIA_SHOWS_URL . "assets/css/greatermedia_shows{$postfix}.css", null, GMEDIA_SHOWS_VERSION );
		wp_enqueue_style( 'jquery-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );

		wp_enqueue_script( 'meta_box', GMEDIA_SHOWS_URL . "assets/js/greatermedia_shows{$postfix}.js", array( 'jquery', 'jquery-ui-datepicker' ), GMEDIA_SHOWS_VERSION, true );
	}
}

/**
 * Registers show episode page.
 *
 * @action admin_menu
 * @global string $gmrs_show_episode_page The show episode page slug.
 */
function gmrs_register_episode_page() {
	global $gmrs_show_episode_page;
	$gmrs_show_episode_page = add_submenu_page( 'edit.php?post_type=' . ShowsCPT::SHOW_CPT, 'Show Schedule', 'Schedule', 'manage_options', 'episode-schedule', 'gmrs_render_episode_schedule_page' );
}

/**
 * Adds new show episode.
 *
 * @action admin_action_gmr_add_show_episode
 */
function gmrs_add_show_episode() {
	check_admin_referer( 'gmr_add_show_episode' );

	$filter_args = array( 'filter' => FILTER_VALIDATE_INT, 'options' => array( 'min_range' => 0 ) );
	$data = filter_input_array( INPUT_POST, array(
		'show'       => $filter_args,
		'date'       => FILTER_DEFAULT,
		'start_time' => $filter_args,
		'end_time'   => $filter_args,
		'repeat'     => $filter_args,
	) );

	if ( empty( $data['show'] ) || ! ( $show = get_post( $data['show'] ) ) || $show->post_type != ShowsCPT::SHOW_CPT ) {
		wp_die( 'The show has not been found.', '', array( 'back_link' => true ) );
	}

	if ( ( $date = strtotime( $data['date'] ) ) === false ) {
		wp_die( 'Wrong date has been selected.', '', array( 'back_link' => true ) );
	}

	$offset = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
	$now = current_time( 'timestamp', 1 );
	$today_gmt = $now - $now % DAY_IN_SECONDS - $offset;
	$start_date = $date + $data['start_time'];
	$start_date_gmt = $start_date - $offset;
	if ( $start_date_gmt < $today_gmt ) {
		wp_die( 'Please, select a date in the future.', '', array( 'back_link' => true ) );
	}
	
	if ( $data['start_time'] > $data['end_time'] ) {
		wp_die( 'Please, select the end time greater than start time.', '', array( 'back_link' => true ) );
	}

	$iterations = 1;
	$skip_daysofweek = array();
	switch ( $data['repeat'] ) {
		case 2: 
			$iterations = 7;
			break;
		case 3:
			$iterations = 7;
			$skip_daysofweek = array( 6, 7 );
			break;
		case 4:
			$iterations = 7;
			$skip_daysofweek = array( 1, 2, 3, 4, 5 );
			break;
	}

	$interval = $data['end_time'] - $data['start_time'];
	$episode = gmrs_get_show_episode_at( $start_date_gmt );
	if ( $episode ) {
		$episode_start_date = strtotime( $episode->post_date_gmt );
		$episode_end_date = $episode_start_date + $episode->menu_order;
		if ( $start_date_gmt <= $episode_start_date || $start_date_gmt < $episode_end_date ) {
			wp_die( 'Selected slot is already taken. Please, find another slot.', '', array( 'back_link' => true ) );
		}
	}

	// disable Edit Flow custom statuse influence on post_date_gmt field
	gmrs_disable_editflow_custom_status_influence();

	// schedule episodes
	$inserted = $iteration = 0;
	while ( $iteration < $iterations ) {
		if ( $iteration++ > 0 ) {
			$start_date += DAY_IN_SECONDS;
			$start_date_gmt += DAY_IN_SECONDS;
		}

		if ( in_array( (int) date( 'N', $start_date ), $skip_daysofweek ) ) {
			continue;
		}

		$post_id = wp_insert_post( array(
			'post_title'    => $show->post_title,
			'post_type'     => ShowsCPT::EPISODE_CPT,
			'post_status'   => 'future',
			'post_date'     => date( DATE_ISO8601, $start_date ),
			'post_date_gmt' => date( DATE_ISO8601, $start_date_gmt ),
			'post_parent'   => $show->ID,
			'menu_order'    => $interval,
		) );

		if ( $post_id ) {
			$inserted++;
			add_post_meta( $post_id, 'repeat-episode', $data['repeat'] ? 1 : 0 );
		}
	}

	// enable back Edit Flow influence
	gmrs_enable_ediflow_custom_status_influence();

	// save submitted fields into cookie
	$cookie_path = parse_url( admin_url( '/' ), PHP_URL_PATH );
	setcookie( 'gmr_show_schedule', urlencode( serialize( array(
		'show'       => $show->ID,
		'repeat'     => $data['repeat'],
		'start_time' => $data['start_time'],
		'end_time'   => $data['end_time'],
		'date'       => strtotime( $data['date'] ),
	) ) ), 0, $cookie_path );

	// redirect back to the scheduler page
	$redirect = add_query_arg( array( 'created' => $inserted ? 1 : 0, 'deleted' => false ), wp_get_referer() );
	wp_redirect( $redirect );
	exit;
}

/**
 * Deletes show episode.
 *
 * @action admin_action_gmr_delete_show_episode
 */
function gmrs_delete_show_episode() {
	$episode_id = filter_input( INPUT_GET, 'episode', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
	check_admin_referer( 'gmr_delete_show_episode_' . $episode_id );

	$episode = get_post( $episode_id );
	if ( ! $episode || ShowsCPT::EPISODE_CPT != $episode->post_type ) {
		wp_die( 'The episode was not found.' );
	}

	$deleted = false;
	if ( filter_input( INPUT_GET, 'all', FILTER_VALIDATE_BOOLEAN ) ) {
		$query = new WP_Query( array(
			'post_type'           => ShowsCPT::EPISODE_CPT,
			'post_status'         => 'any',
			'post_parent'         => $episode->post_parent,
			'posts_per_page'      => 500,
			'no_found_rows'       => true,
			'ignore_sticky_posts' => true,
			'fields'              => 'ids',
			'date_query'          => array(
				array(
					'after'     => $episode->post_date_gmt,
					'inclusive' => true,
					'column'    => 'post_date_gmt'
				),
			),
		) );

		while ( $query->have_posts() ) {
			$deleted = wp_delete_post( $query->next_post(), true );
		}
	} else {
		$deleted = wp_delete_post( $episode_id, true );
	}

	$redirect = add_query_arg( array( 'created' => false, 'deleted' => $deleted ? 1 : 0 ), wp_get_referer() );
	wp_redirect( $redirect );
	exit;
}

/**
 * Renders show episode schedule page.
 */
function gmrs_render_episode_schedule_page() {
	$active = isset( $_COOKIE['gmr_show_schedule'] ) ? unserialize( urldecode( $_COOKIE['gmr_show_schedule'] ) ) : array();
	$active = wp_parse_args( $active, array(
		'show'       => false,
		'start_time' => false,
		'end_time'   => false,
		'date'       => strtotime( 'tomorrow' ),
		'repeat'     => 1,
	) );

	$now = current_time( 'timestamp', 1 );
	$active['date'] = $active['date'] >= time() 
		? date( 'Y-m-d', $active['date'] )
		: date( 'Y-m-d', $now );
	
	$episodes = gmrs_get_scheduled_episodes();
	$precision = 0.5; // 1 - each hour, 0.5 - each 30 mins, 0.25 - each 15 mins

	$days = array();
	$start = current( get_weekstartend( date( DATE_ISO8601 ) ) );
	$offset = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;

	$shows = new WP_Query( array(
		'post_type'           => ShowsCPT::SHOW_CPT,
		'post_status'         => 'publish',
		'posts_per_page'      => 500,
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
		'orderby'             => 'title',
		'order'               => 'ASC',
	) );

	$repeats = array(
		'once, this week only',
		'every week at this time',
		'every day at this time',
		'every working day at this time',
		'every weekend day at this time',
	);

	?><div id="show-schedule" class="wrap">
		<small class="code" style="float:right">(current time: <?php echo date( get_option( 'time_format' ), current_time( 'timestamp' ) ); ?>)</small>
		<h2>Show Schedule</h2>

		<?php foreach ( array( 'created', 'deleted' ) as $action ) : ?>
			<?php if ( isset( $_GET[ $action ] ) ) : ?>
				<?php if ( filter_input( INPUT_GET, $action, FILTER_VALIDATE_BOOLEAN ) ) : ?>
					<div class="updated"><p>The episode has been <?php echo $action; ?> successfully.</p></div>
				<?php else : ?>
					<div class="updated error"><p>The episode has not been <?php echo $action; ?>.</p></div>
				<?php endif; ?>
			<?php endif; ?>
		<?php endforeach; ?>

		<form id="schedule-form" action="admin.php" method="post">
			<?php wp_nonce_field( 'gmr_add_show_episode' ); ?>
			<input type="hidden" name="action" value="gmr_add_show_episode">

			<input type="submit" class="button button-primary" value="Add to the schedule">

			Add
			<select name="show">
				<?php while ( $shows->have_posts() ) : ?>
					<?php $show = $shows->next_post(); ?>
					<option value="<?php echo esc_attr( $show->ID ); ?>"<?php selected( $show->ID, $active['show'] ); ?>>
						<?php echo esc_html( $show->post_title ); ?>
					</option>
				<?php endwhile; ?>
			</select>
			<select name="repeat">
				<?php foreach ( $repeats as $index => $label ) : ?>
				<option value="<?php echo esc_attr( $index ); ?>"<?php selected( $index, $active['repeat'] ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
				<?php endforeach; ?>
			</select>
			starting
			<select id="start-from-date" name="date" required>
				<?php for ( $i = 0; $i <= 7; $i++, $now += DAY_IN_SECONDS ) : ?>
					<?php $now_y_m_d = date( 'Y-m-d', $now ); ?>
					<option value="<?php echo esc_attr( $now_y_m_d ); ?>"<?php selected( $now_y_m_d, $active['date'] ); ?>>
						<?php echo date( 'D, M-j', $now ); ?>
					</option>;
				<?php endfor; ?>
			</select>
			from
			<select name="start_time">
				<?php for ( $i = 0, $count = 24 / $precision; $i < $count; $i++ ) : ?>
					<?php $time = HOUR_IN_SECONDS * $precision * $i; ?>
					<option value="<?php echo $time; ?>"<?php selected( $time, $active['start_time'] ); ?>>
						<?php echo date( 'h:i A', $time ); ?>
					</option>
				<?php endfor; ?>
			</select>
			untill
			<select name="end_time">
				<?php for ( $i = 1; $i <= $count; $i++ ) : ?>
					<?php $time = HOUR_IN_SECONDS * $precision * $i; ?>
					<option value="<?php echo $time; ?>"<?php selected( $time, $active['end_time'] ); ?>>
						<?php echo date( 'h:i A', $time ); ?>
					</option>
				<?php endfor; ?>
			</select>
		</form>

		<table id="schedule-table">
			<thead>
				<tr>
					<?php for ( $i = 0; $i < 7; $i++, $start += DAY_IN_SECONDS ) : ?>
						<?php $days[] = date( 'N', $start ); ?>
						<th><?php echo date( 'l', $start ); ?></th>
					<?php endfor; ?>
				</tr>
			</thead>

			<tbody>
				<tr>
					<?php foreach ( $days as $day ) : ?>
						<td><?php
							if ( ! empty( $episodes[ $day ] ) ) :
								for ( $i = 0, $len = count( $episodes[ $day ] ); $i < $len; $i++ ) :
									$episode = $episodes[ $day ][ $i ];
									$styles = array(
										'top:' . ( ( strtotime( $episode->post_date ) % DAY_IN_SECONDS ) * 60 / HOUR_IN_SECONDS ) . 'px',
										'height:' . ( $episode->menu_order * 60 / HOUR_IN_SECONDS ) . 'px',
										'background-color:' . gmrs_show_color( $episode->post_parent, 0.15 ),
										'border-color:' . gmrs_show_color( $episode->post_parent, 0.75 ),
									);

									?><div class="show-<?php echo esc_attr( $episode->post_parent ); ?>"
										 style="<?php echo implode( ';', $styles ) ?>"
										 data-hover-color="<?php echo gmrs_show_color( $episode->post_parent, 0.4 ) ?>">
										
										<small>
											<?php echo date( 'M d', strtotime( $episode->post_date_gmt ) + $offset ); ?><br>
											<?php echo date( 'h:i A', strtotime( $episode->post_date_gmt ) + $offset ); ?><br>
											<?php echo date( 'h:i A', strtotime( $episode->post_date_gmt ) + $episode->menu_order + $offset ); ?><br>
										</small>

										<b><?php echo esc_html( $episode->post_title ); ?></b>
										
										<div>
											<?php $delete_url = 'admin.php?action=gmr_delete_show_episode&episode=' . $episode->ID ?>
											<a class="remove-show" href="<?php echo esc_url( wp_nonce_url( $delete_url, 'gmr_delete_show_episode_' . $episode->ID ) ) ?>">Remove</a>
										</div>
									</div><?php
								endfor;
							endif;
						?></td>
					<?php endforeach; ?>
				</tr>
			</tbody>
		</table>

		<script id="schedule-remove-popup" type="text/html">
			<div class="popup-wrapper">
				<div class="popup">
					<h1 class="title">Delete recurring show</h1>

					<p>Would you like to delete only this instance of the show, or all shows in this series?</p>
					<p>
						<a href="{url}" class="button button-secondary">Only this instance</a>
						All other shows in this series will remain.
					</p>
					<p>
						<a href="{url}&all=true" class="button button-secondary">All shows in the series</a>
						All next shows in the series will be deleted.
					</p>
					<p class="footer">
						<a href="#" class="button button-cancel">Cancel this change</a>
					</p>
				</div>
			</div>
		</script>
	</div><?php
}

/**
 * Returns scheduled episodes.
 *
 * @param string $from The "from" date in GMT.
 * @param string $to The "to" date in GMT.
 * @return array The array of scheduled episodes.
 */
function gmrs_get_scheduled_episodes( $from = null, $to = null) {
	$query = new WP_Query();

	if ( empty( $from ) ) {
		$from = date( DATE_ISO8601, time() );
	}

	if ( empty( $to ) ) {
		$to = date( 'Y-m-d 23:59:59', strtotime( '+1 week' ) );
	}
	
	$posts = $query->query( array(
		'post_type'           => ShowsCPT::EPISODE_CPT,
		'post_status'         => 'any',
		'posts_per_page'      => 500,
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
		'orderby'             => 'date',
		'order'               => 'ASC',
		'date_query'          => array(
			array(
				'after'     => $from,
				'before'    => $to,
				'inclusive' => true,
				'column'    => 'post_date_gmt'
			),
		),
	) );

	$episodes = array();
	foreach ( $posts as $post ) {
		$dayofweek = date( 'N', strtotime( $post->post_date ) );
		if ( ! isset( $episodes[ $dayofweek ] ) ) {
			$episodes[ $dayofweek ] = array();
		}

		$show = get_post( $post->post_parent );
		if ( ! $show ) {
			continue;
		}
		
		$post->post_title = $show->post_title;
		
		$episodes[ $dayofweek ][] = $post;
	}

	foreach ( $episodes as &$dayofweek ) {
		usort( $dayofweek, 'gmrs_sort_episodes' );
	}

	return $episodes;
}

/**
 * Sorts episodes by time.
 *
 * @param WP_Post $a The first episode.
 * @param WP_Post $b The second episode.
 * @return int Returns 0 if time equals, -1 if a less b and 1 otherwise.
 */
function gmrs_sort_episodes( $a, $b ) {
	$time_a = strtotime( $a->post_date ) % DAY_IN_SECONDS;
	$time_b = strtotime( $b->post_date ) % DAY_IN_SECONDS;

	if ( $time_a == $time_b ) {
		return 0;
	}

	return $time_a < $time_b ? -1 : 1;
}

/**
 * Generates color by show id.
 *
 * @param int $show_id The show id.
 * @param float $opacity The color opacity.
 * @return string CSS rgba string.
 */
function gmrs_show_color( $show_id, $opacity ) {
	$hash = sha1( $show_id );
	return sprintf( 
		'rgba(%d, %d, %d, %f)',
		hexdec( substr( $hash, 0, 2 ) ),
		hexdec( substr( $hash, 2, 2 ) ),
		hexdec( substr( $hash, 4, 2 ) ),
		$opacity
	);
}

/**
 * Returns show episode at a certain time.
 *
 * @param int $time Optional timestamp which determines current time in the system. Could be used to get an episode before another one.
 * @return WP_Post|null The show episode object on success, otherwise NULL.
 */
function gmrs_get_show_episode_at( $time = false ) {
	if ( ! $time ) {
		$time = current_time( 'timestamp', 1 );
	}

	$query = new WP_Query();
	$episodes = $query->query( array(
		'post_type'           => ShowsCPT::EPISODE_CPT,
		'post_status'         => 'any',
		'posts_per_page'      => 1,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'orderby'             => 'date',
		'order'               => 'DESC',
		'date_query'          => array(
			array(
				'before'    => date( DATE_ISO8601, $time ),
				'after'     => date( 'Y-m-d 00:00:00', $time ),
				'inclusive' => true,
				'column'    => 'post_date_gmt'
			),
		),
	) );

	if ( empty( $episodes ) ) {
		return null;
	}

	$episode = current( $episodes );
	$started = strtotime( $episode->post_date_gmt );
	$finished = $started + $episode->menu_order;

	return $started < $time && $time < $finished ? $episode : null;
}

/**
 * Returns current show episode.
 *
 * @return WP_Post|null The show episode object on success, otherwise NULL.
 */
function gmrs_get_current_show_episode() {
	$transient = 'current_show_episode';
	$current_episode = get_transient( $transient );
	if ( $current_episode ) {
		return get_post( $current_episode );
	}

	// current show might be undefined, so we should return NULL in this case.
	if ( $current_episode === null ) {
		return null;
	}

	$current_episode = gmrs_get_show_episode_at( false );
	if ( ! $current_episode ) {
		// current show is undefined, so create empty transient for a minute and
		// then recheck it again if a new episode starts
		set_transient( $transient, null, MINUTE_IN_SECONDS );
		return null;
	}

	$expiration = strtotime( $current_episode->post_date_gmt ) + $current_episode->menu_order - current_time( 'timestamp', 1 );
	set_transient( $transient, $current_episode->ID, $expiration );

	return $current_episode;
}

/**
 * Returns a show at a certain time.
 *
 * @param int $time Optional timestamp which determines current time in the system. Could be used to get an episode before another one.
 * @return WP_Post|null The show object on success, otherwise NULL.
 */
function gmrs_get_show_at( $time = false ) {
	$episode = $time 
		? gmrs_get_show_episode_at( $time )
		: gmrs_get_current_show_episode();
	
	if ( ! empty( $episode ) ) {
		$show = get_post( $episode->post_parent );
		if ( $show && ShowsCPT::SHOW_CPT == $show->post_type ) {
			$show->starts = strtotime( $episode->post_date_gmt );
			$show->ends = $show->starts + $episode->menu_order;
			return $show;
		}
	}

	return null;
}

/**
 * Returns current show.
 *
 * @return WP_Post|null The show object on success, otherwise NULL.
 */
function gmrs_get_current_show() {
	return gmrs_get_show_at( false );
}

/**
 * Returns the next show.
 *
 * @return WP_Post|null The next show object on success, otherwise NULL.
 */
function gmrs_get_next_show() {
	$current_episode = gmrs_get_current_show_episode();
	if ( ! $current_episode ) {
		return null;
	}

	$finished = strtotime( $current_episode->post_date_gmt ) + $current_episode->menu_order;
	return gmrs_get_show_at( $finished + MINUTE_IN_SECONDS );
}

/**
 * Determines whether the episode is on air or not.
 *
 * @param WP_Post $episode The episode object.
 * @return boolean TRUE if the episode is on air, otherwise FALSE.
 */
function gmrs_is_episode_onair( WP_Post $episode ) {
	$started = strtotime( $episode->post_date_gmt );
	$interval = $episode->menu_order;
	if ( empty( $started ) || empty( $interval ) ) {
		return false;
	}

	$current_time = current_time( 'timestamp', 1 );
	$ended = $started + $interval;
	
	return $started <= $current_time && $current_time <= $ended;
}

/**
 * Disables Edit Flow custom status influence on post_date_gmt field.
 * 
 * @global edit_flow $edit_flow The Edit Flow plugin instance.
 * @global boolean $gmrs_editflow_custom_status_disabled Determines whether or not this filter has been previously disabled.
 */
function gmrs_disable_editflow_custom_status_influence() {
	global $edit_flow, $gmrs_editflow_custom_status_disabled;

	if ( $edit_flow && ! empty( $edit_flow->custom_status ) && is_a( $edit_flow->custom_status, 'EF_Custom_Status' ) ) {
		$gmrs_editflow_custom_status_disabled = true;
		remove_filter( 'wp_insert_post_data', array( $edit_flow->custom_status, 'fix_custom_status_timestamp' ), 10, 2 );
	}
}

/**
 * Enables Edit Flow custom status influence on post_date_gmt field.
 * 
 * @global edit_flow $edit_flow The Edit Flow plugin instance.
 * @global boolean $gmrs_editflow_custom_status_disabled Determines whether or not this filter has been previously disabled.
 */
function gmrs_enable_ediflow_custom_status_influence() {
	global $edit_flow, $gmrs_editflow_custom_status_disabled;

	if ( $gmrs_editflow_custom_status_disabled && $edit_flow && ! empty( $edit_flow->custom_status ) && is_a( $edit_flow->custom_status, 'EF_Custom_Status' ) ) {
		$gmrs_editflow_custom_status_disabled = false;
		add_filter( 'wp_insert_post_data', array( $edit_flow->custom_status, 'fix_custom_status_timestamp' ), 10, 2 );
	}
}

/*
 * Returns blogroll episode HTML for live link widget.
 *
 * @filter gmr_blogroll_widget_item
 * @param string $item The initial HTML of a widget item.
 * @return string Show episode HTML if it is an episode post, otherwise initial HTML.
 */
function gmrs_get_blogroll_widget_episode_item( $item ) {
	$episode = get_post();
	if ( ! $episode || ShowsCPT::EPISODE_CPT != $episode->post_type ) {
		return $item;
	}

	$item = esc_html( get_the_title() );
	if ( get_post_meta( $episode->post_parent, 'show_homepage', true ) ) {
		$item = sprintf( '<a href="%s">%s</a>', get_permalink( $episode->post_parent ), $item );
	}

	$post_date = strtotime( $episode->post_date_gmt ) + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
	$post_date = date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $post_date );

	return sprintf( '<div class="live-link__type--show"><div class="live-link__title" title="%s">%s</div></div>', esc_attr( $post_date ), $item );
}
