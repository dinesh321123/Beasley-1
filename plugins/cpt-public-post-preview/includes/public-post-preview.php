<?php
class BBGI_Public_Post_Preview {
	/**
	 * Registers actions and filters.
	 */
	public static function init() {
		add_action( 'transition_post_status', array( __CLASS__, 'unregister_public_preview_on_status_change' ), 20, 3 );
		add_action( 'post_updated', array( __CLASS__, 'unregister_public_preview_on_edit' ), 20, 2 );
		add_filter( 'ppp_nonce_life', array( __CLASS__, 'configured_nounce_life' ) );
		add_action( 'bbgi_register_settings', array( __CLASS__, 'ppp_register_settings' ), 10, 2 );

		if ( ! is_admin() ) {
			add_action( 'pre_get_posts', array( __CLASS__, 'show_public_preview' ) );
			add_filter( 'query_vars', array( __CLASS__, 'add_query_var' ) );
			// Add the query var to WordPress SEO by Yoast whitelist.
			add_filter( 'wpseo_whitelist_permalink_vars', array( __CLASS__, 'add_query_var' ) );
		} else {
			add_action( 'post_submitbox_misc_actions', array( __CLASS__, 'post_submitbox_misc_actions' ) );
			add_action( 'save_post', array( __CLASS__, 'register_public_preview' ), 20, 2 );
			add_action( 'wp_ajax_public-post-preview', array( __CLASS__, 'ajax_register_public_preview' ) );
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_script' ) );
			add_filter( 'display_post_states', array( __CLASS__, 'display_preview_state' ), 20, 2 );
		}
	}

	/**
	 * Registers BBGI Public Post Preview settings.
	 */
	public function ppp_register_settings( $group, $page ) {
		$section_id = 'beasley_ppp';

		add_settings_section( $section_id, 'Public Post Preview', '__return_false', $page );
		add_settings_field( 'bbgi_ppp_hours', 'Expiration hours', 'bbgi_input_field', $page, $section_id, 'name=bbgi_ppp_hours&type=number&desc=Expiration of the preview link in hours, default = 168 Hours' );
		register_setting( $group, 'bbgi_ppp_hours', 'sanitize_text_field' );
	}

	/**
	 * Registers the JavaScript file for post(-new).php.
	 * @param string $hook_suffix Unique page identifier.
	 */
	public static function enqueue_script( $hook_suffix ) {
		if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		if ( get_current_screen()->is_block_editor() ) { } else {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_script(
				'public-post-preview',
				BBGI_PPP_URL . "assets/js/public-post-preview$suffix.js",
				array( 'jquery' ),
				'20190713',
				true
			);

			wp_localize_script(
				'public-post-preview',
				'DSPublicPostPreviewL10n',
				array(
					'enabled'  => __( 'Enabled!', 'public-post-preview' ),
					'disabled' => __( 'Disabled!', 'public-post-preview' ),
				)
			);
		}
	}

	/**
	 * Adds "Public Preview" to the list of display states used in the Posts list table.
	 */
	public static function display_preview_state( $post_states, $post ) {
		if ( in_array( (int) $post->ID, self::get_preview_post_ids(), true ) ) {
			$post_states['ppp_enabled'] = __( 'Public Preview', 'public-post-preview' );
		}

		return $post_states;
	}

	/**
	 * Adds the checkbox to the submit meta box.
	 */
	public static function post_submitbox_misc_actions() {
		$post_types = get_post_types(
			array(
				'public' => true,
			)
		);

		$post = get_post();

		if ( ! in_array( $post->post_type, $post_types, true ) ) {
			return false;
		}

		// Do nothing for auto drafts.
		if ( 'auto-draft' === $post->post_status ) {
			return false;
		}

		// Post is already published.
		if ( in_array( $post->post_status, self::get_published_statuses(), true ) ) {
			return false;
		}

		?>
		<div class="misc-pub-section public-post-preview">
			<?php self::get_checkbox_html( $post ); ?>
		</div>
		<?php

	}

	/**
	 * Returns post statuses which represent a published post.
	 * @return array List with post statuses.
	 */
	private static function get_published_statuses() {
		$published_statuses = array( 'publish', 'private' );

		return apply_filters( 'ppp_published_statuses', $published_statuses );
	}

