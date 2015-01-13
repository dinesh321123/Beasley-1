<?php

// include list table class file if it hasn't been included yet
require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

// action hooks
add_action( 'admin_menu', 'gmr_contests_register_winners_page' );
add_action( 'admin_action_gmr_promote_winner', 'gmr_contest_promote_winner' );
add_action( 'admin_action_gmr_unpromote_entry', 'gmr_contest_unpromote_entry' );
add_action( 'admin_action_gmr_disqualify_entry', 'gmr_contest_disqualify_entry' );
add_action( 'admin_action_gmr_mark_winner', 'gmr_contest_mark_as_winner' );
add_action( 'admin_action_gmr_unmark_winner', 'gmr_contest_unmark_as_winner' );

// filter hooks
add_filter( 'post_row_actions', 'gmr_contests_add_table_row_actions', 10, 2 );
add_filter( 'parent_file', 'gmr_contests_adjust_winners_page_admin_menu' );

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
	$actions['gmr-contest-winner'] = '<a href="' . esc_url( $link ) . '">Winners</a>';

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
 */
function gmr_contests_render_winner_page() {
	$contest = filter_input( INPUT_GET, 'contest_id', FILTER_VALIDATE_INT );
	if ( ! $contest || ! ( $contest = get_post( $contest ) ) || GMR_CONTEST_CPT != $contest->post_type ) {
		wp_die( 'Contest has not been found.' );
	}

	$entries = filter_input( INPUT_GET, 'entries', FILTER_VALIDATE_INT, array( 'options' => array(
		'min_range' => 1,
		'default'   => 5,
	) ) );

	$gender = strtolower( filter_input( INPUT_GET, 'gender' ) );
	if ( $gender != 'm' && $gender != 'f' ) {
		$gender = null;
	}

	$aged_from = filter_input( INPUT_GET, 'aged_from', FILTER_VALIDATE_INT, array(
		'options' => array(
			'min_range' => 12,
			'max_range' => 55,
			'default'   => null,
		),
	) );

	$aged_to = filter_input( INPUT_GET, 'aged_to', FILTER_VALIDATE_INT, array(
		'options' => array(
			'min_range' => 12,
			'default'   => null,
		),
	) );

	$winners_table = new GMR_Contest_Winners_Table( $contest->ID, 'winner' );
	$winners_table->prepare_items();

	$promoted_table = new GMR_Contest_Winners_Table( $contest->ID, 'promoted' );
	$promoted_table->prepare_items();

	$entries_table = new GMR_Contest_Entries_Table( array(
		'contest_id' => $contest->ID,
		'entries'    => $entries,
		'gender'     => $gender,
		'aged_from'  => $aged_from,
		'aged_to'    => $aged_to,
		'exclude'    => array_merge(
			wp_list_pluck( $winners_table->items, 'ID' ),
			wp_list_pluck( $promoted_table->items, 'ID' )
		),
	) );

	$entries_table->prepare_items();

	?><div id="contest-winner-selection" class="wrap">
		<h2>
			<?php echo esc_html( $contest->post_title ); ?>: Winners
			<a class="add-new-h2" href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . GMR_CONTEST_CPT ) ); ?>">
				Back to Contests
			</a>
		</h2>

		<?php if ( count( $winners_table->items ) > 0 ) : ?>
			<h3>Selected Winners</h3>
			<?php $winners_table->display(); ?>
		<?php endif; ?>

		<?php if ( count( $promoted_table->items ) > 0 ) : ?>
			<h3>Potential Winners</h3>
			<?php $promoted_table->display(); ?>
		<?php endif; ?>

		<h3>Find Entries:</h3>

		<form id="winners-query-form">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>">
			<input type="hidden" name="contest_id" value="<?php echo $contest->ID; ?>">

			<input type="submit" class="button button-primary" value="Select Winner(s)">

			Select 
			<input type="text" name="entries" class="small-text" value="<?php echo esc_attr( $entries ); ?>">
			# of random
			<select name="gender">
				<option value="">Male or Female</option>
				<option value="m"<?php selected( 'm', $gender ); ?>>Male Only</option>
				<option value="f"<?php selected( 'f', $gender ); ?>>Female Only</option>
			</select>
			entries aged from
			<select name="aged_from">
				<option value="">---</option>
				<?php for ( $i = 12; $i <= 55; $i++ ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>"<?php selected( $i, $aged_from ); ?>>
						<?php echo esc_html( $i ); ?>
					</option>
				<?php endfor; ?>
			</select>
			to
			<select name="aged_to">
				<option value="">---</option>
				<?php for ( $i = 12; $i <= 55; $i++ ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>"<?php selected( $i, $aged_to ); ?>>
						<?php echo esc_html( $i ); ?>
					</option>
				<?php endfor; ?>
				<option value="99"<?php selected( $aged_to > 55 ); ?>>56+</option>
			</select>
		</form>

		<?php $entries_table->display(); ?>
	</div><?php
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
 * Marks an entry as winner.
 *
 * @action gmr_mark_winner
 */
function gmr_contest_mark_as_winner() {
	check_admin_referer( 'gmr_mark_winner' );

	$entry = filter_input( INPUT_GET, 'entry_id', FILTER_VALIDATE_INT );
	if ( ! $entry || ! ( $entry = get_post( $entry ) ) || GMR_CONTEST_ENTRY_CPT != $entry->post_type ) {
		wp_die( 'Entry has not been found.' );
	}

	update_post_meta( $entry->ID, 'entry_status', 'winner' );

	wp_redirect( wp_get_referer() );
	exit;
}

/**
 * Unmarks an entry as winner.
 *
 * @action gmr_unmark_winner
 */
function gmr_contest_unmark_as_winner() {
	check_admin_referer( 'gmr_unmark_winner' );

	$entry = filter_input( INPUT_GET, 'entry_id', FILTER_VALIDATE_INT );
	if ( ! $entry || ! ( $entry = get_post( $entry ) ) || GMR_CONTEST_ENTRY_CPT != $entry->post_type ) {
		wp_die( 'Entry has not been found.' );
	}

	delete_post_meta( $entry->ID, 'entry_status' );

	wp_redirect( wp_get_referer() );
	exit;
}

/**
 * Promotes an entry to potential winner status.
 *
 * @action gmr_promote_winner
 */
function gmr_contest_promote_winner() {
	check_admin_referer( 'gmr_promote_winner' );

	$entry = filter_input( INPUT_GET, 'entry_id', FILTER_VALIDATE_INT );
	if ( ! $entry || ! ( $entry = get_post( $entry ) ) || GMR_CONTEST_ENTRY_CPT != $entry->post_type ) {
		wp_die( 'Entry has not been found.' );
	}

	update_post_meta( $entry->ID, 'entry_status', 'promoted' );

	wp_redirect( wp_get_referer() );
	exit;
}

/**
 * Unmarks potential winner entry.
 *
 * @action gmr_unpromote_entry
 */
function gmr_contest_unpromote_entry() {
	check_admin_referer( 'gmr_unpromote_entry' );

	$entry = filter_input( INPUT_GET, 'entry_id', FILTER_VALIDATE_INT );
	if ( ! $entry || ! ( $entry = get_post( $entry ) ) || GMR_CONTEST_ENTRY_CPT != $entry->post_type ) {
		wp_die( 'Entry has not been found.' );
	}

	delete_post_meta( $entry->ID, 'entry_status' );

	wp_redirect( wp_get_referer() );
	exit;
}

/**
 * Disqualifies the entry.
 *
 * @action admin_action_gmr_disqualify_entry
 */
function gmr_contest_disqualify_entry() {
	check_admin_referer( 'gmr_disqualify_entry' );

	$entry = filter_input( INPUT_GET, 'entry_id', FILTER_VALIDATE_INT );
	if ( ! $entry || ! ( $entry = get_post( $entry ) ) || GMR_CONTEST_ENTRY_CPT != $entry->post_type ) {
		wp_die( 'Entry has not been found.' );
	}

	add_filter( 'gmr_prevent_contest_entry_removal', '__return_false' );
	wp_trash_post( $entry->ID );
	remove_filter( 'gmr_prevent_contest_entry_removal', '__return_false' );

	wp_redirect( wp_get_referer() );
	exit;
}

/**
 * Contest entries table.
 */
class GMR_Contest_Entries_Table extends WP_List_Table {

	public function prepare_items() {
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $this->get_columns(), array(), $sortable );

		$args = array(
			'post_type'           => GMR_CONTEST_ENTRY_CPT,
			'post_status'         => 'any',
			'post_parent'         => $this->_args['contest_id'],
			'posts_per_page'      => $this->_args['entries'],
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'orderby'             => 'rand',
			'meta_query'          => array( 'relation' => 'AND' ),
			'post__not_in'        => isset( $this->_args['exclude'] ) ? $this->_args['exclude'] : array(),
		);

		if ( ! empty( $this->_args['gender'] ) ) {
			$args['meta_query'][] = array(
				'key'   => 'entrant_gender',
				'value' => $this->_args['gender'],
			);
		}

		if ( ! empty( $this->_args['aged_from'] ) ) {
			$args['meta_query'][] = array(
				'key'     => 'entrant_birth_year',
				'value'   => date( 'Y' ) - $this->_args['aged_from'],
				'compare' => '<=',
				'type'    => 'NUMERIC',
			);
		}

		if ( ! empty( $this->_args['aged_to'] ) ) {
			$args['meta_query'][] = array(
				'key'     => 'entrant_birth_year',
				'value'   => date( 'Y' ) - $this->_args['aged_to'],
				'compare' => '>=',
				'type'    => 'NUMERIC',
			);
		}

		$query = new WP_Query();
		$this->items = $query->query( $args );
	}

	protected function _get_actions( WP_Post $entry ) {
		$promote = admin_url( 'admin.php?action=gmr_promote_winner&entry_id=' . $entry->ID );
		$promote = wp_nonce_url( $promote, 'gmr_promote_winner' );

		$disqualify = admin_url( 'admin.php?action=gmr_disqualify_entry&entry_id=' . $entry->ID );
		$disqualify = wp_nonce_url( $disqualify, 'gmr_disqualify_entry' );

		return $this->row_actions( array(
			'promote'    => '<a href="' . esc_url( $promote ) . '">Potential Winner</a>',
			'disqualify' => '<a href="' . esc_url( $disqualify ) . '" onclick="return confirm(\'Do you really want to disqualify this entry?\');">Disqualify</a>',
		) );
	}

	public function column_name( WP_Post $entry ) {
		return '<b>' . gmr_contest_get_entry_author( $entry->ID ) . '</b>' . $this->_get_actions( $entry );
	}

	public function column_gender( WP_Post $entry ) {
		$gender = get_post_meta( $entry->ID, 'entrant_gender', true );
		
		if ( 'm' == $gender ) {
			return 'Male';
		}
		
		if ( 'f' == $gender ) {
			return 'Female';
		}

		return '&#8212;';
	}

	public function column_email( WP_Post $entry ) {
		$email = gmr_contest_get_entry_author_email( $entry->ID );
		if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			return '&#8212;';
		}

		return sprintf( '<a href="mailto:%1$s" title="%1$s">%1$s</a>', $email );
	}

	public function column_submitted( WP_Post $entry ) {
		return sprintf(
			'<span title="%s">%s ago</span>',
			mysql2date( 'M j, Y H:i', $entry->post_date ),
			human_time_diff( strtotime( $entry->post_date ), current_time( 'timestamp' ) )
		);
	}

	public function column_default( WP_Post $entry, $column_name ) {
		$fields = GreaterMediaFormbuilderRender::parse_entry( $entry->post_parent, $entry->ID );
		if ( isset( $fields[ $column_name ] ) ) {
			$value = $fields[ $column_name ]['value'];
			if ( 'file' == $fields[ $column_name ]['type'] ) {
				return wp_get_attachment_image( $value, array( 75, 75 ) );
			} elseif ( is_array( $value ) ) {
				return implode( ', ', array_map( 'esc_html', $value ) );
			} else {
				return esc_html( $value );
			}
		}

		return '&#8212;';
	}

	public function get_columns() {
		$columns = array(
			'name'   => 'Name',
			'email'  => 'Email',
			'gender' => 'Gender',
		);

		$form = get_post_meta( $this->_args['contest_id'], 'embedded_form', true );
		if ( ! empty( $form ) ) {
			if ( is_string( $form ) ) {
				$clean_form = trim( $form, '"' );
				$form = json_decode( $clean_form );
			}

			foreach ( $form as $field ) {
				$columns[ $field->cid ] = $field->label;
			}
		}

		$columns['submitted'] = 'Submitted';

		return $columns;
	}

	public function no_items() {
		echo 'No entries found.';
	}

}

