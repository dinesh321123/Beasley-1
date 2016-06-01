<?php

// action hooks
add_action( 'admin_init', 'gmr_contests_check_entries_permissions' );
add_action( 'admin_menu', 'gmr_contests_register_winners_page' );
add_action( 'post_submitbox_start', 'gmr_contest_view_entries_link', 11 );
add_action( 'manage_' . GMR_CONTEST_ENTRY_CPT . '_posts_custom_column', 'gmr_contests_render_contest_entry_column', 10, 2 );
add_action( 'admin_action_gmr_contest_entry_mark_winner', 'gmr_contests_mark_contest_winner' );
add_action( 'admin_action_gmr_contest_entry_mark_bulk_winners', 'gmr_contests_mark_bulk_contest_winner' );
add_action( 'admin_action_gmr_contest_entry_unmark_winner', 'gmr_contests_unmark_contest_winner' );
add_action( 'admin_action_gmr_contest_export', 'gmr_contests_export_to_csv' );
add_action( 'pre_get_posts', 'gmr_contest_adjust_contest_entries_query' );
add_action( 'gmr_do_contest_export', 'gmr_do_contest_export' );

// filter hooks
add_filter( 'post_row_actions', 'gmr_contests_add_table_row_actions', 10, 2 );
add_filter( 'parent_file', 'gmr_contests_adjust_winners_page_admin_menu' );
add_filter( 'parent_file', 'gmr_contests_adjust_current_admin_menu' );

/**
 * Checks user capabilities to see entries page.
 */
function gmr_contests_check_entries_permissions() {
	global $pagenow;

	if ( 'admin.php' == $pagenow && isset( $_REQUEST['page'] ) && 'gmr-contest-winner' == $_REQUEST['page'] && ! current_user_can( 'edit_contest', filter_input( INPUT_GET, 'contest_id' ) ) ) {
		wp_die( "You don't have sufficient permissions to view contest entries." );
	}
}

/**
 * Renders link to access contest entries.
 */
function gmr_contest_view_entries_link() {
	if ( ! current_user_can( 'edit_contest_entries' ) ) {
		return;
	}

	$post = get_post();
	$post_status = get_post_status_object( $post->post_status );

	if ( GMR_CONTEST_CPT == $post->post_type && $post_status->public ) :
		echo '<div id="contest-entries-link">';
			echo '<a class="button" href="', admin_url( 'admin.php?page=gmr-contest-winner&contest_id=' . $post->ID ), '">View Entries</a>';
		echo '</div>';
	endif;
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
	$link = admin_url( 'admin.php?page=gmr-contest-winner&contest_id=' . $post->ID );
	$actions['gmr-contest-winner'] = '<a href="' . esc_url( $link ) . '">Entries</a>';

	return $actions;
}

/**
 * Registers contest winner page in the system.
 *
 * @action admin_menu
 * @global array $_registered_pages The array of already registered pages.
 */
function gmr_contests_register_winners_page() {
	global $_registered_pages;

	$page_hook = get_plugin_page_hookname( 'gmr-contest-winner', '' );
	$_registered_pages[ $page_hook ] = true;

	add_action( $page_hook, 'gmr_contests_render_winner_page' );
}

/**
 * Renders contest winner selection page.
 *
 * @global string $typenow The current post type.
 * @global string $post_type_object The current post type object.
 */