	/**
	 * Prints the checkbox with the input field for the preview link.
	 * @param WP_Post $post The post object.
	 */
	private static function get_checkbox_html( $post ) {
		if ( empty( $post ) ) {
			$post = get_post();
		}

		wp_nonce_field( 'public-post-preview_' . $post->ID, 'public_post_preview_wpnonce' );

		$enabled = self::is_public_preview_enabled( $post );
		?>
		<label><input type="checkbox"<?php checked( $enabled ); ?> name="public_post_preview" id="public-post-preview" value="1" />
		<?php _e( 'Enable public preview', 'public-post-preview' ); ?> <span id="public-post-preview-ajax"></span></label>

		<div id="public-post-preview-link" style="margin-top:6px"<?php echo $enabled ? '' : ' class="hidden"'; ?>>
			<label>
				<input type="text" name="public_post_preview_link" class="regular-text" value="<?php echo esc_attr( $enabled ? self::get_preview_link( $post ) : '' ); ?>" style="width:99%" readonly />
				<span class="description"><?php _e( 'Copy and share this preview URL.', 'public-post-preview' ); ?></span>
			</label>
		</div>
		<?php
	}

	/**
	 * Checks if a public preview is enabled for a post.
	 * @param WP_Post $post The post object.
	 * @return bool True if a public preview is enabled, false if not.
	 */
	private static function is_public_preview_enabled( $post ) {
		$preview_post_ids = self::get_preview_post_ids();
		return in_array( $post->ID, $preview_post_ids, true );
	}

	/**
	 * Returns the public preview link.
	 *
	 * The link is the home link with these parameters:
	 *  - preview, always true (query var for core)
	 *  - _bbgi, a custom nonce, see BBGI_Public_Post_Preview::create_nonce()
	 *  - page_id or p or p and post_type to specify the post.
	 *
	 * @param WP_Post $post The post object.
	 * @return string The generated public preview link.
	 */
	public static function get_preview_link( $post ) {
		if ( 'page' === $post->post_type ) {
			$args = array(
				'page_id' => $post->ID,
			);
		} elseif ( 'post' === $post->post_type ) {
			$args = array(
				'p' => $post->ID,
			);
		} else {
			$args = array(
				'p'         => $post->ID,
				'post_type' => $post->post_type,
			);
		}

		$args['preview'] = true;
		$args['_bbgi']    = self::create_nonce( 'public_post_preview_' . $post->ID );

		$link = add_query_arg( $args, home_url( '/' ) );

		return apply_filters( 'ppp_preview_link', $link, $post->ID, $post );
	}

	/**
	 * (Un)Registers a post for a public preview.
	 *
	 * Runs when a post is saved, ignores revisions and autosaves.
	 *
	 * @param int    $post_id The post id.
	 * @param object $post    The post object.
	 * @return bool Returns true on a success, false on a failure.
	 */
	public static function register_public_preview( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return false;
		}

