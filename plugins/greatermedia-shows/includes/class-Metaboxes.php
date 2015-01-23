<?php

/**
 * Created by Eduard
 * Date: 15.10.2014
 */
class GMR_Show_Metaboxes {

	private $_restricted_posts = null;

	/**
	 * Constructor.
	 *
	 * @access public
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'post_submitbox_misc_actions', array( $this, 'post_submitbox_misc_actions' ) );
		add_action( 'save_post', array( $this, 'save_box' ) );

		add_action( 'show_user_profile', array( $this, 'admin_user_meta_fields' ), 10, 1 );
		add_action( 'edit_user_profile', array( $this, 'admin_user_meta_fields' ), 10, 1 );

		add_action( 'personal_options_update', array( $this, 'admin_save_user_meta_fields' ), 10, 1 );
		add_action( 'edit_user_profile_update', array( $this, 'admin_save_user_meta_fields' ), 10, 1 );
		add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );
	}

	/**
	 * Enqueues necessary scripts and styles.
	 *
	 * @action admin_enqueue_scripts
	 * @access public
	 */
	public function admin_enqueue_scripts() {
		global $pagenow, $typenow;
		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
		$post_types = ShowsCPT::get_supported_post_types();

		if ( in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) && $typenow == ShowsCPT::SHOW_CPT ) {
			wp_enqueue_script( 'meta_box', GMEDIA_SHOWS_URL . "assets/js/greatermedia_shows{$postfix}.js", array( 'jquery', 'jquery-ui-datepicker' ), GMEDIA_SHOWS_VERSION, true );
			wp_enqueue_style( 'meta_box', GMEDIA_SHOWS_URL . "assets/css/greatermedia_shows{$postfix}.css", array(), GMEDIA_SHOWS_VERSION );
		}

