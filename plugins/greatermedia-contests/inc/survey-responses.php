<?php

// action hooks
add_action( 'admin_init', 'gmr_survey_check_responses_permissions' );
add_action( 'admin_menu', 'gmr_surveys_register_responses_page' );
add_action( 'post_submitbox_start', 'gmr_survey_view_responses_link', 1 );
add_action( 'manage_' . GMR_SURVEY_RESPONSE_CPT . '_posts_custom_column', 'gmr_surveys_render_survey_response_column', 10, 2 );
add_action( 'pre_get_posts', 'gmr_survey_adjust_survey_responses_query' );
add_action( 'admin_action_gmr_survey_export', 'gmr_survey_export_to_csv' );
add_action( 'gmr_do_survey_export', 'gmr_do_survey_export' );

// filter hooks
add_filter( 'post_row_actions', 'gmr_surveys_add_table_row_actions', PHP_INT_MAX, 2 );
add_filter( 'parent_file', 'gmr_surveys_adjust_responses_page_admin_menu' );

/**
 * Checks user capabilities to see responses page.
 */
function gmr_survey_check_responses_permissions() {
	global $pagenow;

	if ( 'admin.php' == $pagenow && isset( $_REQUEST['page'] ) && 'gmr-survey-responses' == $_REQUEST['page'] && ! current_user_can( 'edit_survey', filter_input( INPUT_GET, 'survey_id' ) ) ) {
		wp_die( "You don't have sufficient permissions to view survey responses." );
	}
}

/**
 * Renders link to access survey responses.
 */
function gmr_survey_view_responses_link() {
	if ( ! current_user_can( 'edit_survey_responses' ) ) {
		return;
	}

	$post = get_post();
	$post_status = get_post_status_object( $post->post_status );

	if ( GMR_SURVEY_CPT == $post->post_type && $post_status->public ) :
		echo '<div id="survey-responses-link">';
			echo '<a class="button" href="', admin_url( 'admin.php?page=gmr-survey-responses&survey_id=' . $post->ID ), '">View Responses</a>';
		echo '</div>';
	endif;
}

/**
 * Adds table row actions to survey records.
 *
 * @filter post_row_actions
 * @param array $actions The initial array of post actions.
 * @param WP_Post $post The post object.
 * @return array The array of post actions.
 */
