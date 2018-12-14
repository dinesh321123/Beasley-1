<?php

namespace Bbgi\Integration;

class ExperienceEngine extends \Bbgi\Module {

	private static $_fields = array(
		'ee_host' => 'API host',
	);

	/**
	 * Registers module.
	 *
	 * @access public
	 */
	public function register() {
		add_action( 'wpmu_options', $this( 'show_network_settings' ) );
		add_action( 'update_wpmu_options', $this( 'save_network_settings' ) );
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
		?><h2>Experience Engine Settings</h2>
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
