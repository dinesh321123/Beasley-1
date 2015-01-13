<?php

namespace GreaterMedia\MyEmma;

class SettingsPage {

	function register() {
		return add_options_page(
			'MyEmma',
			'MyEmma',
			'manage_options',
			'myemma-settings',
			array( $this, 'render' )
		);
	}

	function render() {
		$this->render_scripts();
		$this->render_styles();

		include( $this->get_template_path() );
	}

	function render_scripts() {
		wp_enqueue_script(
			'myemma_settings',
			plugins_url( 'js/myemma_settings_page.js', GMR_GIGYA_PLUGIN_FILE ),
			array( 'wp_ajax_api', 'backbone', 'underscore' ),
			GMR_GIGYA_VERSION,
			true
		);

		$member_query_settings = get_option( 'member_query_settings' );
		$member_query_settings = json_decode( $member_query_settings, true );

		$meta = array(
			'data'                 => array(
				'ajax_url'         => admin_url( 'admin-ajax.php' ),
				'emma_account_id'  => $member_query_settings['emma_account_id'],
				'emma_public_key'  => $member_query_settings['emma_public_key'],
				'emma_private_key' => $member_query_settings['emma_private_key'],

				'change_myemma_settings_nonce' => wp_create_nonce( 'change_myemma_settings' ),
				'list_myemma_webhooks_nonce'   => wp_create_nonce( 'list_myemma_webhooks' ),
				'update_myemma_webhooks_nonce' => wp_create_nonce( 'update_myemma_webhooks' ),
				'add_myemma_group_nonce'       => wp_create_nonce( 'add_myemma_group' ),
				'remove_myemma_group_nonce'    => wp_create_nonce( 'remove_myemma_group' ),
				'update_myemma_group_nonce'    => wp_create_nonce( 'update_myemma_group' ),
			)
		);

		if ( array_key_exists( 'emma_webhook_auth_token', $member_query_settings ) ) {
			$meta['data']['emma_webhook_auth_token'] = $member_query_settings['emma_webhook_auth_token'];
		}

		if ( $meta['data']['emma_webhook_auth_token'] === '' ) {
			$meta['data']['emma_webhook_auth_token'] = md5( $_SERVER['HTTP_USER_AGENT'] . time() );
		}

		$groups = get_option( 'emma_groups' );
		if ( $groups !== false ) {
			$groups = json_decode( $groups, true );
			if ( ! is_array( $groups ) ) {
				$groups = array();
			}
		} else {
			$groups = array();
		}

		$meta['data']['emma_groups'] = $groups;

		wp_localize_script(
			'myemma_settings', 'myemma_settings', $meta
		);
	}

	function render_styles() {
		wp_enqueue_style(
			'myemma_settings_styles',
			plugins_url( 'css/myemma_settings_page.css', GMR_GIGYA_PLUGIN_FILE ),
			array( 'dashicons' ),
			GMR_GIGYA_VERSION
		);
	}

	function get_template_path() {
		return GMR_GIGYA_PATH . '/templates/myemma_settings.php';
	}

}