		// Add support for auto-selecting a user's show ONLY when creting a new post.
		if ( in_array( get_post_type(), (array) $post_types ) && 'post-new.php' === $pagenow ) {
			$term_ids = array();
			$show_tt_id = get_user_option( 'show_tt_id', get_current_user_id() );
			$current_show_term = get_term_by( 'term_taxonomy_id', $show_tt_id, ShowsCPT::SHOW_TAXONOMY );

			if ( false !== $current_show_term ) {
				$term_ids[] = $current_show_term->term_id;
			}

			wp_register_script( 'admin_show_selector', GMEDIA_SHOWS_URL . "assets/js/admin_show_selector{$postfix}.js", array( 'jquery'), GMEDIA_SHOWS_VERSION, true );

			wp_localize_script( 'admin_show_selector', 'SHOW_JS', array(
				'usersShow' => $term_ids,
			));

			wp_enqueue_script( 'admin_show_selector' );
		}
	}

	/**
	 * Adds meta boxes to show edit page.
	 *
	 * @action add_meta_boxes
	 * @access public
	 */
	public function add_meta_boxes() {
		add_meta_box( 'show_logo', 'Logo', array( $this, 'render_logo_meta_box' ), ShowsCPT::SHOW_CPT, 'side' );

		add_meta_box( 'show_featured', 'Featured', array( $this, 'render_featured_meta_box' ), ShowsCPT::SHOW_CPT, 'advanced', 'high' );
		add_meta_box( 'show_favorites', 'Favorites', array( $this, 'render_favorites_meta_box' ), ShowsCPT::SHOW_CPT, 'advanced', 'high' );
		add_meta_box( 'show_time', 'Show Times', array( $this, 'render_show_times_meta_box' ), ShowsCPT::SHOW_CPT, 'side' );
		add_meta_box( 'show_social_pages', 'Social Pages', array( $this, 'render_social_pages_meta_box' ), ShowsCPT::SHOW_CPT, 'advanced' );
	}

	/**
	 * Displays show settings.
	 *
	 * @action post_submitbox_misc_actions
	 * @access public
	 */
	public function post_submitbox_misc_actions() {
		global $typenow;
		if ( ShowsCPT::SHOW_CPT != $typenow ) {
			return;
		}

		wp_nonce_field( 'gmr_show', 'show_nonce', false );

		$has_homepage = \GreaterMedia\Shows\supports_homepage( get_the_ID() );
		$supports_galleries = \GreaterMedia\Shows\supports_galleries( get_the_ID() );
		$supports_podcasts = \GreaterMedia\Shows\supports_podcasts( get_the_ID() );
		$supports_videos = \GreaterMedia\Shows\supports_videos( get_the_ID() );

		?><div id="show-homepage" class="misc-pub-section misc-pub-gmr mis-pub-radio">
			Has home page:
			<span class="post-pub-section-value radio-value"><?php echo $has_homepage ? 'Yes' : 'No' ?></span>
			<a href="#" class="edit-radio hide-if-no-js" style="display: inline;"><span aria-hidden="true">Edit</span></a>

			<div class="radio-select hide-if-js">
				<label><input type="radio" name="show_homepage" value="0"<?php checked( $has_homepage, false ) ?>> No</label><br>
				<label><input type="radio" name="show_homepage" value="1"<?php checked( $has_homepage, true ) ?>> Yes</label><br>

				<p>
					<a href="#" class="save-radio hide-if-no-js button"><?php esc_html_e( 'OK' ) ?></a>
					<a href="#" class="cancel-radio hide-if-no-js button-cancel"><?php esc_html_e( 'Cancel' ) ?></a>
				</p>
			</div>
		</div>

		<div id="show-homepage-supports-galleries" class="misc-pub-section misc-pub-gmr mis-pub-radio">
			Supports Galleries:
			<span class="post-pub-section-value radio-value"><?php echo $supports_galleries ? 'Yes' : 'No'; ?></span>
			<a href="#" class="edit-radio hide-if-no-js" style="display: inline;"><span aria-hidden="true">Edit</span></a>

			<div class="radio-select hide-if-js">
				<label for="show-homepage-supports-galleries-no"><input type="radio" name="show_homepage_galleries" id="show-homepage-supports-galleries-no" value="0"<?php checked( $supports_galleries, false ) ?>> No</label><br>
				<label for="show-homepage-supports-galleries-yes"><input type="radio" name="show_homepage_galleries" id="show-homepage-supports-galleries-yes" value="1"<?php checked( $supports_galleries, true ) ?>> Yes</label><br>

				<p>
					<a href="#" class="save-radio hide-if-no-js button"><?php esc_html_e( 'OK' ) ?></a>
					<a href="#" class="cancel-radio hide-if-no-js button-cancel"><?php esc_html_e( 'Cancel' ) ?></a>
				</p>
			</div>
		</div>

		<div id="show-homepage-supports-podcasts" class="misc-pub-section misc-pub-gmr mis-pub-radio">
			Supports Podcasts:
			<span class="post-pub-section-value radio-value"><?php echo $supports_podcasts ? 'Yes' : 'No'; ?></span>
			<a href="#" class="edit-radio hide-if-no-js" style="display: inline;"><span aria-hidden="true">Edit</span></a>

			<div class="radio-select hide-if-js">
				<label for="show-homepage-supports-podcasts-no"><input type="radio" name="show_homepage_podcasts" id="show-homepage-supports-podcasts-no" value="0"<?php checked( $supports_podcasts, false ) ?>> No</label><br>
				<label for="show-homepage-supports-podcasts-yes"><input type="radio" name="show_homepage_podcasts" id="show-homepage-supports-podcasts-yes" value="1"<?php checked( $supports_podcasts, true ) ?>> Yes</label><br>

				<p>
					<a href="#" class="save-radio hide-if-no-js button"><?php esc_html_e( 'OK' ) ?></a>
					<a href="#" class="cancel-radio hide-if-no-js button-cancel"><?php esc_html_e( 'Cancel' ) ?></a>
				</p>
			</div>
		</div>

		<div id="show-homepage-supports-videos" class="misc-pub-section misc-pub-gmr mis-pub-radio">
			Supports Videos:
			<span class="post-pub-section-value radio-value"><?php echo $supports_videos ? 'Yes' : 'No'; ?></span>
			<a href="#" class="edit-radio hide-if-no-js" style="display: inline;"><span aria-hidden="true">Edit</span></a>

			<div class="radio-select hide-if-js">
				<label for="show-homepage-supports-videos-no"><input type="radio" name="show_homepage_videos" id="show-homepage-supports-videos-no" value="0"<?php checked( $supports_videos, false ) ?>> No</label><br>
				<label for="show-homepage-supports-videos-yes"><input type="radio" name="show_homepage_videos" id="show-homepage-supports-videos-yes" value="1"<?php checked( $supports_videos, true ) ?>> Yes</label><br>

				<p>
					<a href="#" class="save-radio hide-if-no-js button"><?php esc_html_e( 'OK' ) ?></a>
					<a href="#" class="cancel-radio hide-if-no-js button-cancel"><?php esc_html_e( 'Cancel' ) ?></a>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Outputs the logo meta box.
	 *
	 * @access public
	 */
	public function render_logo_meta_box( WP_Post $post ) {
		$image = '';
		$image_id = intval( get_post_meta( $post->ID, 'logo_image', true ) );
		if ( $image_id ) {
			$image = current( (array) wp_get_attachment_image_src( $image_id, 'medium' ) );
		}

		echo '<input name="logo_image" type="hidden" class="meta_box_upload_image" value="', $image_id, '">';
		echo '<img src="', esc_attr( $image ), '" class="meta_box_preview_image">';
		echo '<div style="text-align:center">';
			echo '<a href="#" class="meta_box_upload_image_button button button-primary" rel="', $post->ID, '">Choose Image</a> ';
			echo '<a href="#" class="meta_box_clear_image_button button">Remove Image</a>';
		echo '</div>';
	}

	private function _get_restricted_post_ids() {
		if ( is_null( $this->_restricted_posts ) ) {
			$query = new \WP_Query();
			$this->_restricted_posts = $query->query( array(
				'post_type'           => 'any',
				'post_status'         => 'any',
				'posts_per_page'      => 50,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'fields'              => 'ids',
				'meta_query'          => array(
					'relation' => 'OR',
					array(
						'key'     => 'post_age_restriction',
						'compare' => 'EXISTS',
					),
					array(
						'key'     => 'post_login_restriction',
						'compare' => 'EXISTS',
					),
				),
			) );
		}

		return $this->_restricted_posts;
	}

	public function render_featured_meta_box( WP_Post $post ) {
		if ( ! function_exists( 'pf_render' ) ) {
			?><p>Please install the <a href="http://github.com/10up/post-finder">"post-finder"</a> plugin.</p><?php
			return;
		}
		$featured_posts = get_post_meta( $post->ID, 'gmr_featured_post_ids', true );

		$options = array(
			'args' => array(
				'post_type' => array( 'post', 'tribe_events' ),
				'meta_key'  => '_thumbnail_id',
				'exclude'   => $this->_get_restricted_post_ids(),
			),
			'limit' => 3,
		);

		?>
		<p>These items require featured images. If an item is not present, make sure a featured image is assigned.</p>
		<?php
		pf_render( 'gmr-featured-post-ids', $featured_posts, $options );
	}

	public function render_favorites_meta_box( WP_Post $post ) {
		if ( ! function_exists( 'pf_render' ) ) {
			?><p>Please install the <a href="http://github.com/10up/post-finder">"post-finder"</a> plugin.</p><?php
			return;
		}

		$favorite_posts = get_post_meta( $post->ID, 'gmr_favorite_post_ids', true );

		$options = array(
			'args' => array(
				'post_type' => array( 'post' ),
				'meta_key'  => '_thumbnail_id',
				'exclude'   => $this->_get_restricted_post_ids(),
			),
			'limit' => 10,
		);

		?>
		<p>These items require featured images. If an item is not present, make sure a featured image is assigned.</p>
		<?php
		pf_render( 'gmr-favorite-post-ids', $favorite_posts, $options );
	}

	/**
	 * Render a meta box to enter a show times string for use throughout the site
	 * @param $post WP_Post
	 */
	public function render_show_times_meta_box( $post ) {
		$show_days = get_post_meta( $post->ID, 'show_days', true );
		$show_times = get_post_meta( $post->ID, 'show_times', true );
		?>
		<table class="form-table">
			<tr>
				<td><label>Days</label></td>
				<td>
					<input type="text" name="show_days" class="widefat" value="<?php echo esc_attr( $show_days ); ?>" placeholder="Weekdays">
				</td>
			</tr>
			<tr>
				<td><label>Times</label></td>
				<td>
					<input type="text" name="show_times" class="widefat" value="<?php echo esc_attr( $show_times ); ?>" placeholder="5:30am - 10:30am">
				</td>
			</tr>
		</table>

			<p class="description">
			A simple description for when this show is on air. Used alongside show titles. Independent from the official show schedule.
		</p>
		<?php
	}
	
	/**
	 * Render a meta box to enter a social page links.
	 * @param $post WP_Post
	 */
	public function render_social_pages_meta_box( $post ) {				
		$facebook = get_post_meta( $post->ID, 'show/social_pages/facebook', true ); 
		$twitter = get_post_meta( $post->ID, 'show/social_pages/twitter', true );
		$google = get_post_meta( $post->ID, 'show/social_pages/google', true ); 
		?>
		<table class="form-table">
			<tr>
				<td><label>Facebook URL</label></td>
				<td>
					<input type="text" name="show/social_pages/facebook" class="widefat" value="<?php echo esc_attr( $facebook ); ?>">
				</td>
			</tr>
				<td><label>Twitter URL</label></td>
				<td>
					<input type="text" name="show/social_pages/twitter" class="widefat" value="<?php echo esc_attr( $twitter ); ?>">
				</td>
			</tr>
				<td><label>Google+ URL</label></td>
				<td>
					<input type="text" name="show/social_pages/google" class="widefat" value="<?php echo esc_attr( $google ); ?>">
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Saves the captured data.
	 *
	 * @action save_post
	 * @access public
	 */
	public function save_box( $post_id ) {
		$doing_autosave = defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;
		$valid_nonce = wp_verify_nonce( filter_input( INPUT_POST, 'show_nonce' ), 'gmr_show' );
		$can_edit_post = current_user_can( 'edit_page', $post_id );
		if ( $doing_autosave || ! $valid_nonce || ! $can_edit_post ) {
			return;
		}

		$homepage_support = filter_input( INPUT_POST, 'show_homepage', FILTER_VALIDATE_BOOLEAN );
		$gallery_support = filter_input( INPUT_POST, 'show_homepage_galleries', FILTER_VALIDATE_BOOLEAN );
		$podcast_support = filter_input( INPUT_POST, 'show_homepage_podcasts', FILTER_VALIDATE_BOOLEAN );
		$video_support = filter_input( INPUT_POST, 'show_homepage_videos', FILTER_VALIDATE_BOOLEAN );

		update_post_meta( $post_id, 'show_homepage', $homepage_support );

		if ( $homepage_support ) {
			update_post_meta( $post_id, 'show_homepage_galleries', $gallery_support );
			update_post_meta( $post_id, 'show_homepage_podcasts', $podcast_support );
			update_post_meta( $post_id, 'show_homepage_videos', $video_support );
		} else {
			// Impossible to support these if homepage support is turned off
			update_post_meta( $post_id, 'show_homepage_galleries', false );
			update_post_meta( $post_id, 'show_homepage_podcasts', false );
			update_post_meta( $post_id, 'show_homepage_videos', false );

			if ( $gallery_support || $podcast_support || $video_support ) {
				add_filter( 'redirect_post_location', array( $this, 'add_homepage_validation_error' ), 99 );
			}
		}

		update_post_meta( $post_id, 'logo_image', filter_input( INPUT_POST, 'logo_image', FILTER_VALIDATE_INT ) );

		if ( isset( $_POST['gmr-featured-post-ids'] ) ) {
			$featured_ids = implode( ',', array_map( 'intval', explode( ',', $_POST['gmr-featured-post-ids'] ) ) );
			update_post_meta( $post_id, 'gmr_featured_post_ids', $featured_ids );
		}

		if ( isset( $_POST['gmr-favorite-post-ids'] ) ) {
			$favorite_ids = implode( ',', array_map( 'intval', explode( ',', $_POST['gmr-favorite-post-ids'] ) ) );
			update_post_meta( $post_id, 'gmr_favorite_post_ids', $favorite_ids );
		}

		if ( isset( $_POST['show_times'] ) && ! empty( $_POST['show_times'] ) ) {
			update_post_meta( $post_id, 'show_times', sanitize_text_field( $_POST['show_times'] ) );
		} else {
			delete_post_meta( $post_id, 'show_times' );
		}

		if ( isset( $_POST['show_days'] ) && ! empty( $_POST['show_days'] ) ) {
			update_post_meta( $post_id, 'show_days', sanitize_text_field( $_POST['show_days'] ) );
		} else {
			delete_post_meta( $post_id, 'show_days' );
		}
		
		
		// Save social pages
		update_post_meta( $post_id, 'show/social_pages/facebook', sanitize_url( trim( $_POST['show/social_pages/facebook' ] ) ) );
		update_post_meta( $post_id, 'show/social_pages/twitter', sanitize_url( trim( $_POST['show/social_pages/twitter' ] ) ) );
		update_post_meta( $post_id, 'show/social_pages/google', sanitize_url( trim( $_POST['show/social_pages/google' ] ) ) );
	}

	/**
	 * Adds a query var that specifies that we had an error saving all the homepage support so we can trigger an admin notice
	 *
	 * Happens when we say there is no homepage support, but try to add support for albums, podcasts, or videos at the same time.
	 *
	 * @param $location
	 *
	 * @return string
	 */
	public function add_homepage_validation_error( $location ) {
		remove_filter( 'redirect_post_location', array( $this, 'add_homepage_validation_error' ) );
		return add_query_arg( array( 'gmi-show-message' => 1), $location );
	}

	/**
	 * Renders the message that there was an error saving homepage support
	 */
	public function admin_notices() {
		if ( ! isset( $_GET['gmi-show-message'] ) ) {
			return;
		}

		?>
		<div class="error">
			<p>You must enable show homepage support to support Galleries, Podcasts, or Videos.</p>
		</div>
		<?php
	}

	/**
	 * Add an associated show option to the user profile page.
	 *
	 * @param  WP_User $user The user being edited
	 */
	public function admin_user_meta_fields( $user ) {
		$terms = get_terms( ShowsCPT::SHOW_TAXONOMY, array( 'hide_empty' => false ) );
		$current_show_tt_id = get_user_option( 'show_tt_id', intval( $user->ID ) );
		$current_show_term = get_term_by( 'term_taxonomy_id', $current_show_tt_id, ShowsCPT::SHOW_TAXONOMY );
		$current_show = $current_show_term ? $current_show_term->term_id : false;

		?><h3><?php esc_html_e( 'Show Info', 'greatermedia' ); ?></h3>

		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="user-show"><?php esc_html_e( 'Show', 'greatermedia' ); ?></label></th>
					<td>
						<?php if ( ! empty( $terms ) ) : ?>
							<?php wp_dropdown_categories( array(
								'show_option_all' => __( 'None', 'greatermedia' ),
								'hierarchical'    => false,
								'name'            => 'user_show',
								'id'              => 'user-show',
								'class'           => '',
								'orderby'         => 'name',
								'taxonomy'        => ShowsCPT::SHOW_TAXONOMY,
								'hide_if_empty'   => true,
								'selected'        => intval( $current_show ),
							) ); ?>
							<br>
							<span class="description"><?php esc_html_e( 'Choose the show this user is associated with.', 'greatermedia' ); ?></span>
						<?php else : ?>
							<?php esc_html_e( 'There are no shows available.', 'greatermedia' ); ?>
						<?php endif; ?>
					</td>
				</tr>
			</tbody>
		</table><?php
	}

	/**
	 * Save the associated show option on user profile page.
	 *
	 * @param  int $user_id The user ID
	 */
	public function admin_save_user_meta_fields( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		$user_show = filter_input( INPUT_POST, 'user_show', FILTER_VALIDATE_INT );
		if ( 0 < $user_show ) {
			$term = get_term_by( 'id', $user_show, ShowsCPT::SHOW_TAXONOMY );
			if ( false !== $term ) {
				update_user_option( $user_id, 'show_tt_id', $term->term_taxonomy_id, false );
				update_user_option( $user_id, 'show_tt_id_' . $term->term_taxonomy_id, true, false ); // Adds a version of the key with the show in the key, so that lookups are quicker
			}
		} else {
			// Remove the show association
			$old_tt_id = get_user_option( 'show_tt_id', $user_id );
			delete_user_option( $user_id, 'show_tt_id', false );
			delete_user_option( $user_id, 'show_tt_id_' . intval( $old_tt_id ) );
		}
	}

}

$gmr_show_metaboxes = new GMR_Show_Metaboxes();
