<?php

namespace GreaterMedia\LiveFyre\Settings;

class Page {

	public $options_key = 'livefyre_settings';
	public $defaults = array(
		'network_name' => '',
		'network_key'  => '',
		'site_id'      => '',
		'site_key'     => '',
	);

	function register() {
		return add_options_page(
			'LiveFyre Settings', 'LiveFyre', 'manage_options', 'livefyre_settings',
			array( $this, 'render' )
		);
	}

	function render() {
		wp_enqueue_script(
			'livefyre_settings',
			plugins_url( 'js/settings_page.js', GMR_LIVEFYRE_PLUGIN_FILE ),
			array( 'wp_ajax_api' ),
			GMR_LIVEFYRE_VERSION
		);

		$meta = array(
			'data' => array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'change_livefyre_settings_nonce' => wp_create_nonce(
					'change_livefyre_settings'
				)
			)
		);

		wp_localize_script(
			'livefyre_settings', 'livefyre_settings_data', $meta
		);

		$options      = $this->get_options();
		$network_name = $options['network_name'];
		$network_key  = $options['network_key'];
		$site_id      = $options['site_id'];
		$site_key     = $options['site_key'];

		include( $this->get_template_path() );
	}

	function get_template_path() {
		return GMR_LIVEFYRE_PATH . '/templates/settings.php';
	}

	function get_options() {
		$options = get_option(
			'livefyre_settings', json_encode( $this->defaults )
		);

		$options = json_decode( $options, true );
		$options = wp_parse_args( $options, $this->defaults );

		return $options;
	}

}
