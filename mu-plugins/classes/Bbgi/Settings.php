<?php
/**
 * Registers "Station Settings" admin page
 * wp-admin/options-general.php?page=greatermedia-settings
 */

namespace Bbgi;

class Settings extends \Bbgi\Module {

	const option_group = 'greatermedia_site_options';

	/**
	 * Contains the slug of the settings page once it's registered
	 *
	 * @access protected
	 * @var string
	 */
	protected $_settings_page_hook;

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		add_action( 'admin_menu', $this( 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Adds settings page.
	 *
	 * @access public
	 * @action admin_menu
	 */
	public function add_settings_page() {
		$this->_settings_page_hook = add_options_page( 'Station Settings', 'Station Settings', 'manage_options', 'greatermedia-settings', array( $this, 'render_settings_page' ) );
	}

	/**
	 * Renders settings page.
	 *
	 * @access public
	 */
	public function render_settings_page() {
		echo '<form action="options.php" method="post" style="max-width:750px;">';
			settings_fields( self::option_group );
			do_settings_sections( $this->_settings_page_hook );
			submit_button( 'Submit' );
		echo '</form>';
	}

	/**
	 * Registers settings.
	 *
	 * @access public
	 * @action admin_init
	 */
	public function register_settings() {
		// Fallback Thumbnails Section
		$section_info = bbgi_settings_section_info( 'Select fallback images which will be used as thumbnails when original thumbnail of a post will not be selected.' );
		add_settings_section( 'bbgi_fallback_thumbs', 'Fallback Thumbnails', $section_info, 'media' );

		$callback = array( $this, 'render_fallback_image_field' );
		$types = get_post_types( array( 'public' => true ), 'object' );

		// Sort the Post types in the UI
		ksort( $types, SORT_ASC );

		// Post types to exclude
		$exclude = array(
			'listener_submissions',
			'advertiser',
			'survey',
		);

		foreach ( $types as $type => $type_object ) {
			// If the Post type is in the exclude list, then don't add to Media Page
			if ( true === in_array( $type_object->name, $exclude ) ) {
				continue;
			}

			if ( post_type_supports( $type, 'thumbnail' ) ) {
				$option_name = "{$type}_fallback";
				add_settings_field( $option_name, $type_object->label, $callback, 'media', 'bbgi_fallback_thumbs', array( 'option_name' => $option_name ) );
				register_setting( 'media', $option_name, 'intval' );
			}
		}

		$theme_version_args = array(
			'name'    => 'ee_theme_version',
			'default' => '-dark',
			'class'   => 'regular-text',
			'options' => array(
				'-light' => 'Light',
				'-dark'  => 'Dark',
			),
		);

		$newsletter_args = array(
			'name'              => 'ee_newsletter_signup_page',
			'selected'          => get_option( 'ee_newsletter_signup_page' ),
			'show_option_none'  => '&#8212;',
			'option_none_value' => '0',
		);

		$publisher_args = array(
			'name'     => 'ee_publisher',
			'selected' => get_option( 'ee_publisher' ),
		);

		$ee_login_disabled_args = array(
			'name'     => 'ee_login',
			'selected' => 'disabled' === get_option( 'ee_login', '' ),
		);

		$feature_video_provider_disabled_args = array(
				'name'     => 'feature_video_provider',
				'selected' => get_option( 'feature_video_provider', 'none' ),
		);

		$ee_geotargetly_enabled_args = [
			'name' => 'ee_geotargetly_enabled',
		];

		$ee_geotargetly_embed_code_args = [
			'name' => 'ee_geotargetly_embed_code',
		];

		$contest_show_dates_args = array(
				'name'     => 'contest_show_dates_setting',
				'selected' => get_option( 'contest_show_dates_setting', 'hide' ),
		);

		$ad_lazy_loading_enabled_args = array(
				'name'     => 'ad_lazy_loading_enabled',
				'selected' => get_option( 'ad_lazy_loading_enabled', 'off' ),
		);

		$ad_rotation_enabled_args = array(
				'name'     => 'ad_rotation_enabled',
				'selected' => get_option( 'ad_rotation_enabled', 'on' ),
		);

		add_settings_section( 'ee_site_settings', 'Station Settings', '__return_false', $this->_settings_page_hook );
		add_settings_section( 'ee_site_colors', 'Brand Colors', '__return_false', $this->_settings_page_hook );

		add_settings_section( 'opacity_section', 'Play Button Opacity', '__return_false', $this->_settings_page_hook );
		add_settings_field('play_opacity_setting', 'Opacity', 'bbgi_input_field', $this->_settings_page_hook, 'opacity_section', 'name=play_opacity_setting&default=0.8');
		add_settings_field('play_hover_opacity_setting', 'Hover Opacity', 'bbgi_input_field', $this->_settings_page_hook, 'opacity_section', 'name=play_hover_opacity_setting&default=1');
		add_settings_field('play_live_hover_opacity_setting', 'Live Play Hover Opacity', 'bbgi_input_field', $this->_settings_page_hook, 'opacity_section', 'name=play_live_hover_opacity_setting&default=0.8');

		add_settings_section( 'ee_geotargetly', 'Geo Targetly', '__return_false', $this->_settings_page_hook );
		add_settings_field( 'ee_geotargetly_enabled', 'Geo Targetly Enabled', 'bbgi_checkbox_field', $this->_settings_page_hook, 'ee_geotargetly', $ee_geotargetly_enabled_args );
		add_settings_field( 'ee_geotargetly_embed_code', 'Geo Targetly Embed Code', 'bbgi_textarea_field', $this->_settings_page_hook, 'ee_geotargetly', $ee_geotargetly_embed_code_args );

		add_settings_section('feature_video', 'Feature Video', '__return_false', $this->_settings_page_hook);
		add_settings_field('feature_video_provider', 'Feature Video Provider', array($this, 'render_feature_video_provider'), $this->_settings_page_hook, 'feature_video', $feature_video_provider_disabled_args);
		add_settings_field('stn_cid', 'STN CID', 'bbgi_input_field', $this->_settings_page_hook, 'feature_video', 'name=stn_cid');
		add_settings_field('stn_barker_id', 'STN Barker ID', 'bbgi_input_field', $this->_settings_page_hook, 'feature_video', 'name=stn_barker_id');
		add_settings_field('stn_inarticle_id', 'STN In Article ID', 'bbgi_input_field', $this->_settings_page_hook, 'feature_video', 'name=stn_inarticle_id');
		add_settings_field('stn_categories', 'STN Allowed Categories', 'bbgi_input_field', $this->_settings_page_hook, 'feature_video', 'name=stn_categories');

		add_settings_field( 'gmr_site_logo', 'Site Logo', 'bbgi_image_field', $this->_settings_page_hook, 'ee_site_settings', 'name=gmr_site_logo' );
		add_settings_field( 'ee_subheader_mobile_logo', 'Mobile Subheader Logo', 'bbgi_image_field', $this->_settings_page_hook, 'ee_site_settings', 'name=ee_subheader_mobile_logo' );
		add_settings_field( 'ee_subheader_desktop_logo', 'Desktop Subheader Logo', 'bbgi_image_field', $this->_settings_page_hook, 'ee_site_settings', 'name=ee_subheader_desktop_logo' );

		add_settings_field( 'ee_theme_version', 'Theme Version', 'bbgi_select_field', $this->_settings_page_hook, 'ee_site_settings', $theme_version_args );
		add_settings_field( 'ee_newsletter_signup_page', 'Newsletter Signup Page', 'wp_dropdown_pages', $this->_settings_page_hook, 'ee_site_settings', $newsletter_args );
		add_settings_field( 'ee_publisher', 'Publisher', array( $this, 'render_publisher_select' ), $this->_settings_page_hook, 'ee_site_settings', $publisher_args );
		add_settings_field( 'ee_login', 'EE Login Options', array( $this, 'render_ee_login' ), $this->_settings_page_hook, 'ee_site_settings', $ee_login_disabled_args );

		add_settings_field( 'ee_theme_primary_color', 'Primary', 'bbgi_input_field', $this->_settings_page_hook, 'ee_site_colors', 'name=ee_theme_primary_color&default=#ff0000' );
		add_settings_field( 'ee_theme_secondary_color', 'Secondary', 'bbgi_input_field', $this->_settings_page_hook, 'ee_site_colors', 'name=ee_theme_secondary_color&default=#ffe964' );
		add_settings_field( 'ee_theme_tertiary_color', 'Tertiary', 'bbgi_input_field', $this->_settings_page_hook, 'ee_site_colors', 'name=ee_theme_tertiary_color&default=#ffffff' );

		add_settings_field( 'ee_theme_background_color', 'Background Color', 'bbgi_input_field', $this->_settings_page_hook, 'ee_site_colors', 'name=ee_theme_background_color&default=#ffffff' );

		add_settings_field( 'ee_theme_button_color', 'Button Color', 'bbgi_input_field', $this->_settings_page_hook, 'ee_site_colors', 'name=ee_theme_button_color&default=#ffe964' );

		add_settings_field( 'ee_theme_text_color', 'Text Color', 'bbgi_input_field', $this->_settings_page_hook, 'ee_site_colors', 'name=ee_theme_text_color&default=#000000' );

		add_settings_section( 'contest_section', 'Contests', '__return_false', $this->_settings_page_hook );
		add_settings_field('contest_show_dates_setting', 'Date Display', array($this, 'render_contest_show_dates'), $this->_settings_page_hook, 'contest_section', $contest_show_dates_args);

		add_settings_section( 'ad_settings_section', 'Ad Settings', '__return_false', $this->_settings_page_hook );
		add_settings_field('ad_lazy_loading_enabled', 'Lazy Loading Enabled', array($this, 'render_ad_lazy_loading_enabled'), $this->_settings_page_hook, 'ad_settings_section', $ad_lazy_loading_enabled_args);
		add_settings_field('ad_rotation_enabled', 'Ad Rotation Enabled (Note: Ads on Right Rail will ALWAYS rotate)', array($this, 'render_ad_rotation_enabled'), $this->_settings_page_hook, 'ad_settings_section', $ad_rotation_enabled_args);
		add_settings_field('ad_rotation_polling_sec_setting', 'Poll Interval Seconds (5 is recomended)', 'bbgi_input_field', $this->_settings_page_hook, 'ad_settings_section', 'name=ad_rotation_polling_sec_setting&default=5');
		add_settings_field('ad_rotation_refresh_sec_setting', 'Refresh Interval Seconds (30 is recomended)', 'bbgi_input_field', $this->_settings_page_hook, 'ad_settings_section', 'name=ad_rotation_refresh_sec_setting&default=30');
		add_settings_field('ad_vid_rotation_refresh_sec_setting', 'Video Refresh Interval Seconds (60 is recomended)', 'bbgi_input_field', $this->_settings_page_hook, 'ad_settings_section', 'name=ad_vid_rotation_refresh_sec_setting&default=60');
		add_settings_field('vid_ad_html_tag_csv_setting', 'CSV of HTML tags which indicate Video', 'bbgi_input_field', $this->_settings_page_hook, 'ad_settings_section', 'name=vid_ad_html_tag_csv_setting&default=mixpo');

		add_settings_section( 'prebid_settings_section', 'Prebid Settings', '__return_false', $this->_settings_page_hook );
		add_settings_field('ad_rubicon_zoneid_setting', 'Rubicon Zone ID', 'bbgi_input_field', $this->_settings_page_hook, 'prebid_settings_section', 'name=ad_rubicon_zoneid_setting');
		add_settings_field('ad_appnexus_placementid_setting', 'AppNexus Placement ID', 'bbgi_input_field', $this->_settings_page_hook, 'prebid_settings_section', 'name=ad_appnexus_placementid_setting');

		add_settings_section( 'configurable_iframe_section', 'Configurable iFrame', '__return_false', $this->_settings_page_hook );
		add_settings_field('configurable_iframe_height', 'iFrame Height (0 for no iFrame)', 'bbgi_input_field', $this->_settings_page_hook, 'configurable_iframe_section', 'name=configurable_iframe_height&default=0');
		add_settings_field('configurable_iframe_src', 'iFrame URL', 'bbgi_input_field', $this->_settings_page_hook, 'configurable_iframe_section', 'name=configurable_iframe_src');

		add_settings_section( 'item_counts_section', 'Item Counts', '__return_false', $this->_settings_page_hook );
		add_settings_field( 'ee_featured_item_count_setting', 'Featured Item Count', 'bbgi_input_field', $this->_settings_page_hook, 'item_counts_section', array(
			'name' => 'ee_featured_item_count_setting',
			'default' => '10',
			'desc' => 'Number of items which will be displayed in the Featured Section. Commonly set to 10',
		) );
		add_settings_field( 'ee_dont_miss_item_count_setting', "Don't Miss Item Count", 'bbgi_input_field', $this->_settings_page_hook, 'item_counts_section', array(
			'name' => 'ee_dont_miss_item_count_setting',
			'default' => '10',
			'desc' => "Number of items which will be displayed in the Don't Miss Section. Commonly set to 10",
		) );

		add_settings_section( 'related_article_section', 'Related Articles', '__return_false', $this->_settings_page_hook );
		add_settings_field( 'related_article_title', 'Title Text', 'bbgi_input_field', $this->_settings_page_hook, 'related_article_section', 'name=related_article_title&default=You May Also Like' );

		register_setting( self::option_group, 'gmr_site_logo', 'intval' );
		register_setting( self::option_group, 'ee_subheader_mobile_logo', 'intval' );
		register_setting( self::option_group, 'ee_subheader_desktop_logo', 'intval' );
		register_setting( self::option_group, 'ee_newsletter_signup_page', 'intval' );
		register_setting( self::option_group, 'ee_theme_version', 'sanitize_text_field' );
		register_setting( self::option_group, 'ee_publisher', 'sanitize_text_field' );
		register_setting( self::option_group, 'ee_login', 'sanitize_text_field' );

		register_setting(self::option_group, 'feature_video_provider', 'sanitize_text_field');
		register_setting(self::option_group, 'stn_cid', 'sanitize_text_field');
		register_setting(self::option_group, 'stn_barker_id', 'sanitize_text_field');
		register_setting(self::option_group, 'stn_inarticle_id', 'sanitize_text_field');
		register_setting(self::option_group, 'stn_categories', 'sanitize_text_field');

		register_setting( self::option_group, 'ee_theme_primary_color', 'sanitize_text_field' );
		register_setting( self::option_group, 'ee_theme_secondary_color', 'sanitize_text_field' );
		register_setting( self::option_group, 'ee_theme_tertiary_color', 'sanitize_text_field' );
		register_setting( self::option_group, 'ee_theme_background_color', 'sanitize_text_field' );
		register_setting( self::option_group, 'ee_theme_button_color', 'sanitize_text_field' );
		register_setting( self::option_group, 'ee_theme_text_color', 'sanitize_text_field' );

		register_setting( self::option_group, 'ee_geotargetly_enabled', 'sanitize_text_field' );

		// Note: No Sanitization with the assumption that the GeoTargetly embed code is XSS safe
		// Not for use with untrusted JS code
		register_setting( self::option_group, 'ee_geotargetly_embed_code', '' );

		register_setting(self::option_group, 'play_opacity_setting', 'sanitize_text_field');
		register_setting(self::option_group, 'play_hover_opacity_setting', 'sanitize_text_field');
		register_setting(self::option_group, 'play_live_hover_opacity_setting', 'sanitize_text_field');

		register_setting(self::option_group, 'contest_show_dates_setting', 'sanitize_text_field');

		register_setting(self::option_group, 'ad_lazy_loading_enabled', 'sanitize_text_field');
		register_setting(self::option_group, 'ad_rotation_enabled', 'sanitize_text_field');
		register_setting(self::option_group, 'ad_rotation_polling_sec_setting', 'sanitize_text_field');
		register_setting(self::option_group, 'ad_rotation_refresh_sec_setting', 'sanitize_text_field');
		register_setting(self::option_group, 'ad_vid_rotation_refresh_sec_setting', 'sanitize_text_field');
		register_setting(self::option_group, 'vid_ad_html_tag_csv_setting', 'sanitize_text_field');
		register_setting(self::option_group, 'ad_rubicon_zoneid_setting', 'sanitize_text_field');
		register_setting(self::option_group, 'ad_appnexus_placementid_setting', 'sanitize_text_field');

		register_setting(self::option_group, 'configurable_iframe_height', 'sanitize_text_field');
		register_setting(self::option_group, 'configurable_iframe_src', 'sanitize_text_field');

		register_setting(self::option_group, 'ee_featured_item_count_setting', 'sanitize_text_field');
		register_setting(self::option_group, 'ee_dont_miss_item_count_setting', 'sanitize_text_field');

		register_setting(self::option_group, 'related_article_title', 'sanitize_text_field');

		/**
		 * Allows us to register extra settings that are not necessarily always present on all child sites.
		 */
		do_action( 'bbgi_register_settings', self::option_group, $this->_settings_page_hook );
	}

	/**
	 * Renders fallback image selection field.
	 *
	 * @access public
	 * @param array $args
	 */
	public function render_fallback_image_field( $args ) {
		static $render_script = true;

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
			echo '<button class="select-fallback-image button button-primary" data-img="#', esc_attr( $img_id ), '" data-input="#', esc_attr( $input_id ), '">';
				echo 'Choose Image';
			echo '</button> ';
			echo '<button class="remove-fallback-image button" data-img="#', esc_attr( $img_id ), '" data-input="#', esc_attr( $input_id ), '" style="', ! $image_id ? 'display:none' : '', '">';
				echo 'Remove Image';
			echo '</button>';
		echo '</div>';

		if ( $render_script ) {
			$render_script = false;
			wp_enqueue_media();

			?><script>
				(function ($) {
					$(document).ready(function () {
						var imageFrame,
							selectedImage,
							selectedInput,
							nextButton;

						$('.select-fallback-image').click(function() {
							var $this = $(this);

							selectedImage = $this.data('img');
							selectedInput = $this.data('input');
							nextButton = $this.next();

							// if the frame already exists, open it
							if (imageFrame) {
								imageFrame.open();
								return false;
							}

							// set our settings
							imageFrame = wp.media({
								title: 'Choose Image',
								multiple: false,
								library: { type: 'image' },
								button: { text: 'Use This Image' }
							});

							// set up our select handler
							imageFrame.on( 'select', function() {
								var selection = imageFrame.state().get('selection');
								if ( ! selection ) {
									return;
								}

								// loop through the selected files
								selection.each( function( attachment ) {
									//console.log(attachment);
									var src = attachment.attributes.sizes.full.url;
									var id = attachment.id;

									$(selectedImage).attr('src', src);
									$(selectedInput).val(id);
									nextButton.show();
								} );
							});

							// open the frame
							imageFrame.open();

							return false;
						});

						// the remove image link, removes the image id from the hidden field and replaces the image preview
						$('.remove-fallback-image').click(function() {
							var $this = $(this);

							$($this.data('input')).val('');
							$($this.data('img')).attr('src', '');
							$this.hide();

							return false;
						});
					});
				})(jQuery);
			</script><?php
		}
	}

	public function render_publisher_select( $args ) {
		$publishers = \Bbgi\Module::get( 'experience-engine' )->get_publisher_list();

		?><select name="<?php echo esc_attr( $args['name'] ); ?>">
			<option value="">—</option>
			<?php foreach ( $publishers as $publisher ): ?>
				<option
					value="<?php echo esc_attr( $publisher['id'] ); ?>"
					<?php selected( $args['selected'], $publisher['id'] ); ?>>
					<?php echo esc_html( $publisher['title'] ); ?>
				</option>
			<?php endforeach; ?>
		</select><?php
	}

	public function render_ee_login( $args ) {

		?><select name="<?php echo esc_attr( $args['name'] ); ?>">
			<option value="">Login Enabled</option>
			<option
					value="disabled"
					<?php selected( $args['selected'], true ); ?>>
					Login Disabled
			</option>
		</select><?php
	}

	public function render_contest_show_dates( $args ) {
		?><select name="<?php echo esc_attr( $args['name'] ); ?>">
		<option value="hide"
				<?php selected( $args['selected'], 'hide' ); ?>
		>Hide</option>
		<option value="show"
				<?php selected( $args['selected'], 'show' ); ?>
		>Show</option>

		</select><?php
	}


	public function render_ad_lazy_loading_enabled( $args ) {
		?><select name="<?php echo esc_attr( $args['name'] ); ?>">
		<option value="on"
				<?php selected( $args['selected'], 'on' ); ?>
		>On</option>
		<option value="off"
				<?php selected( $args['selected'], 'off' ); ?>
		>Off</option>

		</select><?php
	}

	public function render_ad_rotation_enabled( $args ) {
		?><select name="<?php echo esc_attr( $args['name'] ); ?>">
		<option value="on"
				<?php selected( $args['selected'], 'on' ); ?>
		>On</option>
		<option value="off"
				<?php selected( $args['selected'], 'off' ); ?>
		>Off</option>

		</select><?php
	}

	public function render_feature_video_provider( $args ) {

		?><select name="<?php echo esc_attr( $args['name'] ); ?>">
		<option value="none"
				<?php selected( $args['selected'], 'none' ); ?>
		>None</option>
		<option value="stn"
				<?php selected( $args['selected'], 'stn' ); ?>
		>STN</option>
		</select><?php
	}
}
