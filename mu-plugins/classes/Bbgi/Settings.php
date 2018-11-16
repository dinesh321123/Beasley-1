<?php

namespace Bbgi;

class Settings extends \Bbgi\Module {

	const option_group = 'greatermedia_site_options';

	/**
	 * Contains the slug of the settings page once it's registered
	 *
	 * @access protected
	 * @var string
	 */
	protected $_settings_page_hook;

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		add_action( 'admin_menu', $this( 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Adds settings page.
	 *
	 * @access public
	 * @action admin_menu
	 */
	public function add_settings_page() {
		$this->_settings_page_hook = add_options_page( 'Station Settings', 'Station Settings', 'manage_options', 'greatermedia-settings', array( $this, 'render_settings_page' ) );
	}

	/**
	 * Renders settings page.
	 *
	 * @access public
	 */
	public function render_settings_page() {
		echo '<form action="options.php" method="post" style="max-width:750px;">';
			settings_fields( self::option_group );
			do_settings_sections( $this->_settings_page_hook );
			submit_button( 'Submit' );
		echo '</form>';
	}

	/**
	 * Registers settings.
	 *
	 * @access public
	 * @action admin_init
	 */
	public function register_settings() {
		/**
		 * Allows us to register extra settings that are not necessarily always present on all child sites.
		 */
		do_action( 'bbgi_register_settings', self::option_group, $this->_settings_page_hook );
	}

}
