<?php

namespace GreaterMedia\Gigya;

class SettingsPage {

	function register() {
		$page_id = add_options_page(
			'Gigya',
			'Gigya',
			'manage_options',
			'gigya-settings',
			array( $this, 'render' )
		);
	}

	function render() {
		wp_enqueue_script(
			'member_query_settings',
			plugins_url( 'js/settings_page.js', GMR_GIGYA_PLUGIN_FILE ),
			array( 'wp_ajax_api' ),
			GMR_GIGYA_VERSION
		);

		$meta = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		);

		wp_localize_script(
			'member_query_settings', 'member_query_settings', $meta
		);

		$options                 = $this->get_options();
		$gigya_api_key           = $options['gigya_api_key'];
		$gigya_secret_key        = $options['gigya_secret_key'];
		$gigya_auth_screenset    = $options['gigya_auth_screenset'];
		$gigya_account_screenset = $options['gigya_account_screenset'];
		$emma_account_id         = $options['emma_account_id'];
		$emma_public_key         = $options['emma_public_key'];
		$emma_private_key        = $options['emma_private_key'];

		include( $this->get_template_path() );
	}

	function get_template_path() {
		return GMR_GIGYA_PATH . '/templates/settings.php';
	}

	function get_options() {
		$defaults = array(
			'gigya_api_key'           => '',
			'gigya_secret_key'        => '',
			'emma_account_id'         => '',
			'emma_public_key'         => '',
			'emma_private_key'        => '',
			'gigya_auth_screenset'    => '',
			'gigya_account_screenset' => '',
		);

		$options = get_option( 'member_query_settings', json_encode( $defaults ) );
		return json_decode( $options, true );
	}

}