function gmr_contests_render_winner_page() {
	global $typenow, $post_type_object;

	$contest = filter_input( INPUT_GET, 'contest_id', FILTER_VALIDATE_INT );
	if ( ! $contest || ! ( $contest = get_post( $contest ) ) || GMR_CONTEST_CPT != $contest->post_type ) {
		wp_die( 'Contest has not been found.' );
	}

	if ( ! current_user_can( 'edit_contest', $contest->ID ) ) {
		wp_die( "You don't have sufficient permissions to view contest entries." );
	}

	// fake post type to make standard WP_Posts_List_Table class working properly
	$_GET['post_type'] = GMR_CONTEST_ENTRY_CPT;

	// override globals to make posts table class working properly
	$post_type = $typenow = GMR_CONTEST_ENTRY_CPT;
	$post_type_object = get_post_type_object( $post_type );

	// create table class
	$wp_list_table = new GMR_Contest_Entries_Table( array( 'screen' => GMR_CONTEST_ENTRY_CPT, 'plural' => 'entry_id' ) );
	if ( filter_input( INPUT_GET, 'noheader', FILTER_VALIDATE_BOOLEAN ) ) {
		$redirect = wp_get_referer();

		$action = $wp_list_table->current_action();
		if ( $action ) {
			do_action( 'admin_action_' . $action );
		} else {
			$random = isset( $_GET['get_random'] ) ? filter_input( INPUT_GET, 'random', FILTER_VALIDATE_INT ) : false;
			$redirect = add_query_arg( 'random', $random, $redirect );
		}

		wp_redirect( $redirect );
		exit;
	}

	// winners table
	$winners = new GMR_Contest_Winners_Table( array( 'contest_id' => $contest->ID ) );
	$winners->prepare_items();

	// links
	$contests_link = admin_url( 'edit.php?post_type=' . GMR_CONTEST_CPT );
	$export_link = admin_url( 'admin.php?action=gmr_contest_export&contest=' . $contest->ID );
	$export_link = wp_nonce_url( $export_link, 'gmr-contest-export' );

	?><div id="contest-winner-selection" class="wrap">
		<h2>
			Entries:
			<a href="<?php echo get_edit_post_link( $contest->ID ); ?>"><?php echo esc_html( $contest->post_title ); ?></a>
			<a class="add-new-h2" href="<?php echo esc_url( $contests_link ); ?>">All Contests</a>
			<?php if ( current_user_can( 'export_contest_entries' ) ) : ?>
				<a class="add-new-h2" href="<?php echo esc_url( $export_link ); ?>">Export to CSV</a>
			<?php endif; ?>
		</h2>

		<?php if ( filter_input( INPUT_GET, 'export', FILTER_VALIDATE_BOOLEAN ) ) : ?>
			<div class="updated">
				<p>Export process has been started. We will email you a CSV file when export is finished. If you don't receive an email in the nearest time, then check your spam folder.</p>
			</div>
		<?php endif; ?>

		<?php if ( $winners->has_items() ) : ?>
			<h3>Selected Winners</h3>
			<?php $winners->display(); ?>

			<h3>All Entries</h3>
		<?php endif; ?>

		<form id="posts-filter">
			<input type="hidden" name="noheader" value="true">
			<input type="hidden" name="page" value="<?php echo esc_html( filter_input( INPUT_GET, 'page' ) ); ?>">
			<input type="hidden" name="contest_id" value="<?php echo esc_attr( $contest->ID ); ?>">
			<?php wp_nonce_field( 'gmr_contest_entries' ); ?>

			<?php $wp_list_table->prepare_items(); ?>
			<?php $wp_list_table->display(); ?>
		</form>
	</div><?php
}

/**
 * Exports contest entries into as a CSV file.
 *
 * @action admin_action_gmr_contest_export
 */
function gmr_contests_export_to_csv() {
	check_admin_referer( 'gmr-contest-export' );

	$contest = filter_input( INPUT_GET, 'contest', FILTER_VALIDATE_INT );
	if ( ! $contest || ! ( $contest = get_post( $contest ) ) || GMR_CONTEST_CPT != $contest->post_type ) {
		status_header( 404 );
		exit;
	}

	if ( ! current_user_can( 'export_contest_entries' ) ) {
		wp_die( "You don't have sufficient permissions to export contest entries." );
		exit;
	}

	wp_async_task_add( 'gmr_do_contest_export', array(
		'contest' => $contest->ID,
		'email'   => wp_get_current_user()->user_email,
	), 'high' );

	$redirect = admin_url( 'admin.php?page=gmr-contest-winner&export=1&contest_id=' . $contest->ID );
	wp_redirect( $redirect );
	exit;
}

/**
 * Performs contest export.
 *
 * @param array $args The export arguments.
 */