function gmr_surveys_add_table_row_actions( $actions, WP_Post $post ) {
	// do nothing if it is not a survey object
	if ( GMR_SURVEY_CPT != $post->post_type ) {
		return $actions;
	}

	// add survey responses action
	if ( current_user_can( 'edit_survey_responses' ) ) {
		$link = admin_url( 'admin.php?page=gmr-survey-responses&survey_id=' . $post->ID );
		$actions['gmr-survey-responses'] = '<a href="' . esc_url( $link ) . '">Responses</a>';
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


	return $actions;
}

/**
 * Registers survey response page in the system.
 *
 * @action admin_menu
 * @global array $_registered_pages The array of already registered pages.
 */
function gmr_surveys_register_responses_page() {
	global $_registered_pages;

	$page_hook = get_plugin_page_hookname( 'gmr-survey-responses', '' );
	$_registered_pages[ $page_hook ] = true;

	add_action( $page_hook, 'gmr_surveys_render_response_page' );
}

/**
 * Renders survey response selection page.
 *
 * @global string $typenow The current post type.
 * @global string $post_type_object The current post type object.
 */
function gmr_surveys_render_response_page() {
	global $typenow, $post_type_object;

	$survey = filter_input( INPUT_GET, 'survey_id', FILTER_VALIDATE_INT );
	if ( ! $survey || ! ( $survey = get_post( $survey ) ) || GMR_SURVEY_CPT != $survey->post_type ) {
		wp_die( 'Survey has not been found.' );
	}

	if ( ! current_user_can( 'edit_survey', $survey->ID ) ) {
		wp_die( "You don't have sufficient permissions to view contest entries." );
	}

	// fake post type to make standard WP_Posts_List_Table class working properly
	$_GET['post_type'] = GMR_SURVEY_RESPONSE_CPT;

	// override globals to make posts table class working properly
	$post_type = $typenow = GMR_SURVEY_RESPONSE_CPT;
	$post_type_object = get_post_type_object( $post_type );

	// create table class
	$wp_list_table = new GMR_Survey_Responses_Table( array( 'screen' => GMR_CONTEST_ENTRY_CPT, 'plural' => 'response_id' ) );
	if ( filter_input( INPUT_GET, 'noheader', FILTER_VALIDATE_BOOLEAN ) ) {
		$redirect = wp_get_referer();

		$action = $wp_list_table->current_action();
		if ( $action ) {
			do_action( 'admin_action_' . $action );
		}

		wp_redirect( $redirect );
		exit;
	}

	// links
	$survys_link = admin_url( 'edit.php?post_type=' . GMR_SURVEY_CPT );
	$export_link = admin_url( 'admin.php?action=gmr_survey_export&survey=' . $survey->ID );
	$export_link = wp_nonce_url( $export_link, 'gmr-survey-export' );

	?><div id="survey-response-selection" class="wrap">
		<h2>
			Responses:
			<a href="<?php echo get_edit_post_link( $survey->ID ); ?>"><?php echo esc_html( $survey->post_title ); ?></a>
			<a class="add-new-h2" href="<?php echo esc_url( $survys_link ); ?>">All Surveys</a>
			<?php if ( current_user_can( 'export_survey_responses' ) ) : ?>
				<a class="add-new-h2" href="<?php echo esc_url( $export_link ); ?>">Export to CSV</a>
			<?php endif; ?>
		</h2>

		<?php if ( filter_input( INPUT_GET, 'export', FILTER_VALIDATE_BOOLEAN ) ) : ?>
			<div class="updated">
				<p>Export process has been started. We will email you a CSV file when export is finished. If you don't receive an email in the nearest time, then check your spam folder.</p>
			</div>
		<?php endif; ?>

		<form id="posts-filter">
			<input type="hidden" name="noheader" value="true">
			<input type="hidden" name="page" value="<?php echo esc_html( filter_input( INPUT_GET, 'page' ) ); ?>">
			<input type="hidden" name="survey_id" value="<?php echo esc_attr( $survey->ID ); ?>">
			<?php wp_nonce_field( 'gmr_survey_responses' ); ?>

			<?php $wp_list_table->prepare_items(); ?>
			<?php $wp_list_table->display(); ?>
		</form>
	</div><?php
}

/**
 * Exports survey responses as CSV file.
 *
 * @action admin_action_gmr_survey_export
 */
function gmr_survey_export_to_csv() {
	check_admin_referer( 'gmr-survey-export' );

	if ( ! current_user_can( 'export_survey_responses' ) ) {
		wp_die( "You don't have sufficient permissions to export survey responses." );
		exit;
	}

	$survey = filter_input( INPUT_GET, 'survey', FILTER_VALIDATE_INT );
	if ( ! $survey || ! ( $survey = get_post( $survey ) ) || GMR_SURVEY_CPT != $survey->post_type ) {
		status_header( 404 );
		exit;
	}

	wp_async_task_add( 'gmr_do_survey_export', array(
		'survey' => $survey->ID,
		'email'  => wp_get_current_user()->user_email,
	), 'high' );

	$redirect = admin_url( 'admin.php?page=gmr-survey-responses&export=1&survey_id=' . $survey->ID );
	wp_redirect( $redirect );
	exit;
}

/**
 * Performs survey export.
 *
 * @param array $args The export arguments.
 */
function gmr_do_survey_export( $args ) {
	if ( empty( $args['survey'] ) || ! ( $survey = get_post( $args['survey'] ) ) || GMR_SURVEY_CPT != $survey->post_type ) {
		return;
	}

	$dir = get_temp_dir();
	$csv_file = $dir . wp_unique_filename( $dir, $survey->post_name . date( '-Y-m-d' ) . '.csv' );
	$zip_file = $dir . wp_unique_filename( $dir, $survey->post_name . date( '-Y-m-d' ) . '.zip' );

	$handle = fopen( $csv_file, 'w' );
	if ( ! $handle ) {
		return;
	}

	$paged = 1;
	$query = new WP_Query();
	$date_format = get_option( 'date_format', 'm/d/Y' );

	$form = get_post_meta( $survey->ID, 'survey_embedded_form', true );
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
			'post_type'           => GMR_SURVEY_RESPONSE_CPT,
			'post_parent'         => $survey->ID,
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
					$records = GreaterMediaFormbuilderRender::parse_entry( $survey->ID, $entry->ID, $form );
					foreach ( $records as $record ) {
						$row[] = is_array( $record['value'] ) ? implode( ',', $record['value'] ) : $record['value'];
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

	$title = $survey->post_title . ' Entries';
	$message = 'Please, find in attach CSV file with all responses.';

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
function gmr_surveys_adjust_responses_page_admin_menu( $parent_file ) {
	global $submenu_file;

	if ( ! empty( $_REQUEST['page'] ) && 'gmr-survey-responses' == $_REQUEST['page'] ) {
		$parent_file = 'edit.php?post_type=' . GMR_SURVEY_CPT;
		$submenu_file = 'edit.php?post_type=' . GMR_SURVEY_CPT;
	}

	return $parent_file;
}

/**
 * Renders custom columns for the survey responses table.
 *
 * @param string $column_name The column name which is gonna be rendered.
 * @param int $post_id The post id.
 */
function gmr_surveys_render_survey_response_column( $column_name, $post_id ) {
	$response = get_post( $post_id );

	if ( '_gmr_username' == $column_name ) {

		echo '<b>';
			echo esc_html( gmr_contest_get_entry_author( $post_id ) );
		echo '</b>';

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
			mysql2date( 'M j, Y H:i', $response->post_date ),
			human_time_diff( strtotime( $response->post_date ), current_time( 'timestamp' ) )
		);

	} elseif ( '_gmr_responses' == $column_name ) {

		$survey_id = wp_get_post_parent_id( $post_ID );

		$form = get_post_meta ( $survey_id, 'survey_embedded_form', true );
		$clean_form = trim( $form, '"' );
		$form = json_decode( $clean_form );

		$fields = GreaterMediaFormbuilderRender::parse_entry( $survey_id, $post_id, $form );

		if ( ! empty( $fields ) ) :
			?>
			<dl class="contest__submission--entries">
				<?php foreach ( $fields as $field ) : ?>
					<?php if ( 'file' != $field['type'] ) : ?>
						<dt>
							<strong><?php echo esc_html( $field['label'] ); ?></strong>
						</dt>
						<dd>
							<?php echo is_array( $field['value'] ) ? esc_html( implode( ', ', $field['value'] ) ) : esc_html( $field['value'] ); ?>
						</dd>
					<?php endif; ?>
				<?php endforeach; ?>
			</dl><?php
		endif;
	}
}

/**
 * Adjustes survey responses query to display responses only for selected survey.
 *
 * @action pre_get_posts
 * @global string $typenow The current post type.
 * @param WP_Query $query The survey response query.
 */
function gmr_survey_adjust_survey_responses_query( WP_Query $query ) {
	global $typenow;

	if ( GMR_SURVEY_RESPONSE_CPT == $typenow && 'gmr-survey-responses' == filter_input( INPUT_GET, 'page' ) && $query->is_main_query() ) {
		$survey = filter_input( INPUT_GET, 'survey_id', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
		if ( $survey && ( $survey = get_post( $survey ) ) && GMR_SURVEY_CPT == $survey->post_type ) {
			$query->set( 'post_parent', $survey->ID );
		}
	}
}

/**
 * Survey responses table.
 */
class GMR_Survey_Responses_Table extends WP_Posts_List_Table {

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
		return array();
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

		unset( $columns['title'], $columns['date'] );

		$columns['_gmr_username'] = 'Submitted by';
		$columns['_gmr_email'] = 'Email';
		$columns['_gmr_responses'] = 'Responses';
		$columns['_gmr_submitted'] = 'Submitted on';

		return $columns;
	}

}
