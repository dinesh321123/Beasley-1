<?php

namespace GreaterMedia\LiveFyre;

class Plugin {

	function enable() {
		add_action( 'admin_menu', array( $this, 'initialize_admin_menu' ) );
		add_action( 'init', array( $this, 'initialize' ) );

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
	}

	function register_ajax_handlers() {
		$handlers   = array();
		$handlers[] = new Ajax\ChangeLiveFyreSettings();
		$handlers[] = new Ajax\GetLiveFyreAuthToken();

		foreach ( $handlers as $handler ) {
			$handler->register();
		}
	}

	function is_ajax_request() {
		return defined( 'DOING_AJAX' ) && DOING_AJAX;
	}

}
