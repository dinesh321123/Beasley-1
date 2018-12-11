<?php

namespace Bbgi\Integration;

class Firebase extends \Bbgi\Module {

	/**
	 * Registers module.
	 *
	 * @access public
	 */
	public function register() {
		add_action( 'admin_init', $this( 'register_settings' ), 9 );
		add_filter( 'bbgiconfig', $this( 'populate_settings' ) );
	}

	/**
	 * Registers Firebase settings.
	 *
	 * @access public
	 * @action admin_init
	 */
	public function register_settings() {
		$text_callback = array( $this, 'render_text_setting' );

		$fields = array(
			'projectId'   => 'Project ID',
			'apiKey'      => 'API Key',
			'authDomain'  => 'Auth Domain',
			'databaseURL' => 'Database URL',
		);

		add_settings_section( 'beasley_firebase', 'Firebase', '__return_false', 'media' );

		foreach ( $fields as $key => $label ) {
			$full_key = "beasley_firebase_{$key}";
			add_settings_field( $full_key, $label, $text_callback, 'media', 'beasley_firebase', $full_key );
			register_setting( 'media', $full_key, 'sanitize_text_field' );
		}
	}

	/**
	 * Renders a setting field.
	 *
	 * @access public
	 * @param string $name The field name.
	 */
	public function render_text_setting( $name ) {
		?><input type="text" class="regular-text" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( get_option( $name ) ); ?>"><?php
	}

	/**
	 * Populates settings array with Firebase information.
	 *
	 * @access public
	 * @filter firebase_settings
	 * @param array $settings The initial array of settings.
	 * @return array Updated array of settings.
	 */
	public function populate_settings( $settings ) {
		$settings['firebase'] =  array(
			'apiKey'      => get_option( 'beasley_firebase_apiKey' ),
			'authDomain'  => get_option( 'beasley_firebase_authDomain' ),
			'databaseURL' => get_option( 'beasley_firebase_databaseURL' ),
			'projectId'   => get_option( 'beasley_firebase_projectId' ),
		);

		return $settings;
	}

}
