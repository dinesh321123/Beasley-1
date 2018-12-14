<?php

namespace Bbgi\Integration;

class Firebase extends \Bbgi\Module {

	private static $_fields = array(
		'firebase_projectId'  => 'Project ID',
		'firebase_apiKey'     => 'API Key',
		'firebase_authDomain' => 'Auth Domain',
	);

	/**
	 * Registers module.
	 *
	 * @access public
	 */
	public function register() {
		add_filter( 'bbgiconfig', $this( 'populate_settings' ) );

		add_action( 'wpmu_options', $this( 'show_network_settings' ) );
		add_action( 'update_wpmu_options', $this( 'save_network_settings' ) );
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
			'apiKey'     => get_site_option( 'firebase_apiKey' ),
			'authDomain' => get_site_option( 'firebase_authDomain' ),
			'projectId'  => get_site_option( 'firebase_projectId' ),
		);

		return $settings;
	}

	/**
	 * Saves network settings.
	 *
	 * @access public
	 * @action update_wpmu_options
	 */
	public function save_network_settings() {
		foreach ( self::$_fields as $id => $label ) {
			$value = filter_input( INPUT_POST, $id );
			$value = sanitize_text_field( $value );
			update_site_option( $id, $value );
		}
	}

	/**
	 * Shows network settings
	 *
	 * @access public
	 * @action wpmu_options
	 */
	public function show_network_settings() {
		?><h2>Firebase Settings</h2>
		<table id="menu" class="form-table">
			<?php foreach ( self::$_fields as $id => $label ) : ?>
				<tr>
					<th scope="row"><?php echo esc_html( $label ); ?></th>
					<td>
						<input type="text" class="regular-text" name="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( get_site_option( $id ) ); ?>">
					</td>
				</tr>
			<?php endforeach; ?>
		</table><?php
	}

}
