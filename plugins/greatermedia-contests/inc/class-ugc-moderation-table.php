<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

if ( ! class_exists( 'WP_List_table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class GreaterMediaUserGeneratedContentModerationTable extends WP_List_Table {

	const PAGE_NAME = 'moderate-ugc';

	/**
	 * Constructor.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @see    WP_List_Table::__construct() for more information on default arguments.
	 *
	 * @param array $args An associative array of arguments.
	 */
	public function __construct( $args = array() ) {

		parent::__construct( array(
			'plural'   => 'submissions',
			'singular' => 'submission',
			'ajax'     => true,
			'screen'   => isset( $args['screen'] ) ? $args['screen'] : null,
		) );

	}


	public static function admin_init() {
		if ( isset( $_REQUEST['page'] ) && self::PAGE_NAME === $_REQUEST['page'] ) {
			add_filter( 'admin_body_class', array( __CLASS__, 'admin_body_class' ) );
		}
	}

	/**
	 * Add a body class identifying pages with this list table
	 *
	 * @param String $classes Existing body classes
	 *
	 * @return String           Modified body classes
	 */
	public static function admin_body_class( $classes ) {
		$classes .= ' listener-submission-moderation-table ';

		return $classes;
	}

	/**
	 * Sanitize & marshall data for displaying the table
	 *
	 * @author Dave Ross <dave.ross@get10up.com>
	 * @return array
	 */
	public function prepare_items() {

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->query = new WP_Query(
			array(
				'post_type'   => GMR_SUBMISSIONS_CPT,
				'post_status' => 'pending',
				'order'       => 'DESC',
				'orderby'     => 'date',
			)
		);

	}

	/**
	 * Get the columns displayed in the table
	 *
	 * @author Dave Ross <dave.ross@get10up.com>
	 * @return array()
	 */
	public function get_columns() {
		$columns = array(
			'actions' => 'Actions',
			'content' => '',
		);

		return $columns;
	}

	/**
	 * Identify columns that can be sorted
	 *
	 * @author Dave Ross <dave.ross@get10up.com>
	 * @return array
	 */
	public function get_sortable_columns() {
		// Nothing is sortable
		$sortable_columns = array();

		return $sortable_columns;
	}

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @return array
	 * @todo make this "protected" again once we're sure this will only run in WP 4.x
	 * PHP allows subclasses to increase the visibility of inherited methods, which is used here as a hack to
	 * allow this WP_List_Table subclass to work on WP 3.9.2 and WP 4.0-beta4
	 */
	public function get_bulk_actions() {

		static $actions;
		if ( ! isset( $actions ) ) {
			$actions = array();

			$actions['approve'] = __( 'Approve' );
			$actions['trash']   = __( 'Move to Trash' );
		}

		return $actions;

	}

	public function display_rows( $posts = array(), $level = 0 ) {

		global $wp_query, $per_page, $mode;

		if ( ! empty( $this->query->posts ) ) {
			foreach ( $this->query->posts as $post ) {
				$this->single_row( $post, $level );
			}
		} else {
			printf( '<tr class="no-items"><td><p>%s</p></td></tr>', __( 'No content needing moderation found.', 'greatermedia_ugc' ) );
		}

	}

	public function single_row( $post, $level = 0 ) {

		global $mode;
		static $alternate;

		$global_post     = get_post();
		$GLOBALS['post'] = $post;
		setup_postdata( $post );

		$edit_link        = get_edit_post_link( $post->ID );
		$title            = _draft_or_post_title();
		$post_type_object = get_post_type_object( $post->post_type );
		$can_edit_post    = current_user_can( 'edit_post', $post->ID );

		$alternate = 'alternate' == $alternate ? '' : 'alternate';
		$classes   = $alternate . ' iedit author-' . ( get_current_user_id() == $post->post_author ? 'self' : 'other' );

		$lock_holder = wp_check_post_lock( $post->ID );
		if ( $lock_holder ) {
			$classes .= ' wp-locked';
			$lock_holder = get_userdata( $lock_holder );
		}

		if ( $post->post_parent ) {
			$count = count( get_post_ancestors( $post->ID ) );
			$classes .= ' level-' . $count;
		} else {
			$classes .= ' level-0';
		}

		$tr_id      = 'post-' . $post->ID;
		$tr_classes = implode( ' ', get_post_class( $classes, $post->ID ) );
		echo sprintf( '<tr id="%s" class="%s" data-ugc-id="%d">', $tr_id, $tr_classes, $post->ID );

		list( $columns, $hidden ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$class = " $column_name column-$column_name";

			switch ( $column_name ) {

				case 'actions':
					include trailingslashit( GREATER_MEDIA_CONTESTS_PATH ) . 'tpl/moderation-table-actions.tpl.php';
					break;
				case 'content':
					$ugc               = GreaterMediaUserGeneratedContent::for_post_id( $post->ID );
					$contest           = $ugc->contest();
					$listener_name     = $ugc->listener_name();
					$listener_gigya_id = $ugc->listener_gigya_id();
					$preview           = $ugc->render_moderation_row();
					include trailingslashit( GREATER_MEDIA_CONTESTS_PATH ) . 'tpl/moderation-table-content.tpl.php';
					break;
			}
		}
		?>
		</tr>
		<?php
		$GLOBALS['post'] = $global_post;
	}

	/**
	 * Build a URL for approving an item of User Generated Content
	 *
	 * @param $post_id
	 *
	 * @return string
	 */
	public function approve_link( $post_id ) {

		$url = home_url( sprintf( 'ugc/%d/approve', intval( $post_id ) ) );

		return $url;

	}
}

add_action( 'admin_init', array( 'GreaterMediaUserGeneratedContentModerationTable', 'admin_init' ) );