<?php

namespace GreaterMedia\Gigya;

class ProfilePage {

	public $allowed_pages = array(
		'register',
		'forgot-password',
		'login',
		'logout',
		'settings',
		'cookies-required',
	);

	public function register() {
		add_action(
			'template_include', array( $this, 'render_if_profile_page' ), 99
		);

		add_action(
			'wp_title', array( $this, 'change_page_title' ), 10
		);
	}

	public function change_page_title( $title ) {
		$page_path = $this->get_current_page_path();

		if ( is_404() && $this->is_profile_page( $page_path ) ) {
			$page_name = $this->get_profile_page_name( $page_path );

			return "Profile - $page_name" ;
		} else {
			return $title;
		}
	}

	public function render_if_profile_page( $template ) {
		$page_path = $this->get_current_page_path();

		if ( $this->is_profile_page( $page_path ) ) {
			return $this->render( $page_path, $template );
		}

		return $template;
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

		if ( $api_key === '' ) {
			error_log( 'Fatal Error: Gigya API Key not found.' );
			return;
		}

		wp_enqueue_script(
			'gigya_socialize',
			"http://cdn.gigya.com/JS/gigya.js?apiKey={$api_key}",
			array( 'jquery', 'cookies-js', 'underscore' ),
			GMR_GIGYA_VERSION,
			true
		);

		wp_enqueue_script(
			'gigya_profile',
			plugins_url( 'js/gigya_profile.js', GMR_GIGYA_PLUGIN_FILE ),
			array( 'gigya_socialize' ),
			GMR_GIGYA_VERSION,
			true
		);

		$meta = array(
			'ajax_url'     => admin_url( 'admin-ajax.php' ),
			'current_page' => $page_name,
		);

		wp_localize_script(
			'gigya_profile', 'gigya_profile_meta', $meta
		);
	}

	public function load_styles() {
		wp_enqueue_style(
			'gigya_profile',
			get_stylesheet_directory_uri() . '/profile/profile.css',
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
		return strpos( $path, '/profile/' ) === 0;
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
			return get_stylesheet_directory() . "/profile/{$page_name}.php";
		} else {
			return null;
		}
	}

	public function get_gigya_api_key() {
		$settings = get_option( 'member_query_settings' );

		if ( $settings !== false ) {
			$settings = json_decode( $settings, true );
			if ( array_key_exists( 'gigya_api_key', $settings ) ) {
				return $settings['gigya_api_key'];
			} else {
				return '';
			}
		} else {
			return '';
		}
	}

}
