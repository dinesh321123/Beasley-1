<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class GreaterMediaSiteOptions {

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
		add_action( 'bbgi_register_settings', array( $this, 'register_settings' ), 1, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}


	public function register_settings( $group, $page ) {
		// Fallback Thumbnails Section
		add_settings_section( 'greatermedia_fallback_thumbnails', 'Fallback Thumbnails', array( $this, 'render_fallback_section_info' ), 'media' );

		$callback = array( $this, 'render_fallback_image_field' );
		$types = get_post_types( array( 'public' => true ), 'object' );

		// Sort the Post types in the UI
		ksort( $types, SORT_ASC );

		foreach ( $types as $type => $type_object ) {
			// Post types to exclude
			$exclude = array(
				'listener_submissions',
				'advertiser',
				'survey',
				'show',
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
		add_settings_section( 'beasley_site_settings', 'Station Site', array( $this, 'render_site_settings_section' ), $page );
		add_settings_section( 'beasley_social_networks', 'Social Networks', '__return_false', $page );

		add_settings_field( 'gmr_livelinks_title', 'Live Links Sidebar Title', 'bbgi_input_field', $page, 'beasley_site_settings', 'name=gmr_livelinks_title' );

		add_settings_field( 'gmr_facebook_url', 'Facebook', 'bbgi_input_field', $page, 'beasley_social_networks', 'name=gmr_facebook_url' );
		add_settings_field( 'gmr_twitter_name', 'Twitter', 'bbgi_input_field', $page, 'beasley_social_networks', array( 'name' => 'gmr_twitter_name', 'desc' => 'Please enter username minus the @' ) );
		add_settings_field( 'gmr_youtube_url', 'Youtube', 'bbgi_input_field', $page, 'beasley_social_networks', 'name=gmr_youtube_url' );
		add_settings_field( 'gmr_instagram_name', 'Instagram', 'bbgi_input_field', $page, 'beasley_social_networks', 'name=gmr_instagram_name' );

		// Social URLs
		register_setting( $group, 'gmr_facebook_url', 'esc_url_raw' );
		register_setting( $group, 'gmr_twitter_name', 'sanitize_text_field' );
		register_setting( $group, 'gmr_youtube_url', 'esc_url_raw' );
		register_setting( $group, 'gmr_instagram_name', 'sanitize_text_field' );
		register_setting( $group, 'gmr_site_logo', 'intval' );
		register_setting( $group, 'gmr_livelinks_title', 'sanitize_text_field');
		register_setting( $group, 'gmr_newssite', 'esc_attr' );
		register_setting( $group, 'gmr_livelinks_more_redirect', 'esc_attr' );
		register_setting( $group, 'gmr_liveplayer_disabled', 'esc_attr' );
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
		$news_site = get_option( 'gmr_newssite', '' );
		$livelinks_more = get_option( 'gmr_livelinks_more_redirect', '' );
		$liveplayer_disabled = get_option( 'gmr_liveplayer_disabled', '' );

		$site_logo_id = get_option( 'gmr_site_logo', 0 );
		self::render_image_select( 'Site Logo', 'gmr_site_logo', $site_logo_id ); ?>

		<hr />

		<h4><?php _e( 'Station Type', 'greatermedia' ); ?></h4>

		<div class="gmr__option">
			<input type="checkbox" name="gmr_newssite" id="gmr_newssite" value="1" <?php checked( 1 == esc_attr( $news_site ) ); ?>><label for="gmr_newssite" class="gmr__option--label-inline"><?php _e( 'News/Sports Station', 'greatermedia' ); ?></label>
			<div class="gmr-option__field--desc"><?php _e( 'Check this box if this site is for a News or Sports Radio Station.', 'greatermedia' ); ?></div>
		</div>

		<hr />

		<h4><?php _e( 'Live Player and Live Links', 'greatermedia' ); ?></h4>

		<div class="gmr__option">
			<input type="checkbox" name="gmr_liveplayer_disabled" id="gmr_liveplayer_disabled" value="1" <?php checked( 1 == esc_attr( $liveplayer_disabled ) ); ?> /><label for="gmr_liveplayer_disabled" class="gmr__option--label-inline"><?php _e( 'Disable the Live Player', 'greatermedia' ); ?></label>
			<div class="gmr-option__field--desc"><?php _e( 'Check this box if this site does not have a live audio stream.', 'greatermedia' ); ?></div>
		</div>

		<div class="gmr__option">
			<input type="checkbox" name="gmr_livelinks_more_redirect" id="gmr_livelinks_more_redirect" value="1" <?php checked( 1 == esc_attr( $livelinks_more ) ); ?> /><label for="gmr_livelinks_more_redirect" class="gmr__option--label-inline"><?php _e( 'Redirect the Live Links "More" button to the Station Stream Archive', 'greatermedia' ); ?></label>
			<div class="gmr-option__field--desc"><?php _e( 'By default, the "More" button located in the Live Links section of the live player sidebar, points to an archive of Live Links for the station. Checking this box will change the reference point for the more button so that when clicked, the button redirects to a Stream Archive for the Station.', 'greatermedia' ); ?></div>
		</div>

		<hr /><?php
	}

	/**
	 * Localize scripts and enqueue
	 */
	public static function enqueue_scripts() {
		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
		$baseurl = untrailingslashit( get_template_directory_uri() );

		wp_enqueue_media();

		wp_enqueue_script( 'gmr-options-admin', "{$baseurl}/assets/js/admin{$postfix}.js", array( 'jquery' ), GREATERMEDIA_VERSION, 'all' );
		wp_enqueue_style( 'gmr-options-admin', "{$baseurl}/assets/css/greater_media_admin{$postfix}.css", array(), GREATERMEDIA_VERSION );
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
