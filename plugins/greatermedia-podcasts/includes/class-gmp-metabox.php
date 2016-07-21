<?php
/**
 * Class GMP_Meta
 *
 * This class constructs a meta box for episodes and saves data entered into the fields of the meta box.
 */
class GMP_Meta {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );

		add_action( 'post_submitbox_misc_actions', array( $this, 'post_submitbox_misc_actions' ) );
		add_action( 'save_post', array( $this, 'save_episode_meta_box' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );

	}

	/**
	 * Displays episode settings.
	 *
	 * @action post_submitbox_misc_actions
	 */
	public function post_submitbox_misc_actions() {
		global $typenow;
		if ( GMP_CPT::EPISODE_POST_TYPE != $typenow ) {
			return;
		}

		$post = get_post();

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'gmp_episodes_meta_box', 'gmp_episodes_meta_box_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$gmp_explicit = sanitize_text_field( get_post_meta( $post->ID, 'gmp_episode_explicit', true ) );
		$gmp_explicit = filter_var( $gmp_explicit, FILTER_VALIDATE_BOOLEAN );

		$gmp_block = sanitize_text_field( get_post_meta( $post->ID, 'gmp_block', true ) );
		$gmp_block = filter_var( $gmp_block, FILTER_VALIDATE_BOOLEAN );

		$gmp_downloadable = sanitize_text_field( get_post_meta( $post->ID, 'gmp_audio_downloadable', true ) );
		$gmp_downloadable = filter_var( $gmp_downloadable, FILTER_VALIDATE_BOOLEAN );

		?><div id="episode-explicit" class="misc-pub-section misc-pub-gmr-podcast mis-pub-radio">
			Explicit:
			<span class="post-pub-section-value radio-value"><?php echo $gmp_explicit ? 'Yes' : 'No'; ?></span>
			<a href="#" class="edit-radio hide-if-no-js" style="display: inline;"><span aria-hidden="true">Edit</span></a>

			<div class="radio-select hide-if-js">
				<label><input type="radio" name="gmp_episode_explicit" value="on"<?php checked( $gmp_explicit, true ); ?>> Yes</label><br>
				<label><input type="radio" name="gmp_episode_explicit" value="off"<?php checked( $gmp_explicit, false ); ?>> No</label><br>

				<p>
					<a href="#" class="save-radio hide-if-no-js button"><?php esc_html_e( 'OK' ) ?></a>
					<a href="#" class="cancel-radio hide-if-no-js button-cancel"><?php esc_html_e( 'Cancel' ) ?></a>
				</p>
			</div>
		</div>

		<div id="episode-block-from-feed" class="misc-pub-section misc-pub-gmr-podcast mis-pub-radio">
			Block From Feed:
			<span class="post-pub-section-value radio-value"><?php echo $gmp_block ? 'Yes' : 'No'; ?></span>
			<a href="#" class="edit-radio hide-if-no-js" style="display: inline;"><span aria-hidden="true">Edit</span></a>

			<div class="radio-select hide-if-js">
				<label><input type="radio" name="gmp_block" value="on"<?php checked( $gmp_block, true ); ?>> Yes</label><br>
				<label><input type="radio" name="gmp_block" value="off"<?php checked( $gmp_block, false ); ?>> No</label><br>

				<p>
					<a href="#" class="save-radio hide-if-no-js button"><?php esc_html_e( 'OK' ) ?></a>
					<a href="#" class="cancel-radio hide-if-no-js button-cancel"><?php esc_html_e( 'Cancel' ) ?></a>
				</p>
			</div>
		</div>

		<div id="episode-downloadable" class="misc-pub-section misc-pub-gmr-podcast mis-pub-radio">
			Downloadable:
			<span class="post-pub-section-value radio-value"><?php echo $gmp_downloadable ? 'Yes' : 'No'; ?></span>
			<a href="#" class="edit-radio hide-if-no-js" style="display: inline;"><span aria-hidden="true">Edit</span></a>

			<div class="radio-select hide-if-js">
				<label><input type="radio" name="gmp_audio_downloadable" value="on"<?php checked( $gmp_downloadable, true ); ?>> Yes</label><br>
				<label><input type="radio" name="gmp_audio_downloadable" value="off"<?php checked( $gmp_downloadable, false ); ?>> No</label><br>

				<p>
					<a href="#" class="save-radio hide-if-no-js button"><?php esc_html_e( 'OK' ) ?></a>
					<a href="#" class="cancel-radio hide-if-no-js button-cancel"><?php esc_html_e( 'Cancel' ) ?></a>
				</p>
			</div>
		</div><?php
	}

	/**
	 * Adds the meta box container for Episodes.
	 *
	 * @param $post_type
	 */
	public function add_meta_box( $post_type ) {

		$post_types = array( GMP_CPT::PODCAST_POST_TYPE );

		if ( in_array( $post_type, $post_types ) ) {
			add_meta_box(
				'gmp_episodes_meta_box'
				, __( 'Podcast Details', 'gmpodcasts' )
				, array( $this, 'render_meta_box_content' )
				, $post_type
				, 'normal'
				, 'high'
			);
		}

	}

	/**
	 * Save the meta when the post is saved for Episodes.
	 *
	 * @param $post_id
	 */
	public function save_meta_box( $post_id ) {

		// Check if our nonce is set and that it validates it. Also serves as a post type check, because this is only created in the post-type specific meta box
		if ( ! isset( $_POST['gmp_podcast_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['gmp_podcast_meta_box_nonce' ], 'gmp_podcast_meta_box' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted we don't want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

		/* OK, its safe for us to save the data now. */
		$itunes_url = esc_url_raw( $_POST[ 'gmp_podcast_itunes_url' ] );
		// Sanitize and save the user input.
		update_post_meta( $post_id, 'gmp_podcast_itunes_url', esc_url_raw( $itunes_url ) );

		$google_play_url = esc_url_raw( $_POST[ 'gmp_podcast_google_play_url' ] );
		// Sanitize and save the user input.
		update_post_meta( $post_id, 'gmp_podcast_google_play_url', esc_url_raw( $google_play_url ) );

		$gmp_subtitle =  sanitize_text_field( $_POST['gmp_subtitle'] );
		update_post_meta( $post_id, 'gmp_subtitle', $gmp_subtitle );

		$gmp_subtitle =  sanitize_text_field( $_POST['gmp_explicit'] );
		update_post_meta( $post_id, 'gmp_explicit', $gmp_subtitle );

		$gmp_category =  sanitize_text_field( $_POST['gmp_category'] );
		update_post_meta( $post_id, 'gmp_category', $gmp_category );

		$gmp_sub_category =  sanitize_text_field( $_POST['gmp_sub_category'] );
		update_post_meta( $post_id, 'gmp_sub_category', $gmp_sub_category );

		$gmp_author =  sanitize_text_field( $_POST['gmp_author'] );
		update_post_meta( $post_id, 'gmp_author', $gmp_author );

		$podcast_feed = esc_url_raw( $_POST['gmp_podcast_feed'] );
		update_post_meta( $post_id, 'gmp_podcast_feed', esc_url_raw( $podcast_feed ) );


	}


	public function save_episode_meta_box( $post_id ) {

		// Check if our nonce is set and that it validates it. Also serves as a post type check, because this is only created in the post-type specific meta box
		if ( ! isset( $_POST['gmp_episodes_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['gmp_episodes_meta_box_nonce' ], 'gmp_episodes_meta_box' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted we don't want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

		/* OK, its safe for us to save the data now. */
		$gmp_episode_explicit =  '';
		if ( isset( $_POST['gmp_episode_explicit'] ) ) {
			$gmp_episode_explicit =  sanitize_text_field( $_POST['gmp_episode_explicit'] );
		}
		update_post_meta( $post_id, 'gmp_episode_explicit', $gmp_episode_explicit );

		$gmp_block =  '';
		if ( isset( $_POST['gmp_block'] ) ) {
			$gmp_block =  sanitize_text_field( $_POST['gmp_block'] );
		}

		update_post_meta( $post_id, 'gmp_block', $gmp_block );

		update_post_meta( $post_id, 'gmp_audio_downloadable', filter_input( INPUT_POST, 'gmp_audio_downloadable', FILTER_VALIDATE_BOOLEAN ) );
	}

	/**
	 * Render Meta Box content for Episodes.
	 *
	 * @param $post
	 */
	public function render_meta_box_content( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'gmp_podcast_meta_box', 'gmp_podcast_meta_box_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$itunes_url = esc_url( get_post_meta( $post->ID, 'gmp_podcast_itunes_url', true ) );
		$google_play_url = esc_url( get_post_meta( $post->ID, 'gmp_podcast_google_play_url', true ) );
		$gmp_subtitle = sanitize_text_field( get_post_meta( $post->ID, 'gmp_subtitle', true ) );
		$gmp_explicit = sanitize_text_field( get_post_meta( $post->ID, 'gmp_explicit', true ) );
		$gmp_category = sanitize_text_field( get_post_meta( $post->ID, 'gmp_category', true ) );
		$gmp_sub_category = sanitize_text_field( get_post_meta( $post->ID, 'gmp_sub_category', true ) );
		$gmp_author = sanitize_text_field( get_post_meta( $post->ID, 'gmp_author', true ) );
		$podcast_feed = esc_url( get_post_meta( $post->ID, 'gmp_podcast_feed', true ) );
		?>

		<div class="gmp-meta-row">
			<div class="gmp-meta-row-content gmp-upload">
					<label for="gmp_podcast_itunes_url" class="gmp-meta-row-label"><?php _e( 'iTunes Feed URL:', 'gmpodcasts' ); ?></label>
					<input type="text" id="gmp_podcast_itunes_url" name="gmp_podcast_itunes_url" value="<?php echo esc_url( $itunes_url ); ?>"/>
			</div>
		</div>
		<br>
		<div class="gmp-meta-row">
			<div class="gmp-meta-row-content gmp-upload">
					<label for="gmp_podcast_google_play_url" class="gmp-meta-row-label"><?php _e( 'Google Play Feed URL:', 'gmpodcasts' ); ?></label>
					<input type="text" id="gmp_podcast_google_play_url" name="gmp_podcast_google_play_url" value="<?php echo esc_url( $google_play_url ); ?>"/>
			</div>
		</div>
		<br>
		<div class="gmp-meta-row">
			<div class="gmp-meta-row-content gmp-upload">
					<label for="gmp_podcast_feed" class="gmp-meta-row-label"><?php _e( 'Custom Podcast Feed URL:', 'gmpodcasts' ); ?></label>
					<input type="text" id="gmp_podcast_feed" name="gmp_podcast_feed" value="<?php echo esc_url( $podcast_feed ); ?>"/>
			</div>
		</div>
		<br>
		<div class="gmp-meta-row">
			<div class="gmp-meta-row-content gmp-upload">
				<label for="gmp_subtitle" class="gmp-meta-row-label"><?php _e( 'Podcast Subtitle:', 'gmpodcasts' ); ?></label>
				<input type="text" id="gmp_subtitle" name="gmp_subtitle" value="<?php echo esc_attr( $gmp_subtitle ); ?>"/>
			</div>
		</div>
		<br>
		<div class="gmp-meta-row">
			<div class="gmp-meta-row-content gmp-upload">
				<label for="gmp_author" class="gmp-meta-row-label"><?php _e( 'Podcast Author:', 'gmpodcasts' ); ?></label>
				<input type="text" id="gmp_author" name="gmp_author" value="<?php echo esc_attr( $gmp_author ); ?>"/>
				<br>
				<span class="description">Specify custom podcast author. Post author will be used by default.</span>
			</div>
		</div>
		<br>
		<div class="gmp-meta-row">
			<div class="gmp-meta-row-content gmp-upload">
				<label for="gmp_explicit" class="gmp-meta-row-label"><?php _e( 'Explicit:', 'gmpodcasts' ); ?></label>
				<input type="checkbox" id="gmp_explicit" name="gmp_explicit" <?php checked( 'on', $gmp_explicit, true ); ?>/>
			</div>
		</div>
		<br>
		<div class="gmp-meta-row">
			<div class="gmp-meta-row-content gmp-upload">
				<label for="gmp_category" class="gmp-meta-row-label">
					<?php _e( 'iTunes Category:', 'gmpodcasts' ); ?>
				</label>
				<input type="text" id="gmp_category" name="gmp_category" value="<?php echo esc_attr( $gmp_category ); ?>" />
				<br>
				<?php echo '<span class="description">' . sprintf( __( 'Your podcast\'s category - use one of the first-tier categories from %1$sthis list%2$s.' , 'ss-podcasting' ) , '<a href="' . esc_url( 'http://www.apple.com/itunes/podcasts/specs.html#categories' ) . '" target="' . esc_attr( '_blank' ) . '">' , '</a>' ) . '</span>'; ?>
			</div>
		</div>
		<br>
		<div class="gmp-meta-row">
			<div class="gmp-meta-row-content gmp-upload">
				<label for="gmp_sub_category" class="gmp-meta-row-label">
					<?php _e( 'iTunes Sub-Category:', 'gmpodcasts' ); ?>
				</label>
				<input type="text" id="gmp_sub_category" name="gmp_sub_category" value="<?php echo esc_attr( $gmp_sub_category ); ?>"/>
				<br>
				<?php echo '<span class="description">' . sprintf( __( 'Your podcast\'s sub-category - use one of the second-tier categories from %1$sthis list%2$s (must be a sub-category of your selected primary category).' , 'ss-podcasting' ) , '<a href="' . esc_url( 'http://www.apple.com/itunes/podcasts/specs.html#categories' ) . '" target="' . esc_attr( '_blank' ) . '">' , '</a>' ) . '</span>'; ?>
			</div>
		</div>

	<?php

	}

	/**
	 * Enqueue scripts and styles for Admin
	 */
	public function enqueue_scripts_styles() {
		global $pagenow, $typenow;

		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

//		wp_enqueue_media();
//		wp_enqueue_script( 'gmp-admin-js', GMPODCASTS_URL . "/assets/js/gmp_admin{$postfix}.js", array( 'jquery' ), GMPODCASTS_VERSION, true );

		if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) && ( GMP_CPT::EPISODE_POST_TYPE == $typenow || GMP_CPT::PODCAST_POST_TYPE == $typenow ) ) {
			wp_enqueue_style( 'gmp-admin-style', GMPODCASTS_URL . "/assets/css/gmp_admin{$postfix}.css", null, GMPODCASTS_VERSION );
			wp_enqueue_script( 'gmp-admin-episode-js', GMPODCASTS_URL . "/assets/js/gmp_admin_episode{$postfix}.js", array( 'jquery' ), GMPODCASTS_VERSION, true );
		}
	}

}

$GMP_Meta = new GMP_Meta();
