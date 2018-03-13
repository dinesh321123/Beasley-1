<?php

class GMR_Archive_Admin {

	/**
	 * Setups class hooks.
	 *
	 * @access public
	 */
	public function setup() {
		add_action( 'beasley-register-settings', array( $this, 'init_setting' ), 10, 2 );
		add_filter( 'the_title', array( $this, 'the_title' ), 10, 2 );
		add_filter( 'display_post_states', array( $this, 'display_post_states' ), 10, 2 );
		add_action( 'admin_footer-post.php', array( $this, 'add_post_status_to_dropdown' ) );
		add_action( 'post_row_actions', array( $this, 'post_row_actions' ), 10, 2 );
		add_action( 'post_action_archive_post', array( $this, 'archive_post' ) );
		add_action( 'post_action_unarchive_post', array( $this, 'unarchive_post' ) );
		add_action( 'post_submitbox_misc_actions', array( $this, 'post_submitbox_misc_actions' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
	}

	function init_setting( $group, $page ) {
		add_settings_section( 'greatermedia_auto_archive_settings', 'Auto Archive Content', array( $this, 'render_section' ), $page );
		register_setting( $group, 'content_auto_archive_days', array( $this, 'sanitize_days' ) );
	}

	/**
	 * Sanitize days and schedule cron event
	 *
	 * @param $days
	 *
	 * @return int
	 */
	function sanitize_days( $days ) {
		$days      = absint( $days );
		$timestamp = wp_next_scheduled( GMR_AUTO_ARCHIVE_CRON );

		if ( $days ) {
			if ( ! $timestamp ) {
				$timestamp = current_time( 'timestamp', 1 ) + DAY_IN_SECONDS;
				wp_schedule_event( $timestamp, 'daily', GMR_AUTO_ARCHIVE_CRON );
			}
		} else {
			if ( $timestamp ) {
				wp_unschedule_event( $timestamp, GMR_AUTO_ARCHIVE_CRON );
			}
		}

		return $days;
	}

	/**
	 * Render setting section
	 */
	function render_section() {
		$days = get_option( GMR_AUTO_ARCHIVE_OPTION_NAME, 0 );
		?>
		<div class="gmr__option">
			<label for="<?php echo esc_attr( GMR_AUTO_ARCHIVE_OPTION_NAME ); ?>" class="gmr__option--label">Archive
				Days </label>
			<input type="number" min="0" class="" name="<?php echo esc_attr( GMR_AUTO_ARCHIVE_OPTION_NAME ); ?>"
				   id="<?php echo esc_attr( GMR_AUTO_ARCHIVE_OPTION_NAME ); ?>"
				   value="<?php echo esc_attr( $days ); ?>"/>
			<div class="gmr-option__field--desc">Number of days after which post will be marked as archived. 0 - Never
			</div>
		</div>
		<?php
	}

	/**
	 * Filter Archived post titles on the frontend.
	 *
	 * @param  string $title
	 * @param  int $post_id (optional)
	 *
	 * @return string
	 */
	function the_title( $title, $post_id = null ) {

		$post = get_post( $post_id );

		if (
			! is_admin()
			&&
			isset( $post->post_status )
			&&
			GMR_AUTO_ARCHIVE_POST_STATUS === $post->post_status
		) {

			$title = sprintf( '%s: %s', __( 'Archived', 'caas' ), $title );

		}

		return $title;

	}

	/**
	 * Add archive status in post
	 */
	function add_post_status_to_dropdown() {
		global $post;
		$complete = '';
		$label    = '';
		if ( $post->post_type == 'post' ) {
			if ( $post->post_status == GMR_AUTO_ARCHIVE_POST_STATUS ) {
				$complete = ' selected=\"selected\"';
				$label    = '<span id="post-status-display"> ' . esc_html__( 'Archived', 'greatermedia' ) . '</span>';
			}
			?>
			<script>
				jQuery( document ).ready( function ( $ ) {
					$( "select#post_status" ).append( '<option value="archive" <?php echo $complete; ?> ><?php esc_html_e( 'Archived', 'greatermedia' ); ?> </option>' );
					$( ".misc-pub-section label" ).append( '<?php echo $label; ?>' );
				} );
			</script>
			<?php
		}
	}


	/**
	 * Display custom post state text next to post titles that are Archived.
	 *
	 * @filter display_post_states
	 *
	 * @param  array $post_states
	 * @param  WP_Post $post
	 *
	 * @return array
	 */
	function display_post_states( $post_states, $post ) {

		if ( GMR_AUTO_ARCHIVE_POST_STATUS !== $post->post_status ) {
			return $post_states;
		}

		return array_merge(
			$post_states,
			array(
				GMR_AUTO_ARCHIVE_POST_STATUS => __( 'Archived', 'greatermedia' ),
			)
		);

	}

	/**
	 * Add create draft in post list actions
	 *
	 * @param $actions Existing actions
	 * @param $post WP_Post object
	 *
	 * @return array if required it will add create draft action in existing actions
	 */
	function post_row_actions( $actions, $post ) {

		if ( current_user_can( 'delete_posts', $post->ID ) ) {
			$title = _draft_or_post_title();
			unset( $actions['delete'] );
			$post_type_object = get_post_type_object( $post->post_type );
			if ( 'archive' === $post->post_status ) {
				unset( $actions['archive'] );
				unset( $actions['edit'] );
				unset( $actions['trash'] );
				unset( $actions['inline hide-if-no-js'] );

				$archive_link = add_query_arg( array(
					'action' => 'unarchive_post',
					'nonce'  => wp_create_nonce( 'unarchive-post_' . $post->ID )
				), admin_url( sprintf( $post_type_object->_edit_link, $post->ID ) ) );


				$actions['unarchive'] = sprintf(
					'<a href="%s" class="submitdelete" aria-label="%s">%s</a>',
					esc_url( $archive_link ),
					/* translators: %s: post title */
					esc_attr( sprintf( __( 'Restore &#8220;%s&#8221;' ), $title ) ),
					_x( 'Restore', 'verb' )
				);
			} else {
				$archive_link = add_query_arg( array(
					'action' => 'archive_post',
					'nonce'  => wp_create_nonce( 'archive-post_' . $post->ID )
				), admin_url( sprintf( $post_type_object->_edit_link, $post->ID ) ) );

				$actions['archive'] = sprintf(
					'<a href="%s" class="submitdelete" aria-label="%s">%s</a>',
					esc_url( $archive_link ),
					/* translators: %s: post title */
					esc_attr( sprintf( __( 'Archive &#8220;%s&#8221;' ), $title ) ),
					_x( 'Archive', 'verb' )
				);
			}
		}

		return $actions;
	}

	/**
	 * Handle archive post action
	 *
	 * @param $post_id
	 */
	function archive_post( $post_id ) {
		if ( ! current_user_can( 'delete_posts', $post_id ) ) {
			return;
		}
		check_admin_referer( 'archive-post_' . $post_id, 'nonce' );
		wp_update_post(
			array(
				'ID'          => $post_id,
				'post_status' => GMR_AUTO_ARCHIVE_POST_STATUS
			)
		);
		delete_post_meta( $post_id, '_unarchived' );
	}

	/**
	 * Handle archive post action
	 *
	 * @param $post_id
	 */
	function unarchive_post( $post_id ) {
		if ( ! current_user_can( 'delete_posts', $post_id ) ) {
			return;
		}
		check_admin_referer( 'unarchive-post_' . $post_id, 'nonce' );
		wp_update_post(
			array(
				'ID'          => $post_id,
				'post_status' => 'draft'
			)
		);
		update_post_meta( $post_id, '_exclude_auto_archive', 1 );
	}

	/**
	 * Added Do not auto archive metabox
	 */
	function post_submitbox_misc_actions() {
		$post_id = get_the_ID();

		if ( get_post_type( $post_id ) != 'post' ) {
			return;
		}

		$value = get_post_meta( $post_id, '_exclude_auto_archive', true );
		wp_nonce_field( 'do_not_archive_' . $post_id, 'auto_archive_nonce' );
		?>
		<div id="content-auto-archive-meta-fields">
			<div class="misc-pub-section">
				<input id="_exclude_auto_archive" type="checkbox" value="1" <?php checked( $value, true, true ); ?>
					   name="_exclude_auto_archive"/> <label
						for="_exclude_auto_archive"><?php esc_html_e( 'Do Not Auto Archive', 'greatermedia' ); ?></label>
			</div>
		</div>
		<?php
	}

	/**
	 * Add / Delete meta omn save post
	 *
	 * @param $post_id
	 */
	function save_post( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if (
			! isset( $_POST['auto_archive_nonce'] ) ||
			! wp_verify_nonce( $_POST['auto_archive_nonce'], 'do_not_archive_' . $post_id )
		) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( isset( $_POST['_exclude_auto_archive'] ) ) {
			update_post_meta( $post_id, '_exclude_auto_archive', absint( $_POST['_exclude_auto_archive'] ) );
		} else {
			delete_post_meta( $post_id, '_exclude_auto_archive' );
		}
	}
}