/**
 * Contest (potential) winners table.
 */
class GMR_Contest_Winners_Table extends GMR_Contest_Entries_Table {

	public function __construct( $contest_id, $status ) {
		parent::__construct( array(
			'contest_id' => $contest_id,
			'status'     => $status,
			'plural'     => 'contest-winners',
		) );
	}

	protected function _get_actions( WP_Post $entry ) {
		$actions = array();
		if ( 'promoted' == $this->_args['status'] ) {
			$mark_winner = admin_url( 'admin.php?action=gmr_mark_winner&entry_id=' . $entry->ID );
			$mark_winner = wp_nonce_url( $mark_winner, 'gmr_mark_winner' );

			$unpromote = admin_url( 'admin.php?action=gmr_unpromote_entry&entry_id=' . $entry->ID );
			$unpromote = wp_nonce_url( $unpromote, 'gmr_unpromote_entry' );
			
			$actions['mark-winner'] = '<a href="' . esc_url( $mark_winner ) . '">Mark as Winner</a>';
			$actions['unpromote-winner'] = '<a href="' . esc_url( $unpromote ) . '">Unmark as Potential Winner</a>';
		} else {
			$unmark_winner = admin_url( 'admin.php?action=gmr_unmark_winner&entry_id=' . $entry->ID );
			$unmark_winner = wp_nonce_url( $unmark_winner, 'gmr_unmark_winner' );

			$actions['unmark-winner'] = '<a href="' . esc_url( $unmark_winner ) . '">Unmark Winner</a>';
		}

		return $this->row_actions( $actions );
	}

	public function prepare_items() {
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $this->get_columns(), array(), $sortable );

		$args = array(
			'post_type'           => GMR_CONTEST_ENTRY_CPT,
			'post_status'         => 'any',
			'post_parent'         => $this->_args['contest_id'],
			'posts_per_page'      => -1,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'meta_key'            => 'entry_status',
			'meta_value'          => $this->_args['status'],
		);

		$query = new WP_Query();
		$this->items = $query->query( $args );
	}

	public function get_columns() {
		return array(
			'name'      => 'Name',
			'email'     => 'Email',
			'gender'    => 'Gender',
			'submitted' => 'Submitted',
		);
	}

}