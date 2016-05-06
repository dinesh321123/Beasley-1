<?php

namespace GreaterMedia\Gigya;

class ProfilePage {

	public $allowed_pages = array(
		'join',
		'login',
		'logout',
		'account',
		'forgot-password',
		'cookies-required',
		'reset-password',
		'verify-email',
	);

	public function register() {
		add_rewrite_rule( '^members/([^/]*)$', 'index.php?profile_page=$matches[1]', 'top' );
		add_rewrite_tag( '%profile_page%', '([^&]+)' );
		add_action(
			'template_include', array( $this, 'render_if_profile_page' ), 99
		);
		add_filter( 'body_class', array( $this, 'add_profile_page_to_body_class' ), null, 2 );
		add_action(
			'wp_title', array( $this, 'change_page_title' ), 99
		);
	}

	public function change_page_title( $title ) {
		$profile_page = get_query_var( 'profile_page' );

		if ( $profile_page !== '' ) {
			return "Members - $profile_page" ;
		} else {
			return $title;
		}
	}

	public function render_if_profile_page( $template ) {
		$profile_page = get_query_var( 'profile_page' );

		if ( $profile_page !== '' ) {
			$endpoint  = $this->get_profile_endpoint();
			$page_path = "/{$endpoint}/{$profile_page}";

			return $this->render( $page_path, $template );
		}

		return $template;
	}

	/**
	 * Add classes to <body> to make profile pages easier to style.
	 *
	 * Adds a generic 'profile-page' class.
	 * Adds a class specific for the type of profile page that is being viewed.
	 *
	 * @param $classes array of classes to add to <body>
	 * @param $class
	 * @return array
	 */
	public function add_profile_page_to_body_class( $classes, $class ) {

		$profile_page = get_query_var( 'profile_page' );

		if ( isset( $profile_page ) && ! empty( $profile_page ) ) {
			$classes[] = 'profile-page';
			$classes[] = sanitize_html_class( 'profile-page-' . $profile_page );
		}

		return $classes;
	}

	public function render( $page_path, $template ) {
		$page_name        = $this->get_profile_page_name( $page_path );
		$profile_template = $this->get_profile_page_template( $page_name );

		if ( ! is_null( $profile_template ) ) {
			status_header( 200 );

			$this->load_scripts( $page_name );
			$this->load_styles();

			return $profile_template;
		} else {
			return $template;
		}
	}

	public function load_scripts( $page_name ) {
		$api_key = $this->get_gigya_api_key();
		$postfix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.js' : '.min.js';

		if ( $api_key === '' ) {
			error_log( 'Fatal Error: Gigya API Key not found.' );
			return;
		}

		wp_enqueue_script(
			'geodata',
			plugins_url( "js/geodata{$postfix}", GMR_GIGYA_PLUGIN_FILE ),
			array(),
			GMR_GIGYA_VERSION,
			true
		);

		wp_enqueue_script(
			'country-region-selector',
			plugins_url( "js/country_state_selector{$postfix}", GMR_GIGYA_PLUGIN_FILE ),
			array(),
			GMR_GIGYA_VERSION,
			true
		);

		wp_enqueue_script(
			'gigya_config',
			plugins_url( 'js/gigya_config.js', GMR_GIGYA_PLUGIN_FILE ),
			array(),
			GMR_GIGYA_VERSION,
			true
		);

		wp_enqueue_script(
			'gigya_socialize',
			"https://cdns.gigya.com/JS/gigya.js?apiKey={$api_key}",
			array( 'jquery', 'cookies-js', 'underscore', 'gigya_config' ),
			GMR_GIGYA_VERSION,
			true
		);

		wp_enqueue_script(
			'gigya_profile',
			plugins_url( 'js/gigya_profile.js', GMR_GIGYA_PLUGIN_FILE ),
			array( 'gigya_socialize', 'wp_ajax_api' ),
			GMR_GIGYA_VERSION,
			true
		);

		$meta = array(
			'ajax_url'               => admin_url( 'admin-ajax.php' ),
			'register_account_nonce' => wp_create_nonce( 'register_account' ),
			'update_account_nonce'   => wp_create_nonce( 'update_account' ),
			'gigya_login_nonce'      => wp_create_nonce( 'gigya_login' ),
			'reset_password_nonce'   => wp_create_nonce( 'reset_password' ),
			'current_page'           => $page_name,

			'join_header' => get_option( 'gmr_join_page_heading', '' ),
			'join_message' => get_option( 'gmr_join_page_message', '' ),

			'login_header' => get_option( 'gmr_login_page_heading', '' ),
			'login_message' => get_option( 'gmr_login_page_message', '' ),

			'logout_header' => get_option( 'gmr_logout_page_heading', '' ),
			'logout_message' => get_option( 'gmr_logout_page_message', '' ),

			'forgot-password_header' => get_option( 'gmr_password_page_heading', '' ),
			'forgot-password_message' => get_option( 'gmr_password_page_message', '' ),

			'account_header' => get_option( 'gmr_account_page_heading', '' ),
			'account_message' => get_option( 'gmr_account_page_message', '' ),

			'cookies-required_header' => get_option( 'gmr_cookies_page_heading', '' ),
			'cookies-required_message' => get_option( 'gmr_cookies_page_message', '' ),
		);

		wp_localize_script(
			'gigya_profile', 'gigya_profile_meta', $meta
		);
	}

	public function load_styles() {
		wp_enqueue_style(
			'gigya_profile',
			get_template_directory_uri() . '/profile/profile.css',
			array(),
			GMR_GIGYA_VERSION,
			'all'
		);
	}

	/* helpers */
	public function get_current_page_path() {
		// TODO: make this multisite-subdir safe
		return parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
	}

	public function is_user_on_profile_page() {
		$path = $this->get_current_page_path();
		return $this->is_profile_page( $path );
	}

	public function is_profile_page( $path ) {
		$endpoint = $this->get_profile_endpoint();
		return strpos( $path, "/{$endpoint}/" ) === 0;
	}

	public function get_profile_page_name( $page_path ) {
		$page_parts = explode( '/', $page_path );

		if ( array_key_exists( 2, $page_parts ) ) {
			return $page_parts[2];
		} else {
			return 'login';
		}
	}

	public function get_profile_page_template( $page_name ) {
		if ( in_array( $page_name, $this->allowed_pages ) ) {
			return locate_template( array( 'profile/profile.php' ) );
		} else {
			return null;
		}
	}

	public function get_member_query_settings() {
		$settings = get_option( 'member_query_settings' );

		if ( $settings !== false ) {
			return json_decode( $settings, true );
		} else {
			return array();
		}
	}

	public function get_gigya_api_key() {
		$settings = $this->get_member_query_settings();

		if ( array_key_exists( 'gigya_api_key', $settings ) ) {
			return $settings['gigya_api_key'];
		} else {
			return '';
		}
	}

	public function get_profile_endpoint() {
		return ProfilePath::get_instance()->endpoint;
	}

}