function gmr_do_contest_export( $args ) {
	if ( empty( $args['contest'] ) || ! ( $contest = get_post( $args['contest'] ) ) || GMR_CONTEST_CPT != $contest->post_type ) {
		return;
	}

	$dir = get_temp_dir();
	$csv_file = $dir . wp_unique_filename( $dir, $contest->post_name . date( '-Y-m-d' ) . '.csv' );
	$zip_file = $dir . wp_unique_filename( $dir, $contest->post_name . date( '-Y-m-d' ) . '.zip' );

	$handle = fopen( $csv_file, 'w' );
	if ( ! $handle ) {
		return;
	}

	$paged = 1;
	$query = new WP_Query();
	$date_format = get_option( 'date_format', 'm/d/Y' );

	$form = get_post_meta( $contest->ID, 'embedded_form', true );
	if ( ! empty( $form ) ) {
		if ( is_string( $form ) ) {
			$clean_form = trim( $form, '"' );
			$form = json_decode( $clean_form );
		}
	}

	$headers = array(
		'EntryDateTime',
		'Gigya First Name',
		'Gigya Last Name',
		'Gigya Email',
		'Gigya Address',
		'Gigya City',
		'Gigya State',
		'Gigya Zip',
		'Gigya Country',
		'Gigya Date of Birth',
		'Gigya Age',
		'Gigya Gender',
	);

	if ( $form ) {
		foreach ( $form as $field ) {
			$headers[] = $field->label;
		}
	}

	fputcsv( $handle, $headers );

	do {
		$query->query( array(
			'post_type'           => GMR_CONTEST_ENTRY_CPT,
			'post_parent'         => $contest->ID,
			'suppress_filters'    => true,
			'paged'               => $paged,
			'posts_per_page'      => 100,
			'ignore_sticky_posts' => true,
		) );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$entry = $query->next_post();

				$profile = get_post_meta( $entry->ID, 'entrant_reference', true );
				if ( ! empty( $profile ) ) {
					try {
						$profile = get_gigya_user_profile( $profile );
					} catch ( Exception $e ) {
						$profile = array();
					}
				}

				$birthday = (int) get_post_meta( $entry->ID, 'entrant_birth_date', true );
				if ( ! empty( $birthday ) ) {
					$birthday = date( $date_format, $birthday );
				}

				if ( ! empty( $profile['birthMonth'] ) && ! empty( $profile['birthDay'] ) && ! empty( $profile['birthYear'] ) ) {
					$birthday = new DateTime();
					$birthday->setDate( $profile['birthYear'], $profile['birthMonth'], $profile['birthDay'] );
					$birthday = $birthday->format( $date_format );
				}

				$zip = ! empty( $profile['zip'] ) ? $profile['zip'] : get_post_meta( $entry->ID, 'entrant_zip', true );
				$zip = str_pad( absint( $zip ), 5, '0', STR_PAD_LEFT );

				$row = array_merge( array( $entry->post_date ), gmr_contest_get_entry_author( $entry->ID, 'array' ), array(
					gmr_contest_get_entry_author_email( $entry->ID ),
					! empty( $profile['address'] ) ? $profile['address'] : '',
					! empty( $profile['city'] ) ? $profile['city'] : '',
					! empty( $profile['state'] ) ? $profile['state'] : '',
					$zip,
					! empty( $profile['country'] ) ? $profile['country'] : '',
					$birthday,
					! empty( $profile['age'] ) ? $profile['age'] : '',
					! empty( $profile['gender'] ) ? $profile['gender'] : get_post_meta( $entry->ID, 'entrant_gender', true ),
				) );

				if ( $form ) {
					$records = GreaterMediaFormbuilderRender::parse_entry( $contest->ID, $entry->ID, $form );

					foreach ( $records as $record ) {
						if ( $record['type'] == 'file' ) {
							$attachment = get_post( $record['value'] );
							$row[] = $attachment && 'attachment' == $attachment->post_type && 'inherit' == $attachment->post_status
								? wp_get_attachment_url( $attachment->ID )
								: '';
						} else {
							$row[] = is_array( $record['value'] )
								? implode( ',', $record['value'] )
								: $record['value'];
						}
					}
				}

				fputcsv( $handle, $row );
			}
		}

		$paged++;
	} while( $query->post_count > 0 );

	fclose( $handle );

	$attachment = $csv_file;
	if ( extension_loaded( 'zip' ) && class_exists( 'ZipArchive' ) ) {
		$zip = new ZipArchive();
		if ( $zip->open( $zip_file, ZipArchive::CREATE ) ) {
			$zip->addFile( $csv_file, basename( $csv_file ) );
			$zip->close();

			$attachment = $zip_file;
		}
	}

	$title = $contest->post_title . ' Entries';
	$message = 'Please, find in attach CSV file with all entries.';

	$mail_headers = array( 'From: no-reply@' . parse_url( home_url(), PHP_URL_HOST ) );
	if ( defined( 'GMR_CSV_EXPORT_BCC' ) && filter_var( GMR_CSV_EXPORT_BCC, FILTER_VALIDATE_EMAIL ) ) {
		$mail_headers[] = 'Bcc: ' . GMR_CSV_EXPORT_BCC;
	}

	wp_mail( $args['email'], $title, $message, $mail_headers, array( $attachment ) );

	@unlink( $csv_file );
	@unlink( $zip_file );
}

