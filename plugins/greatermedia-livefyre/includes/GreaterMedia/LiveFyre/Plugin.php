<?php

namespace GreaterMedia\LiveFyre;

use Livefyre\Livefyre;

class Plugin {

	function enable() {
		add_action( 'admin_menu', array( $this, 'initialize_admin_menu' ) );
		add_action( 'init', array( $this, 'initialize' ) );
		add_action( 'update_gigya_account', array( $this, 'sync_livefyre_user' ) );
		add_action( 'gigya_login', array( $this, 'sync_livefyre_user' ) );

		if ( $this->is_ajax_request() ) {
			$this->register_ajax_handlers();
		}
	}

	function initialize() {
		$comments_app = new CommentsApp();
		$comments_app->register();

		$shortcode = new MediaWallShortCode();
		$shortcode->register();
	}

	function initialize_admin_menu() {
		$settings_page = new Settings\Page();
		$settings_page->register();

		remove_menu_page( 'edit-comments.php' );

		$livefyre_admin_url = $this->get_livefyre_admin_url();
		if ( $this->can_moderate_comments() && $livefyre_admin_url !== '' ) {
			$this->add_comments_mod_menu( $livefyre_admin_url );
		}
	}

	function can_moderate_comments() {
		return current_user_can( 'moderate_comments' ) && current_user_can( 'import' );
	}

	function add_comments_mod_menu( $url ) {
		add_management_page(
			'LiveFyre ModQ',
			'LiveFyre ModQ',
			'manage_options',
			'livefyre-admin-comments'
		);

		global $submenu;
		$settings_menu = $submenu['tools.php'];

		foreach ( $settings_menu as $key => $menu ) {
			if ( $menu[2] === 'livefyre-admin-comments' ) {
				$submenu['tools.php'][ $key ] = array(
					'LiveFyre ModQ', 'manage_options', $url,
				);
			}
		}
	}

	function register_ajax_handlers() {
		$handlers   = array();
		$handlers[] = new Ajax\ChangeLiveFyreSettings();
		$handlers[] = new Ajax\GetLiveFyreAuthToken();
		$handlers[] = new Ajax\PullGigyaProfile();

		foreach ( $handlers as $handler ) {
			$handler->register();
		}
	}

	function is_ajax_request() {
		return defined( 'DOING_AJAX' ) && DOING_AJAX;
	}

	function get_livefyre_admin_url() {
		$settings = $this->get_livefyre_settings();

		if ( $settings ) {
			$network_name  = $settings['network_name'];
			$network_admin = str_replace( 'fyre.co', 'admin.fyre.co', $network_name );
			return "https://$network_admin/v3/modq";
		} else {
			return '';
		}
	}

	function get_livefyre_settings() {
		$settings = get_option( 'livefyre_settings' );
		$settings = json_decode( $settings, true );

		return $settings;
	}

	function sync_livefyre_user( $gigya_user_id ) {
		$gigya_user_id = rtrim( base64_encode( $gigya_user_id ), '=' );
		$settings      = $this->get_livefyre_settings();
		$network_name  = $settings['network_name'];
		$network_key   = $settings['network_key'];

		try {
			$network = Livefyre::getNetwork( $network_name, $network_key );
			$network->syncUser( $gigya_user_id );
		} catch ( \Exception $e ) {
			error_log( "Failed to sync livefyre user: $gigya_user_id" );
		}
	}

}
