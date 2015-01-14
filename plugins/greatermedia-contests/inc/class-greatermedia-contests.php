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
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'pre_get_posts', array( $this, 'adjust_contest_entries_query' ) );
		add_action( 'manage_' . GMR_CONTEST_ENTRY_CPT . '_posts_custom_column', array( $this, 'render_contest_entry_column' ), 10, 2 );
		add_action( 'admin_action_gmr_contest_entry_mark_winner', array( $this, 'mark_contest_winner' ) );
		add_action( 'admin_action_gmr_contest_entry_mark_bulk_winners', array( $this, 'mark_bulk_contest_winner' ) );
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
	 * @param WP_Query $query The contest entry query.
	 */
	public function adjust_contest_entries_query( WP_Query $query ) {
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
					$query->set( 'posts_per_page', 3 );
				}
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

		if ( gmr_contest_has_files( $contest->ID ) ) {
			$columns['_gmr_thumbmail'] = 'Thumbnail';
		}
		
		$columns['_gmr_username'] = 'Submitted by';
		$columns['_gmr_email'] = 'Email';
//		foreach ( $form as $field ) {
//			$columns[ "_gmr_form_{$field->cid}" ] = $field->label;
//		}
		$columns['_gmr_submitted'] = 'Submitted on';

		return $columns;
	}

	/**
	 * Adds contest entry to the winners list.
	 *
	 * @access protected
	 * @param WP_Post $entry The contest entry object.
	 */
	protected function _add_entry_to_winners( $entry ) {
		$gigya_id = get_post_meta( $entry->ID, 'entrant_reference', true );
		add_post_meta( $entry->post_parent, 'winner', "{$entry->ID}:{$gigya_id}" );
	}

	/**
	 * Marks contest winner.
	 */
	public function mark_contest_winner() {
		check_admin_referer( 'contest_entry_mark_winner' );

		$entry = filter_input( INPUT_GET, 'entry', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
		if ( ! $entry || ! ( $entry = get_post( $entry ) ) || GMR_CONTEST_ENTRY_CPT != $entry->post_type ) {
			wp_die( 'Contest entry was not found.' );
		}

		$this->_add_entry_to_winners( $entry );
		
		wp_redirect( wp_get_referer() );
		exit;
	}

	/**
	 * Marks multiple entries as winner.
	 */
	public function mark_bulk_contest_winner() {
		check_admin_referer( 'gmr_contest_entries' );

		$entries = isset( $_GET['post'] ) ? (array) $_GET['post'] : array();
		$entries = array_filter( array_map( 'intval', $entries ) );
		foreach ( $entries as $entry_id ) {
			if ( ! $entry_id || ! ( $entry = get_post( $entry_id ) ) || GMR_CONTEST_ENTRY_CPT != $entry->post_type ) {
				continue;
			}

			$this->_add_entry_to_winners( $entry );
		}

		wp_redirect( wp_get_referer() );
		exit;
	}

	/**
	 * Unmarks contest winner.
	 */
	public function unmark_contest_winner() {
		check_admin_referer( 'contest_entry_unmark_winner' );

		$entry = filter_input( INPUT_GET, 'entry', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
		if ( ! $entry || ! ( $entry = get_post( $entry ) ) || GMR_CONTEST_ENTRY_CPT != $entry->post_type ) {
			wp_die( 'Contest entry was not found.' );
		}

		$gigya_id = get_post_meta( $entry->ID, 'entrant_reference', true );
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
			
		} else {

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

	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'greatermedia-contests-admin', trailingslashit( GREATER_MEDIA_CONTESTS_URL ) . 'css/greatermedia-contests-admin.css' );
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