/**
 * Adjustes parent and submenu files.
 *
 * @filter parent_file
 * @global string $submenu_file The current submenu page.
 * @param string $parent_file The parent file name.
 * @return string The parent file.
 */
function gmr_contests_adjust_winners_page_admin_menu( $parent_file ) {
	global $submenu_file;

	if ( ! empty( $_REQUEST['page'] ) && 'gmr-contest-winner' == $_REQUEST['page'] ) {
		$parent_file = 'edit.php?post_type=' . GMR_CONTEST_CPT;
		$submenu_file = 'edit.php?post_type=' . GMR_CONTEST_CPT;
	}

	return $parent_file;
}

/**
 * Builds HTML string to render user submitted responses for display in the admin.
 * @param int $entry_id The post id for the entry.
 */
function gmr_contests_build_contest_responses_list( $entry_id ) {
	$contest_id = wp_get_post_parent_id( $entry_id );

	$fields = GreaterMediaFormbuilderRender::parse_entry( $contest_id, $entry_id );

	$responses_list = '';

	if ( ! empty( $fields ) ) :
		$responses_list .= '<dl class="contest__submission--entries">';

		foreach ( $fields as $field ) :
			if ( 'file' != $field['type'] ) :
				$responses_list .= '<dt>';
				$responses_list .= '<strong>' . esc_html( $field['label'] ) . '</strong>';
				$responses_list .= '</dt>';
				$responses_list .= '<dd>';
				$responses_list .= esc_html( is_array( $field['value'] ) ? implode( ', ', $field['value'] ) : $field['value'] );
				$responses_list .= '</dd>';
			endif;
		endforeach;

		$responses_list .= '</dl>';
	endif;

	return $responses_list;
}

/**
 * Renders custom columns for the contest entries table.
 *
 * @param string $column_name The column name which is gonna be rendered.
 * @param int $post_id The post id.
 */