		if ( empty( $_POST['public_post_preview_wpnonce'] ) || ! wp_verify_nonce( $_POST['public_post_preview_wpnonce'], 'public-post-preview_' . $post_id ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		$preview_post_ids = self::get_preview_post_ids();
		$preview_post_id  = (int) $post->ID;

		if ( empty( $_POST['public_post_preview'] ) && in_array( $preview_post_id, $preview_post_ids, true ) ) {
			$preview_post_ids = array_diff( $preview_post_ids, (array) $preview_post_id );
		} elseif (
			! empty( $_POST['public_post_preview'] ) &&
			! empty( $_POST['original_post_status'] ) &&
			! in_array( $_POST['original_post_status'], self::get_published_statuses(), true ) &&
			in_array( $post->post_status, self::get_published_statuses(), true )
		) {
			$preview_post_ids = array_diff( $preview_post_ids, (array) $preview_post_id );
		} elseif ( ! empty( $_POST['public_post_preview'] ) && ! in_array( $preview_post_id, $preview_post_ids, true ) ) {
			$preview_post_ids = array_merge( $preview_post_ids, (array) $preview_post_id );
		} else {
			return false; // Nothing has changed.
		}

		return self::set_preview_post_ids( $preview_post_ids );
	}

	/**
	 * Unregisters a post for public preview when a (scheduled) post gets published
	 * or trashed.
	 *
	 * @param string  $new_status New post status.
	 * @param string  $old_status Old post status.
	 * @param WP_Post $post       Post object.
	 * @return bool Returns true on a success, false on a failure.
	 */
	public static function unregister_public_preview_on_status_change( $new_status, $old_status, $post ) {
		$disallowed_status   = self::get_published_statuses();
		$disallowed_status[] = 'trash';

		if ( in_array( $new_status, $disallowed_status, true ) ) {
			return self::unregister_public_preview( $post->ID );
		}

		return false;
	}

	/**
	 * Unregisters a post for public preview when a post gets published or trashed.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @return bool Returns true on a success, false on a failure.
	 */
	public static function unregister_public_preview_on_edit( $post_id, $post ) {
		$disallowed_status   = self::get_published_statuses();
		$disallowed_status[] = 'trash';

		if ( in_array( $post->post_status, $disallowed_status, true ) ) {
			return self::unregister_public_preview( $post_id );
		}

		return false;
	}

	/**
	 * Unregisters a post for public preview.
	 * @param int $post_id Post ID.
	 * @return bool Returns true on a success, false on a failure.
	 */
	private static function unregister_public_preview( $post_id ) {
		$post_id          = (int) $post_id;
		$preview_post_ids = self::get_preview_post_ids();

		if ( ! in_array( $post_id, $preview_post_ids, true ) ) {
			return false;
		}

		$preview_post_ids = array_diff( $preview_post_ids, (array) $post_id );

		return self::set_preview_post_ids( $preview_post_ids );
	}

	/**
	 * (Un)Registers a post for a public preview for an AJAX request.
	 */
	public static function ajax_register_public_preview() {
		if ( ! isset( $_POST['post_ID'], $_POST['checked'] ) ) {
			wp_send_json_error( 'incomplete_data' );
		}

		$preview_post_id = (int) $_POST['post_ID'];
		$checked         = (string) $_POST['checked'];

		check_ajax_referer( 'public-post-preview_' . $preview_post_id );

		$post = get_post( $preview_post_id );

		if ( ! current_user_can( 'edit_post', $preview_post_id ) ) {
			wp_send_json_error( 'cannot_edit' );
		}

		if ( in_array( $post->post_status, self::get_published_statuses(), true ) ) {
			wp_send_json_error( 'invalid_post_status' );
		}

		$preview_post_ids = self::get_preview_post_ids();

		if ( 'false' === $checked && in_array( $preview_post_id, $preview_post_ids, true ) ) {
			$preview_post_ids = array_diff( $preview_post_ids, (array) $preview_post_id );
		} elseif ( 'true' === $checked && ! in_array( $preview_post_id, $preview_post_ids, true ) ) {
			$preview_post_ids = array_merge( $preview_post_ids, (array) $preview_post_id );
		} else {
			wp_send_json_error( 'unknown_status' );
		}

		$ret = self::set_preview_post_ids( $preview_post_ids );

		if ( ! $ret ) {
			wp_send_json_error( 'not_saved' );
		}

		$data = null;
		if ( 'true' === $checked ) {
			$data = array( 'preview_url' => self::get_preview_link( $post ) );
		}

		wp_send_json_success( $data );
	}

	/**
	 * Registers the new query var `_bbgi`.
	 *
	 * @param  array $qv Existing list of query variables.
	 * @return array List of query variables.
	 */
	public static function add_query_var( $qv ) {
		$qv[] = '_bbgi';

		return $qv;
	}

	/**
	 * Registers the filter to handle a public preview.
	 *
	 * Filter will be set if it's the main query, a preview, a singular page
	 * and the query var `_bbgi` exists.
	 * @param object $query The WP_Query object.
	 */
	public static function show_public_preview( $query ) {
		if (
			$query->is_main_query() &&
			$query->is_preview() &&
			$query->is_singular() &&
			$query->get( '_bbgi' )
		) {
			if ( ! headers_sent() ) {
				nocache_headers();
				header( 'X-Robots-Tag: noindex' );
			}
			if ( function_exists( 'wp_robots_no_robots' ) ) { // WordPress 5.7+
				add_filter( 'wp_robots', 'wp_robots_no_robots' );
			} else {
				add_action( 'wp_head', 'wp_no_robots' );
			}

			add_filter( 'posts_results', array( __CLASS__, 'set_post_to_publish' ), 10, 2 );
		}
	}

	/**
	 * Checks if a public preview is available and allowed.
	 * Verifies the nonce and if the post id is registered for a public preview.
	 *
	 * @param int $post_id The post id.
	 * @return bool True if a public preview is allowed, false on a failure.
	 */
	private static function is_public_preview_available( $post_id ) {
		if ( empty( $post_id ) ) {
			return false;
		}

		if ( ! self::verify_nonce( get_query_var( '_bbgi' ), 'public_post_preview_' . $post_id ) ) {
			wp_die( __( 'This link has expired!', 'public-post-preview' ), 403 );
		}

		if ( ! in_array( $post_id, self::get_preview_post_ids(), true ) ) {
			wp_die( __( 'No public preview available!', 'public-post-preview' ), 404 );
		}

		return true;
	}

	/**
	 * Filters the HTML output of individual page number links to use the
	 * preview link.
	 *
	 * @param string $link        The page number HTML output.
	 * @param int    $page_number Page number for paginated posts' page links.
	 * @return string The filtered HTML output.
	 */
	public static function filter_wp_link_pages_link( $link, $page_number ) {
		$post = get_post();
		if ( ! $post ) {
			return $link;
		}

		$preview_link = self::get_preview_link( $post );
		$preview_link = add_query_arg( 'page', $page_number, $preview_link );

		return preg_replace( '~href=(["|\'])(.+?)\1~', 'href=$1' . $preview_link . '$1', $link );
	}

	/**
	 * Sets the post status of the first post to publish, so we don't have to do anything
	 * *too* hacky to get it to load the preview.
	 * @param  array $posts The post to preview.
	 * @return array The post that is being previewed.
	 */
	public static function set_post_to_publish( $posts ) {
		// Remove the filter again, otherwise it will be applied to other queries too.
		remove_filter( 'posts_results', array( __CLASS__, 'set_post_to_publish' ), 10 );

		if ( empty( $posts ) ) {
			return $posts;
		}

		$post_id = (int) $posts[0]->ID;

		// If the post has gone live, redirect to it's proper permalink.
		self::maybe_redirect_to_published_post( $post_id );

		if ( self::is_public_preview_available( $post_id ) ) {
			// Set post status to publish so that it's visible.
			$posts[0]->post_status = 'publish';

			// Disable comments and pings for this post.
			add_filter( 'comments_open', '__return_false' );
			add_filter( 'pings_open', '__return_false' );
			add_filter( 'wp_link_pages_link', array( __CLASS__, 'filter_wp_link_pages_link' ), 10, 2 );
		}

		return $posts;
	}

	/**
	 * Redirects to post's proper permalink, if it has gone live.
	 * @param int $post_id The post id.
	 * @return false False of post status is not a published status.
	 */
	private static function maybe_redirect_to_published_post( $post_id ) {
		if ( ! in_array( get_post_status( $post_id ), self::get_published_statuses(), true ) ) {
			return false;
		}

		wp_safe_redirect( get_permalink( $post_id ), 301 );
		exit;
	}
	public function configured_nounce_life( $nonce_life ) {
		$expiration_hours = (int) get_option( 'bbgi_ppp_hours' );
		if ( $expiration_hours && $expiration_hours > 0 ) {
			return 60 * 60 * $expiration_hours;
		}

		return $nonce_life;
	}

	/**
	 * Get the time-dependent variable for nonce creation.
	 *
	 * @see wp_nonce_tick()
	 * @return int The time-dependent variable.
	 */
	private static function nonce_tick() {
		$nonce_life = apply_filters( 'ppp_nonce_life', 7 * DAY_IN_SECONDS ); // 2 days.

		return ceil( time() / ( $nonce_life / 2 ) );
	}

	/**
	 * Creates a random, one time use token. Without an UID.
	 *
	 * @see wp_create_nonce()
	 *
	 * @param  string|int $action Scalar value to add context to the nonce.
	 * @return string The one use form token.
	 */
	private static function create_nonce( $action = -1 ) {
		$i = self::nonce_tick();

		return substr( wp_hash( $i . $action, 'nonce' ), -12, 10 );
	}

	/**
	 * Verifies that correct nonce was used with time limit. Without an UID.
	 *
	 * @see wp_verify_nonce()
	 *
	 * @param string     $nonce  Nonce that was used in the form to verify.
	 * @param string|int $action Should give context to what is taking place and be the same when nonce was created.
	 * @return bool               Whether the nonce check passed or failed.
	 */
	private static function verify_nonce( $nonce, $action = -1 ) {
		$i = self::nonce_tick();

		// Nonce generated 0-12 hours ago.
		if ( substr( wp_hash( $i . $action, 'nonce' ), -12, 10 ) === $nonce ) {
			return 1;
		}

		// Nonce generated 12-24 hours ago.
		if ( substr( wp_hash( ( $i - 1 ) . $action, 'nonce' ), -12, 10 ) === $nonce ) {
			return 2;
		}

		// Invalid nonce.
		return false;
	}

	/**
	 * Returns the post IDs which are registered for a public preview.
	 * @return array The post IDs. (Empty array if no IDs are registered.)
	 */
	private static function get_preview_post_ids() {
		$post_ids = get_option( 'public_post_preview', array() );
		$post_ids = array_map( 'intval', $post_ids );

		return $post_ids;
	}

	/**
	 * Saves the post IDs which are registered for a public preview.
	 * @param array $post_ids List of post IDs that have a preview.
	 * @return bool Returns true on a success, false on a failure.
	 */
	private static function set_preview_post_ids( $post_ids = array() ) {
		$post_ids = array_map( 'absint', $post_ids );
		$post_ids = array_filter( $post_ids );
		$post_ids = array_unique( $post_ids );

		return update_option( 'public_post_preview', $post_ids );
	}

	/**
	 * Deletes the option 'public_post_preview' if the plugin will be uninstalled.
	 */
	public static function uninstall() {
		delete_option( 'public_post_preview' );
	}
}
// BBGI_Public_Post_Preview::init();
