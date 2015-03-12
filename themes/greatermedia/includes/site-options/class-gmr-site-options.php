<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class GreaterMediaSiteOptions {

	const option_group = 'greatermedia_site_options';

	/**
	 * Contains the slug of the settings page once it's registered
	 *
	 * @var
	 */
	protected $_settings_page_hook;

	/**
	 * Instance of this class, if it has been created.
	 *
	 * @var GreaterMediaSiteOptions
	 */
	protected static $_instance = null;

	/**
	 * Get the instance of this class, or set it up if it has not been setup yet.
	 *
	 * @return GreaterMediaSiteOptions
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new static();
			self::$_instance->_init();
		}

		return self::$_instance;
	}

	/**
	 * Sets up actions and filters.
	 */
	protected function _init() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function add_settings_page() {
		$this->_settings_page_hook = add_options_page( 'Station Site', 'Station Site', 'manage_options', 'greatermedia-settings', array( $this, 'render_settings_page' ), '', 3 );
	}

	public function render_settings_page() {
		?>
		<form action="options.php" method="post" class="greatermedia-settings-form" style="max-width: 550px;">
			<?php
			settings_fields( self::option_group );
			do_settings_sections( $this->_settings_page_hook );

			/**
			 * Allows adding additional settings sections here.
			 *
			 * Useful for the sections that are only enabled if theme support is enabled, we can conditionally add settings for each child theme.
			 */
			do_action( 'greatermedia-settings-additional-settings' );

			submit_button( 'Submit' );
			?>
		</form>
	<?php
	}


	public function register_settings() {
		// Fallback Thumbnails Section
		add_settings_section( 'greatermedia_fallback_thumbnails', 'Fallback Thumbnails', array( $this, 'render_fallback_section_info' ), 'media' );

		$callback = array( $this, 'render_fallback_image_field' );
		$types = get_post_types( array( 'public' => true ), 'object' );

		// Sort the Post types in the UI
		ksort( $types, SORT_ASC );

		foreach ( $types as $type => $type_object ) {

			// Post types to exclude
			$exclude = array(
				GMR_SUBMISSIONS_CPT,
				GMR_ADVERTISER_CPT,
				GMR_SURVEY_CPT,
				ShowsCPT::SHOW_CPT,
				ShowsCPT::EPISODE_CPT
			);

			// If the Post type is in the exclude list, then don't add to Media Page
			if ( true === in_array( $type_object->name, $exclude ) ) {
				continue;
			}

			if ( post_type_supports( $type, 'thumbnail' ) ) {
				$option_name = "{$type}_fallback";
				add_settings_field( $option_name, $type_object->label, $callback, 'media', 'greatermedia_fallback_thumbnails', array( 'option_name' => $option_name ) );
				register_setting( 'media', $option_name, 'intval' );
			}
		}

		// Settings Section
		add_settings_section( 'greatermedia_site_settings', 'Station Site', array( $this, 'render_site_settings_section' ), $this->_settings_page_hook );
		add_settings_section( 'greatermedia_tracking_codes', 'Tracking Codes', array( $this, 'render_tracking_scripts_section' ), $this->_settings_page_hook );

		// Social URLs
		register_setting( self::option_group, 'gmr_facebook_url', 'esc_url_raw' );
		register_setting( self::option_group, 'gmr_twitter_name', 'sanitize_text_field' );
		register_setting( self::option_group, 'gmr_youtube_url', 'esc_url_raw' );
		register_setting( self::option_group, 'gmr_instagram_name', 'sanitize_text_field' );
		register_setting( self::option_group, 'gmr_site_logo', 'intval' );
		register_setting( self::option_group, 'gmr_site_favicon', 'intval' );
		register_setting( self::option_group, 'gmr_google_analytics', 'sanitize_text_field' );
		register_setting( self::option_group, 'gmr_google_uid_dimension', 'absint' );
		register_setting( self::option_group, 'gmr_livelinks_title', 'sanitize_text_field');

		/**
		 * Allows us to register extra settings that are not necessarily always present on all child sites.
		 */
		do_action( 'greatermedia-settings-register-settings', self::option_group );
	}

	public function render_fallback_section_info() {
		echo '<p>Select fallback images which will be used as thumbnails when original thumbnail of a post will not be selected.</p>';
	}

	public function render_fallback_image_field( $args ) {
		$name = $args['option_name'];

		$image = '';
		$image_id = intval( get_option( $name ) );
		if ( $image_id ) {
			$image = current( (array) wp_get_attachment_image_src( $image_id, 'medium' ) );
		}

		$img_id = $name . '-fallback-image';
		$input_id = $img_id . '-id';
		echo '<input id="', esc_attr( $input_id ), '" name="', esc_attr( $name ), '" type="hidden" value="', esc_attr( $image_id ), '">';
		echo '<img id="', esc_attr( $img_id ), '" src="', esc_attr( $image ), '" style="width:100px;height:auto">';
		echo '<div>';
			echo '<a href="#" class="select-fallback-image button button-primary" data-img="#', esc_attr( $img_id ), '" data-input="#', esc_attr( $input_id ), '">';
				echo 'Choose Image';
			echo '</a> ';
			echo '<a href="#" class="remove-fallback-image button" data-img="#', esc_attr( $img_id ), '" data-input="#', esc_attr( $input_id ), '" style="', ! $image_id ? 'display:none' : '', '">';
				echo 'Remove Image';
			echo '</a>';
		echo '</div>';
	}

	public function render_site_settings_section() {
		$facebook = get_option( 'gmr_facebook_url', '' );
		$twitter = get_option( 'gmr_twitter_name', '' );
		$youtube = get_option( 'gmr_youtube_url', '' );
		$instagram = get_option( 'gmr_instagram_name', '' );
		$site_logo_id = GreaterMediaSiteOptionsHelperFunctions::get_site_logo_id();
		$site_favicon_id = GreaterMediaSiteOptionsHelperFunctions::get_site_favicon_id();
		$livelinks_title = get_option( 'gmr_livelinks_title', '' );

		?>

		<?php self::render_image_select( 'Site Logo', 'gmr_site_logo', $site_logo_id ); ?>

		<hr />

		<?php self::render_image_select( 'Site Fav Icon', 'gmr_site_favicon', $site_favicon_id ); ?>

		<hr />

		<h4>Live Links</h4>

		<div class="gmr__option">
			<label for="gmr_livelinks_title" class="gmr__option--label">Title of Live Links Sidebar</label>
			<input type="text" class="gmr__option--input" name="gmr_livelinks_title" id="gmr_livelinks_title" value="<?php echo esc_attr( $livelinks_title ); ?>" />
		</div>

		<hr />

		<h4>Social Pages</h4>

		<div class="gmr__option">
			<label for="gmr_facebook_url" class="gmr__option--label">Facebook URL</label>
			<input type="text" class="gmr__option--input" name="gmr_facebook_url" id="gmr_facebook_url" value="<?php echo esc_url( $facebook ); ?>" />
		</div>

		<div class="gmr__option">
			<label for="gmr_twitter_url" class="gmr__option--label">Twitter Username</label>
			<input type="text" class="gmr__option--input" name="gmr_twitter_name" id="gmr_twitter_name" value="<?php echo esc_attr( $twitter ); ?>" />
			<div class="gmr-option__field--desc"><?php _e( 'Please enter username minus the @', 'greatermedia' ); ?></div>
		</div>

		<div class="gmr__option">
			<label for="gmr_youtube_url" class="gmr__option--label">YouTube URL</label>
			<input type="text" class="gmr__option--input" name="gmr_youtube_url" id="gmr_youtube_url" value="<?php echo esc_url( $youtube ); ?>" />
		</div>

		<div class="gmr__option">
			<label for="gmr_instagram_url" class="gmr__option--label">Instagram Username</label>
			<input type="text" class="gmr__option--input" name="gmr_instagram_name" id="gmr_instagram_name" value="<?php echo esc_attr( $instagram ); ?>" />
			<div class="gmr-option__field--desc"><?php _e( 'Please enter username only, not a full url.', 'greatermedia' ); ?></div>
		</div>

		<hr />


	<?php
	}

	public function render_tracking_scripts_section() {
		$google_analytics = get_option( 'gmr_google_analytics', '' );
		$google_uid_dimension = get_option( 'gmr_google_uid_dimension', '' );
		if ( 0 === $google_uid_dimension ) {
			$google_uid_dimension = '';
		}
		?>
		<div class="gmr__option">
			<label for="gmr_google_analytics" class="gmr__option--label">Google Analytics ID</label>
			<input type="text" class="gmr__option--input" name="gmr_google_analytics" id="gmr_google_analytics" value="<?php echo esc_attr( $google_analytics ); ?>" />

			<div class="gmr-option__field--desc">UA-xxxxxx-xx</div>
		</div>
		<div class="gmr__option">
			<label for="gmr_google_uid_dimension" class="gmr__option--label">Google User ID Custom Dimension</label>
			dimension<input type="text" name="gmr_google_uid_dimension" id="gmr_google_uid_dimension" value="<?php echo esc_attr( $google_uid_dimension ); ?>" size="3" length="3">
			<div class="gmr-option__field--desc">
				Sends the current user's ID to this custom Google Analytics dimension<br>
				Most sites can use dimension1 unless it is already in use.
			</div>
		</div>
		<?php
	}

	/**
	 * Localize scripts and enqueue
	 */
	public static function enqueue_scripts() {

		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_media();

		wp_enqueue_script( 'gmr-options-admin', get_template_directory_uri() . "/assets/js/greater_media_admin{$postfix}.js", array( 'jquery' ), GREATERMEDIA_VERSION, 'all' );
		wp_enqueue_style( 'gmr-options-admin', get_template_directory_uri() . "/assets/css/greater_media_admin{$postfix}.css", array(), GREATERMEDIA_VERSION );
	}

	public static function render_image_select( $label, $name, $image_id = 0 ) {
		$image_src = wp_get_attachment_image_src( $image_id, 'thumbnail' );
		$image_src = is_array( $image_src ) ? reset( $image_src ): '';

		if ( empty( $image_src ) ) {
			$image_src = get_template_directory_uri() . '/images/admin-no-logo.png';
		}

		?>
		<div class="gmr__option image-select-parent">
			<label for="<?php echo esc_attr( $name ); ?>" class="gmr__option--label"><?php echo esc_html( $label ); ?></label>
			<input class="image-id-input" type="hidden" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" value="<?php echo intval( $image_id ); ?>"/>
			<div class="gmr-image-preview gmr__option--preview">
				<img src="<?php echo esc_url( $image_src ); ?>"/>
			</div>
			<div class="gmr-image-buttons">
				<div class="button select-image">Select Image</div>
				<div class="button remove-image">Remove Image</div>
			</div>
		</div>
	<?php
	}

}

GreaterMediaSiteOptions::instance();