function gmr_contests_render_contest_entry_column( $column_name, $post_id ) {
	$entry = get_post( $post_id );

	if ( '_gmr_thumbmail' == $column_name ) {

		$thumbnail = false;
		$submission = get_contest_entry_submission( $post_id );
		if ( $submission ) {
			$thumbnail = get_post_thumbnail_id( $submission->ID ) ;
		}

		if ( $thumbnail ) {
			echo wp_get_attachment_image( $thumbnail, array( 75, 75 ) );
		} else {
			echo '<img width="75" src="http://placehold.it/75&text=noimage" class="attachment-75x75">';
		}

	} elseif ( '_gmr_username' == $column_name ) {

		$gigya_id = get_post_meta( $post_id, 'entrant_reference', true );
		$winners = get_post_meta( $entry->post_parent, 'winner' );
		$is_winner = in_array( "{$post_id}:{$gigya_id}", $winners );

		echo '<b>';
			echo esc_html( gmr_contest_get_entry_author( $post_id ) );
			if ( $is_winner ) :
				echo ' <span class="dashicons dashicons-awards"></span>';
			endif;
		echo '</b>';

		echo '<div class="row-actions">';
			if ( $is_winner ) :
				$action_link = admin_url( 'admin.php?action=gmr_contest_entry_unmark_winner&entry=' . $post_id );
				$action_link = wp_nonce_url( $action_link, 'contest_entry_unmark_winner' );

				echo '<span class="unmark-winner">';
					echo '<a href="', esc_url( $action_link ), '">Unmark as Winner</a>';
				echo '</span>';
			else :
				$action_link = admin_url( 'admin.php?action=gmr_contest_entry_mark_winner&entry=' . $post_id );
				$action_link = wp_nonce_url( $action_link, 'contest_entry_mark_winner' );

				echo '<span class="mark-winner">';
					echo '<a href="', esc_url( $action_link ), '">Mark as a Winner</a>';
				echo '</span>';
			endif;
		echo '</div>';

	} elseif ( '_gmr_email' == $column_name ) {

		$email = gmr_contest_get_entry_author_email( $post_id );
		if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			echo '&#8212;';
		} else {
			printf( '<a href="mailto:%1$s" title="%1$s">%1$s</a>', $email );
		}

	} elseif ( '_gmr_submitted' == $column_name ) {

		printf(
			'<span title="%s">%s ago</span>',
			mysql2date( 'M j, Y H:i', $entry->post_date ),
			human_time_diff( strtotime( $entry->post_date ), current_time( 'timestamp' ) )
		);
	} elseif ( '_gmr_responses' == $column_name ) {

		printf( gmr_contests_build_contest_responses_list( $entry->ID ) );

	}  else {

		$form_column_name = substr( $column_name, strlen( '_gmr_form_' ) );
		$fields = GreaterMediaFormbuilderRender::parse_entry( $entry->post_parent, $entry->ID );
		if ( isset( $fields[ $form_column_name ] ) ) {

			$value = $fields[ $form_column_name ]['value'];
			if ( 'file' == $fields[ $form_column_name ]['type'] ) {
				echo wp_get_attachment_image( $value, array( 75, 75 ) );
			} elseif ( is_array( $value ) ) {
				echo implode( ', ', array_map( 'esc_html', $value ) );
			} else {
				echo esc_html( $value );
			}

		} else {
			echo '&#8212;';
		}

	}
}

/**
 * Adds contest entry to the winners list.
 *
 * @access protected
 * @param WP_Post $entry The contest entry object.
 */
function _gmr_contests_add_entry_to_winners( $entry ) {
	$gigya_id = get_post_meta( $entry->ID, 'entrant_reference', true );
	add_post_meta( $entry->post_parent, 'winner', "{$entry->ID}:{$gigya_id}" );
}

/**
 * Marks contest winner.
 */
