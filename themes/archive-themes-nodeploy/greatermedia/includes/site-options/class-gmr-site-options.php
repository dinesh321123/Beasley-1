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
		// Settings Section
		add_settings_section( 'beasley_site_settings', 'Station Type', array( $this, 'render_site_settings_section' ), $page );
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
		register_setting( $group, 'gmr_livelinks_title', 'sanitize_text_field');
		register_setting( $group, 'gmr_newssite', 'esc_attr' );
		register_setting( $group, 'gmr_livelinks_more_redirect', 'esc_attr' );
		register_setting( $group, 'gmr_liveplayer_disabled', 'esc_attr' );
	}

	public function render_site_settings_section() {
		$news_site = get_option( 'gmr_newssite', '' );
		$livelinks_more = get_option( 'gmr_livelinks_more_redirect', '' );
		$liveplayer_disabled = get_option( 'gmr_liveplayer_disabled', '' );
		
		?><div class="gmr__option">
			<input type="checkbox" name="gmr_newssite" id="gmr_newssite" value="1" <?php checked( 1 == esc_attr( $news_site ) ); ?>><label for="gmr_newssite" class="gmr__option--label-inline"><?php _e( 'News/Sports Station', 'greatermedia' ); ?></label>
			<div class="gmr-option__field--desc"><?php _e( 'Check this box if this site is for a News or Sports Radio Station.', 'greatermedia' ); ?></div>
		</div>

		<hr />

		<h4>Live Player and Live Links</h4>

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

}

GreaterMediaSiteOptions::instance();
