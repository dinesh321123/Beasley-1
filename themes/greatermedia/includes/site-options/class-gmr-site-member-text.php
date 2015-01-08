<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class GreaterMediaSiteMemberText {

	const option_group = 'greatermedia_site_member_text';

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

	public function __construct() {
		// I don't do anything
	}

	protected function _init() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public function add_settings_page() {
		$this->_settings_page_hook = add_options_page( 'Member Page Text', 'Member Page Text', 'manage_options', 'greatermedia-member-text', array(
			$this,
			'render_settings_page'
		), '', 3 );
	}

	public function render_settings_page() {
		?>
		<form action="options.php" method="post" class="greatermedia-member-text-form" style="max-width: 550px;">
			<?php
			settings_fields( self::option_group );
			do_settings_sections( $this->_settings_page_hook );

			/**
			 * Allows adding additional settings sections here.
			 *
			 * Useful for the sections that are only enabled if theme support is enabled, we can conditionally add settings for each child theme.
			 */
			do_action( 'greatermedia-member-text-additional-settings' );

			submit_button( 'Submit' );
			?>
		</form>
	<?php
	}

	public function register_settings() {
		// Settings Section
		add_settings_section( self::option_group, 'Member Text', array(
			$this,
			'render_member_text_settings_section'
		), $this->_settings_page_hook );

		// Headlines & Messages
		register_setting( self::option_group, 'gmr_join_page_heading', 'sanitize_text_field' );
		register_setting( self::option_group, 'gmr_join_page_message', 'wp_kses_post' );

		register_setting( self::option_group, 'gmr_login_page_heading', 'sanitize_text_field' );
		register_setting( self::option_group, 'gmr_login_page_message', 'wp_kses_post' );

		register_setting( self::option_group, 'gmr_logout_page_heading', 'sanitize_text_field' );
		register_setting( self::option_group, 'gmr_logout_page_message', 'wp_kses_post' );

		register_setting( self::option_group, 'gmr_account_page_heading', 'sanitize_text_field' );
		register_setting( self::option_group, 'gmr_account_page_message', 'wp_kses_post' );

		register_setting( self::option_group, 'gmr_password_page_heading', 'sanitize_text_field' );
		register_setting( self::option_group, 'gmr_password_page_message', 'wp_kses_post' );

		register_setting( self::option_group, 'gmr_cookies_page_heading', 'sanitize_text_field' );
		register_setting( self::option_group, 'gmr_cookies_page_message', 'wp_kses_post' );

		/**
		 * Allows us to register extra settings that are not necessarily always present on all child sites.
		 */
		do_action( 'greatermedia-member-text-register-settings', self::option_group );
	}

	public function render_member_text_settings_section() {

		$join_page_heading = get_option( 'gmr_join_page_heading', '' );
		$join_page_message = get_option( 'gmr_join_page_message', '' );

		$login_page_heading = get_option( 'gmr_login_page_heading', '' );
		$login_page_message = get_option( 'gmr_login_page_message', '' );

		$logout_page_heading = get_option( 'gmr_logout_page_heading', '' );
		$logout_page_message = get_option( 'gmr_logout_page_message', '' );

		$account_page_heading = get_option( 'gmr_account_page_heading', '' );
		$account_page_message = get_option( 'gmr_account_page_message', '' );

		$password_page_heading = get_option( 'gmr_password_page_heading', '' );
		$password_page_message = get_option( 'gmr_password_page_message', '' );

		$cookies_page_heading = get_option( 'gmr_cookies_page_heading', '' );
		$cookies_page_message = get_option( 'gmr_cookies_page_message', '' );

		?>

		<h3>Join Page</h3>

		<div class="gmr__option">
			<label for="gmr_join_page_heading" class="gmr__option--label"><?php _e( 'Heading', 'greatermedia' ); ?></label>
			<input type="text" class="gmr__option--input" name="gmr_join_page_heading" id="gmr_join_page_header" value="<?php echo esc_attr( $join_page_heading ); ?>" />
		</div>

		<div class="gmr__option">
			<label for="gmr_join_page_message" class="gmr__option--label"><?php _e( 'Message', 'greatermedia' ); ?></label>
			<?php wp_editor( $join_page_message, 'gmr_join_page_message', $settings = array() ); ?>
		</div>

		<h3>Login Page</h3>

		<div class="gmr__option">
			<label for="gmr_login_page_heading" class="gmr__option--label"><?php _e( 'Heading', 'greatermedia' ); ?></label>
			<input type="text" class="gmr__option--input" name="gmr_login_page_heading" id="gmr_login_page_header" value="<?php echo esc_attr( $login_page_heading ); ?>" />
		</div>

		<div class="gmr__option">
			<label for="gmr_login_page_message" class="gmr__option--label"><?php _e( 'Message', 'greatermedia' ); ?></label>
			<?php wp_editor( $login_page_message, 'gmr_login_page_message', $settings = array() ); ?>
		</div>

		<h3>Logout Page</h3>

		<div class="gmr__option">
			<label for="gmr_logout_page_heading" class="gmr__option--label"><?php _e( 'Heading', 'greatermedia' ); ?></label>
			<input type="text" class="gmr__option--input" name="gmr_logout_page_heading" id="gmr_logout_page_header" value="<?php echo esc_attr( $logout_page_heading ); ?>" />
		</div>

		<div class="gmr__option">
			<label for="gmr_logout_page_message" class="gmr__option--label"><?php _e( 'Message', 'greatermedia' ); ?></label>
			<?php wp_editor( $logout_page_message, 'gmr_logout_page_message', $settings = array() ); ?>
		</div>

		<h3>Account Page</h3>

		<div class="gmr__option">
			<label for="gmr_account_page_heading" class="gmr__option--label"><?php _e( 'Heading', 'greatermedia' ); ?></label>
			<input type="text" class="gmr__option--input" name="gmr_account_page_heading" id="gmr_account_page_header" value="<?php echo esc_attr( $account_page_heading ); ?>" />
		</div>

		<div class="gmr__option">
			<label for="gmr_account_page_message" class="gmr__option--label"><?php _e( 'Message', 'greatermedia' ); ?></label>
			<?php wp_editor( $account_page_message, 'gmr_account_page_message', $settings = array() ); ?>
		</div>

		<h3>Forgot Password Page</h3>

		<div class="gmr__option">
			<label for="gmr_password_page_heading" class="gmr__option--label"><?php _e( 'Heading', 'greatermedia' ); ?></label>
			<input type="text" class="gmr__option--input" name="gmr_password_page_heading" id="gmr_password_page_header" value="<?php echo esc_attr( $password_page_heading ); ?>" />
		</div>

		<div class="gmr__option">
			<label for="gmr_password_page_message" class="gmr__option--label"><?php _e( 'Message', 'greatermedia' ); ?></label>
			<?php wp_editor( $password_page_message, 'gmr_password_page_message', $settings = array() ); ?>
		</div>

		<h3>Cookies Required Page</h3>

		<div class="gmr__option">
			<label for="gmr_cookies_page_heading" class="gmr__option--label"><?php _e( 'Heading', 'greatermedia' ); ?></label>
			<input type="text" class="gmr__option--input" name="gmr_cookies_page_heading" id="gmr_cookies_page_header" value="<?php echo esc_attr( $cookies_page_heading ); ?>" />
		</div>

		<div class="gmr__option">
			<label for="gmr_cookies_page_message" class="gmr__option--label"><?php _e( 'Message', 'greatermedia' ); ?></label>
			<?php wp_editor( $cookies_page_message, 'gmr_cookies_page_message', $settings = array() ); ?>
		</div>

		<hr />


	<?php
	}

}

GreaterMediaSiteMemberText::instance();