function gmr_contests_mark_contest_winner() {
	check_admin_referer( 'contest_entry_mark_winner' );

	$entry = filter_input( INPUT_GET, 'entry', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
	if ( ! $entry || ! ( $entry = get_post( $entry ) ) || GMR_CONTEST_ENTRY_CPT != $entry->post_type ) {
		wp_die( 'Contest entry was not found.' );
	}

	_gmr_contests_add_entry_to_winners( $entry );

	wp_redirect( add_query_arg( 'random', false, wp_get_referer() ) );
	exit;
}

/**
 * Marks multiple entries as winner.
 */
function gmr_contests_mark_bulk_contest_winner() {
	check_admin_referer( 'gmr_contest_entries' );

	$entries = isset( $_GET['post'] ) ? (array) $_GET['post'] : array();
	$entries = array_filter( array_map( 'intval', $entries ) );
	foreach ( $entries as $entry_id ) {
		if ( ! $entry_id || ! ( $entry = get_post( $entry_id ) ) || GMR_CONTEST_ENTRY_CPT != $entry->post_type ) {
			continue;
		}

		_gmr_contests_add_entry_to_winners( $entry );
	}

	wp_redirect( add_query_arg( 'random', false, wp_get_referer() ) );
	exit;
}

/**
 * Unmarks contest winner.
 */
function gmr_contests_unmark_contest_winner() {
	check_admin_referer( 'contest_entry_unmark_winner' );

	$entry = filter_input( INPUT_GET, 'entry', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
	if ( ! $entry || ! ( $entry = get_post( $entry ) ) || GMR_CONTEST_ENTRY_CPT != $entry->post_type ) {
		wp_die( 'Contest entry was not found.' );
	}

	$gigya_id = get_post_meta( $entry->ID, 'entrant_reference', true );
	delete_post_meta( $entry->post_parent, 'winner', "{$entry->ID}:{$gigya_id}" );

	wp_redirect( add_query_arg( 'random', false, wp_get_referer() ) );
	exit;
}

/**
 * Adjustes contest entries query to display entries only for selected contest.
 *
 * @action pre_get_posts
 * @global string $typenow The current post type.
 * @param WP_Query $query The contest entry query.
 */
function gmr_contest_adjust_contest_entries_query( WP_Query $query ) {
	global $typenow;

	if ( GMR_CONTEST_ENTRY_CPT == $typenow && 'gmr-contest-winner' == filter_input( INPUT_GET, 'page' ) && $query->is_main_query() ) {
		$contest = filter_input( INPUT_GET, 'contest_id', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
		if ( $contest && ( $contest = get_post( $contest ) ) && GMR_CONTEST_CPT == $contest->post_type ) {
			$winners = get_post_meta( $contest->ID, 'winner' );
			if ( ! empty( $winners ) ) {
				$entries = array();
				foreach ( $winners as $winner ) {
					$entries[] = current( explode( ':', $winner, 2 ) );
				}

				$query->set( 'post__not_in', $entries );
			}

			$query->set( 'post_parent', $contest->ID );

			$random = filter_input( INPUT_GET, 'random', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
			if ( $random ) {
				$query->set( 'orderby', 'rand' );
				$query->set( 'posts_per_page', $random );
			}
		}
	}
}

/**
 * Adjustes parent and submenu files.
 *
 * @filter parent_file
 * @global string $submenu_file The current submenu page.
 * @global string $typenow The current post type.
 * @global string $pagenow The current admin page.
 * @return string The parent file.
 */
function gmr_contests_adjust_current_admin_menu( $parent_file ) {
	global $submenu_file, $typenow, $pagenow;

	if ( in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) && GMR_SUBMISSIONS_CPT == $typenow ) {
		$parent_file = 'edit.php?post_type=' . GMR_CONTEST_CPT;
		$submenu_file = 'edit.php?post_type=' . $typenow;
	} elseif ( GMR_CONTEST_ENTRY_CPT == $typenow && 'edit.php' == $pagenow ) {
		$parent_file = 'edit.php?post_type=' . GMR_CONTEST_CPT;
		$submenu_file = 'edit.php?post_type=' . GMR_CONTEST_CPT;
	}

	return $parent_file;
}

/**
 * Contest entries table.
 */
class GMR_Contest_Entries_Table extends WP_Posts_List_Table {

	/**
	 * Renders bulk actions dropdown at the top of the table.
	 *
	 * @access protected
	 * @param string $which The area where to render dropdown.
	 */
	protected function bulk_actions( $which = '' ) {
		if ( 'top' == $which ) {
			parent::bulk_actions( $which );
		}
	}

	/**
	 * Returns bulk actions array.
	 *
	 * @access protected
	 * @return array The array of bulk actions.
	 */
	protected function get_bulk_actions() {
		return array( 'gmr_contest_entry_mark_bulk_winners' => 'Mark as Winner' );
	}

	/**
	 * Displays view switcher. Disabled for current table.
	 *
	 * @access protected
	 * @param string $current_mode The view switcher mode.
	 */
	protected function view_switcher( $current_mode ) {
	}

	/**
	 * Displays extra table navigation. Disabled for current table.
	 *
	 * @access protected
	 * @global WP_Query $wp_query
	 * @param string $which The area where to render extra navigation.
	 */
	protected function extra_tablenav( $which ) {
		global $wp_query;

		$random = filter_input( INPUT_GET, 'random', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );

		if ( $which == 'top' ) :
			?><div class="alignleft actions">
				<input type="text" name="random" size="3" value="<?php echo ! empty( $random ) ? esc_attr( $random ) : 1; ?>">
				<?php submit_button( 'Random Entries', 'button', 'get_random', false ); ?>
				<?php if ( $random ) : ?>
					<?php submit_button( 'All Entries', 'apply', 'get_all', false ); ?>
					<i style="margin-left:1em;">Currently showing <?php echo esc_html( $random ); ?> entries of <?php echo $wp_query->found_posts ?>.</i>
				<?php endif; ?>
			</div><?php
		endif;
	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * @access protected
	 * @param string $which
	 */
	protected function display_tablenav( $which ) {
		if ( 'top' == $which ) :
			?><div class="tablenav <?php echo esc_attr( $which ); ?>">
				<div class="alignleft actions bulkactions">
					<?php $this->bulk_actions( $which ); ?>
				</div>
				<?php $this->extra_tablenav( $which ); ?>
				<?php $this->pagination( $which ); ?>
				<br class="clear">
			</div><?php
		endif;
	}

	/**
	 * Returns columns array.
	 *
	 * @access public
	 * @return array The array of columns.
	 */
	public function get_columns() {
		$columns = parent::get_columns();

		$contest = filter_input( INPUT_GET, 'contest_id', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
		if ( ! $contest || ! ( $contest = get_post( $contest ) ) || GMR_CONTEST_CPT != $contest->post_type ) {
			return $columns;
		}

		unset( $columns['title'], $columns['date'] );

		if ( gmr_contest_has_files( $contest->ID ) ) {
			$columns['_gmr_thumbmail'] = 'Thumbnail';
		}

		$columns['_gmr_username'] = 'Submitted by';
		$columns['_gmr_email'] = 'Email';
		$columns['_gmr_responses'] = 'Responses';
		$columns['_gmr_submitted'] = 'Submitted on';

		return $columns;
	}

}

/**
 * Contest entries table.
 */
class GMR_Contest_Winners_Table extends WP_List_Table {

	public function prepare_items() {
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $this->get_columns(), array(), $sortable );

		$winners = get_post_meta( $this->_args['contest_id'], 'winner' );
		if ( empty( $winners ) ) {
			$this->items = array();
			return;
		}

		$entries = array();
		foreach ( $winners as $winner ) {
			$entries[] = current( explode( ':', $winner, 2 ) );
		}

		$args = array(
			'post_type'           => GMR_CONTEST_ENTRY_CPT,
			'post_status'         => 'any',
			'post_parent'         => $this->_args['contest_id'],
			'post__in'            => $entries,
			'posts_per_page'      => 100,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		$query = new WP_Query();
		$this->items = $query->query( $args );
	}

	public function column__gmr_username( WP_Post $entry ) {
		$unmark = admin_url( 'admin.php?action=gmr_contest_entry_unmark_winner&entry=' . $entry->ID );
		$unmark = wp_nonce_url( $unmark, 'contest_entry_unmark_winner' );

		return '<b>' . gmr_contest_get_entry_author( $entry->ID ) . '</b>' . $this->row_actions( array(
			'unmark-winner' => '<a href="' . esc_url( $unmark ) . '">Unmark Winner</a>',
		) );
	}

	public function column__gmr_thumbmail( WP_Post $entry ) {
		$thumbnail = false;
		$submission = get_contest_entry_submission( $entry->ID );
		if ( $submission ) {
			$thumbnail = get_post_thumbnail_id( $submission->ID ) ;
		}

		if ( $thumbnail ) {
			return wp_get_attachment_image( $thumbnail, array( 75, 75 ) );
		}

		return '<img width="75" src="http://placehold.it/75&text=noimage" class="attachment-75x75">';
	}

	public function column__gmr_email( WP_Post $entry ) {
		$email = gmr_contest_get_entry_author_email( $entry->ID );
		if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			return '&#8212;';
		}

		return sprintf( '<a href="mailto:%1$s" title="%1$s">%1$s</a>', $email );
	}

	public function column__gmr_responses( WP_Post $entry ) {
		printf( gmr_contests_build_contest_responses_list( $entry->ID ) );
	}

	public function column__gmr_submitted( WP_Post $entry ) {
		return sprintf(
			'<span title="%s">%s ago</span>',
			mysql2date( 'M j, Y H:i', $entry->post_date ),
			human_time_diff( strtotime( $entry->post_date ), current_time( 'timestamp' ) )
		);
	}

	public function get_columns() {
		$actions = array();

		if ( gmr_contest_has_files( $this->_args['contest_id'] ) ) {
			$actions['_gmr_thumbmail'] = 'Thumbnail';
		}

		$actions['_gmr_username'] = 'Name';
		$actions['_gmr_email'] = 'Email';
		$actions['_gmr_responses'] = 'Responses';
		$actions['_gmr_submitted'] = 'Submitted';

		return $actions;
	}

	protected function view_switcher( $current_mode ) {
	}

	protected function extra_tablenav( $which ) {
	}

	protected function display_tablenav( $which ) {
	}

}
