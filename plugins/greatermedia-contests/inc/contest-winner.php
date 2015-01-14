<?php

// include list table class files if it hasn't been included yet
require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-posts-list-table.php';

// action hooks
add_action( 'admin_menu', 'gmr_contests_register_winners_page' );
add_action( 'admin_action_gmr_promote_winner', 'gmr_contest_promote_winner' );
add_action( 'admin_action_gmr_unpromote_entry', 'gmr_contest_unpromote_entry' );
add_action( 'admin_action_gmr_disqualify_entry', 'gmr_contest_disqualify_entry' );
add_action( 'admin_action_gmr_mark_winner', 'gmr_contest_mark_as_winner' );
add_action( 'admin_action_gmr_unmark_winner', 'gmr_contest_unmark_as_winner' );
add_action( 'post_submitbox_start', 'gmr_contest_view_entries_link' );

// filter hooks
add_filter( 'post_row_actions', 'gmr_contests_add_table_row_actions', 10, 2 );
add_filter( 'parent_file', 'gmr_contests_adjust_winners_page_admin_menu' );

/**
 * Renders link to access contest entries.
 */
function gmr_contest_view_entries_link() {
	$post = get_post();
	
	echo '<div id="contest-entries">';
		echo '<a href="', admin_url( 'admin.php?page=gmr-contest-winner&contest_id=' . $post->ID ), '">View Entries</a>';
	echo '</div>';
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

	// fake post type to make standard WP_Posts_List_Table class working properly
	$_GET['post_type'] = GMR_CONTEST_ENTRY_CPT;

	// override globals to make posts table class working properly
	$post_type = $typenow = GMR_CONTEST_ENTRY_CPT;
	$post_type_object = get_post_type_object( $post_type );

	// create table class
	$wp_list_table = new GMR_Contest_Entries_Table( array( 'screen' => GMR_CONTEST_ENTRY_CPT, 'plural' => 'entry_id' ) );
	$wp_list_table->prepare_items();

	// winners table
	$winners = new GMR_Contest_Winners_Table( array( 'contest_id' => $contest->ID ) );
	$winners->prepare_items();

	?><div id="contest-winner-selection" class="wrap">
		<h2>
			Entries:
			<a href="<?php echo get_edit_post_link( $contest->ID ); ?>"><?php echo esc_html( $contest->post_title ); ?></a>
			<a class="add-new-h2" href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . GMR_CONTEST_CPT ) ); ?>">All Contests</a>
		</h2>

		<?php if ( $winners->has_items() ) : ?>
			<h3>Selected Winners</h3>
			<?php $winners->display(); ?>

			<h3>All Entries</h3>
		<?php endif; ?>

		<form id="posts-filter">
			<?php wp_nonce_field( 'gmr_contest_entries' ) ?>
			<?php $wp_list_table->display(); ?>
		</form>
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
	 * @param string $which The area where to render extra navigation.
	 */
	protected function extra_tablenav( $which ) {
		$random = filter_input( INPUT_GET, 'random', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
		
		if ( $which == 'top' ) :
			?><div class="alignleft actions">
				<a class="button button-primary" href="<?php echo esc_url( add_query_arg( 'random', 3 ) ); ?>">Load 3 Random Entries</a>
				<?php if ( $random ) : ?>
					<a class="button" href="<?php echo esc_url( add_query_arg( 'random', false ) ); ?>">See All Entries</a>
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