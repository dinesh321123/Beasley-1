<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

/**
 * Class GreaterMediaContests
 * @see  https://core.trac.wordpress.org/ticket/12668#comment:27
 */
class GreaterMediaContests {

	public function __construct() {
		add_action( 'init', array( $this, 'register_contest_type_taxonomy' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'restrict_manage_posts', array( $this, 'admin_contest_type_filter' ) );
		add_action( 'pre_get_posts', array( $this, 'admin_filter_contest_list' ) );
		add_action( 'pre_get_posts', array( $this, 'adjust_contest_entries_query' ) );
		add_action( 'manage_' . GMR_CONTEST_ENTRY_CPT . '_posts_custom_column', array( $this, 'render_contest_entry_column' ), 10, 2 );
		add_action( 'admin_action_gmr_contest_entry_mark_winner', array( $this, 'mark_contest_winner' ) );
		add_action( 'admin_action_gmr_contest_entry_unmark_winner', array( $this, 'unmark_contest_winner' ) );

		add_filter( 'manage_' . GMR_CONTEST_ENTRY_CPT . '_posts_columns', array( $this, 'filter_contest_entry_columns_list' ) );
		add_filter( 'parent_file', array( $this, 'adjust_current_admin_menu' ) );
		add_filter( 'gmr_live_link_suggestion_post_types', array( $this, 'extend_live_link_suggestion_post_types' ) );
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
	public function adjust_current_admin_menu( $parent_file ) {
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
	 * Adjustes contest entries query to display entries only for selected contest.
	 *
	 * @action pre_get_posts
	 * @global string $typenow The current post type.
	 * @global string $pagenow The current admin page.
	 * @param WP_Query $query The contest entry query.
	 */
	public function adjust_contest_entries_query( WP_Query $query ) {
		global $typenow, $pagenow;

		if ( GMR_CONTEST_ENTRY_CPT == $typenow && 'edit.php' == $pagenow && $query->is_main_query() ) {
			$contest = filter_input( INPUT_GET, 'contest_id', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
			if ( $contest && ( $contest = get_post( $contest ) ) && GMR_CONTEST_CPT == $contest->post_type ) {
				$query->set( 'post_parent', $contest->ID );
			}
		}
	}

	/**
	 * Adds columns to the contest entries table.
	 *
	 * @param array $columns Initial array of columns.
	 * @return array The array of columns.
	 */
	public function filter_contest_entry_columns_list( $columns ) {
		$contest = filter_input( INPUT_GET, 'contest_id', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
		if ( ! $contest || ! ( $contest = get_post( $contest ) ) || GMR_CONTEST_CPT != $contest->post_type ) {
			return $columns;
		}

		$form = get_post_meta( $contest->ID, 'embedded_form', true );
		if ( empty( $form ) ) {
			return $columns;
		}

		if ( is_string( $form ) ) {
			$clean_form = trim( $form, '"' );
			$form = json_decode( $clean_form );
		}

		unset( $columns['title'], $columns['date'] );

		$columns['gigya'] = 'Gigya User';
		foreach ( $form as $field ) {
			$columns[ $field->cid ] = $field->label;
		}
		$columns['submitted'] = 'Submitted';

		return $columns;
	}

	public function mark_contest_winner() {
		check_admin_referer( 'contest_entry_mark_winner' );

		$entry = filter_input( INPUT_GET, 'entry', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
		if ( ! $entry || ! ( $entry = get_post( $entry ) ) || GMR_CONTEST_ENTRY_CPT != $entry->post_type ) {
			wp_die( 'Contest entry was not found.' );
		}

		$gigya_id = get_post_meta( $entry->ID, 'entrant_reference', true );
		if ( empty( $gigya_id ) ) {
			wp_die( 'Gigya user has not been found.' );
		}

		add_post_meta( $entry->post_parent, 'winner', "{$entry->ID}:{$gigya_id}" );

		wp_redirect( wp_get_referer() );
		exit;
	}

	public function unmark_contest_winner() {
		check_admin_referer( 'contest_entry_unmark_winner' );

		$entry = filter_input( INPUT_GET, 'entry', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
		if ( ! $entry || ! ( $entry = get_post( $entry ) ) || GMR_CONTEST_ENTRY_CPT != $entry->post_type ) {
			wp_die( 'Contest entry was not found.' );
		}

		$gigya_id = get_post_meta( $entry->ID, 'entrant_reference', true );
		if ( empty( $gigya_id ) ) {
			wp_die( 'Gigya user has not been found.' );
		}

		delete_post_meta( $entry->post_parent, 'winner', "{$entry->ID}:{$gigya_id}" );
		
		wp_redirect( wp_get_referer() );
		exit;
	}

	/**
	 * Renders custom columns for the contest entries table.
	 *
	 * @param string $column_name The column name which is gonna be rendered.
	 * @param int $post_id The post id.
	 */
	public function render_contest_entry_column( $column_name, $post_id ) {
		$entry = get_post( $post_id );
		
		if ( 'gigya' == $column_name ) {

			$gigya_id = get_post_meta( $post_id, 'entrant_reference', true );
			$winners = get_post_meta( $entry->post_parent, 'winner' );
			$is_winner = in_array( "{$post_id}:{$gigya_id}", $winners );

			echo '<b>';
				echo esc_html( get_post_meta( $post_id, 'entrant_name', true ) );
				if ( $is_winner ) :
					echo ' <span class="dashicons dashicons-awards"></span>';
				endif;
			echo '</b>';

			if ( ! empty( $gigya_id ) ) :
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
							echo '<a href="', esc_url( $action_link ), '">Mark as Winner</a>';
						echo '</span>';
					endif;
				echo '</div>';
			endif;

		} elseif ( 'submitted' == $column_name ) {

			echo mysql2date( 'M j, Y H:i', $entry->post_date );
			
		} else {

			$fields = GreaterMediaFormbuilderRender::parse_entry( $entry->post_parent, $entry->ID );
			if ( isset( $fields[ $column_name ] ) ) {

				$value = $fields[ $column_name ]['value'];
				if ( 'file' == $fields[ $column_name ]['type'] ) {
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
	 * Register a custom taxonomy representing Contest Types
	 * @uses register_taxonomy
	 */
	public function register_contest_type_taxonomy() {
		$labels = array(
			'name'                       => 'Contest Types',
			'singular_name'              => 'Contest Type',
			'menu_name'                  => 'Contest Type',
			'all_items'                  => 'All Contest Types',
			'parent_item'                => 'Parent Contest Type',
			'parent_item_colon'          => 'Parent Contest Type:',
			'new_item_name'              => 'New Contest Type Name',
			'add_new_item'               => 'Add New Contest Type',
			'edit_item'                  => 'Edit Contest Type',
			'update_item'                => 'Update Contest Type',
			'separate_items_with_commas' => 'Separate items with commas',
			'search_items'               => 'Search Contest Types',
			'add_or_remove_items'        => 'Add or remove contest types',
			'choose_from_most_used'      => 'Choose from the most used contest types',
			'not_found'                  => 'Not Found',
		);

		$args = array(
			'labels'            => $labels,
			// The data isn't hierarchical. This is just to make WP display checkboxes instead of free-form text entry
			'hierarchical'      => true,
			'public'            => false,
			'show_ui'           => true,
			'show_admin_column' => false,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
		);

		register_taxonomy( 'contest_type', array( GMR_CONTEST_CPT ), $args );

		$this->maybe_seed_contest_type_taxonomy();
	}

	/**
	 * Populate the initial records in the Contest Type taxonomy
	 *
	 * @uses wp_insert_term
	 * @uses get_option
	 * @uses set_option
	 */
	public function maybe_seed_contest_type_taxonomy() {
		$seeded = get_option( 'contest_type_seeded', false );
		if ( $seeded ) {
			return;
		}

		wp_insert_term( 'On Air', 'contest_type', array( 'description' => 'On-air contests generally require a call or, perhaps, text message, from the entrant. The specific requirements and number to text or call can be written directly in the "how to enter" section of the contest.' ) );
		wp_insert_term( 'Online', 'contest_type', array( 'description' => '' ) );

		update_option( 'contest_type_seeded', 1 );

		if ( class_exists( 'GreaterMediaAdminNotifier' ) ) {
			GreaterMediaAdminNotifier::message( __( 'Seeded "Contest Types" taxonomy.', 'greatermedia_contests' ) );
		}
	}

	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'greatermedia-contests-admin', trailingslashit( GREATER_MEDIA_CONTESTS_URL ) . 'css/greatermedia-contests-admin.css' );
	}

	/**
	 * Add a dropdown on the contest list page to filter by contest type.
	 */
	public function admin_contest_type_filter() {
		global $typenow;
		$contest_type_tax_id = 0;

		if ( GMR_CONTEST_CPT !== $typenow || ! is_admin() ) {
			return;
		}

		if ( isset( $_GET['type_filter'] ) ) {
			// If user selected a term in the filter drop-down on the contest list page
			$contest_type_tax_id = intval( $_GET['type_filter'] );
		} else if ( isset( $_GET['contest_type'] ) ) {
			// If user clicked on the post count next to the taxonomy term
			$term = get_term_by( 'slug', $_GET['contest_type'], 'contest_type' );

			if ( false !== $term ) {
				$contest_type_tax_id = intval( $term->term_id );
			}
		}

		$args = array(
			'show_option_all' => __( 'All contest types', 'greatermedia_contests' ),
			'hierarchical'    => true,
			'name'            => 'type_filter',
			'id'              => 'type-filter',
			'class'           => 'postform',
			'orderby'         => 'name',
			'taxonomy'        => 'contest_type',
			'hide_if_empty'   => true,
			'selected'        => $contest_type_tax_id,
		);

		wp_dropdown_categories( $args );
	}

	/**
	 * Handle the request to filter contests by type.
	 *
	 * @param  WP_Query $wp_query
	 */
	public function admin_filter_contest_list( $wp_query ) {
		global $typenow;

		$contest_type_tax_id = isset( $_GET['type_filter'] ) ? intval( $_GET['type_filter'] ) : 0;

		if ( GMR_CONTEST_CPT !== $typenow || ! is_admin() || empty( $contest_type_tax_id ) ) {
			return;
		}

		$args = array(
			array(
				'taxonomy' => 'contest_type',
				'field'    => 'id',
				'terms'    => $contest_type_tax_id,
			),
		);

		$wp_query->set( 'tax_query', $args );
	}

	/**
	 * Extends live link suggestion post types.
	 *
	 * @static
	 * @access public
	 *
	 * @param array $post_types The array of already registered post types.
	 *
	 * @return array The array of extended post types.
	 */
	public function extend_live_link_suggestion_post_types( $post_types ) {
		$post_types[] = GMR_CONTEST_CPT;
		return $post_types;
	}

}

$GreaterMediaContests = new GreaterMediaContests();