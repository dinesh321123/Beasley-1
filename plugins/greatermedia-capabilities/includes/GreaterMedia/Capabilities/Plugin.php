<?php

namespace GreaterMedia\Capabilities;

require_once __DIR__ . '/CapabilitiesLoader.php';

class Plugin {

	static public $instance;
	static public function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Plugin();
		}

		return self::$instance;
	}

	function enable() {

	}

	function activate() {
		$this->load_capabilities();
	}

	function deactivate() {
		$this->unload_capabilities();
	}

	function change_capabilities( $action ) {
		$post_types = $this->get_custom_post_types();
		$loader     = new CapabilitiesLoader();

		foreach ( $post_types as $post_type ) {
			if ( $action === 'load' ) {
				$loader->load( $post_type );
			} else {
				$loader->unload( $post_type );
			}
		}
	}

	function load_capabilities() {
		$this->change_capabilities( 'load' );
	}

	function unload_capabilities() {
		$this->change_capabilities( 'unload' );
	}

	function get_custom_post_types() {
		return array(
			'gmr-live-link',
			'announcement',
			'gmr_album',
			'gmr_gallery',
			'contest',
			'contest_entry',
			'listener_submissions',
			'survey',
			'survey_response',
			'show',
			'show-episode',
			'podcast',
			'episode',
			'advertiser',
			'gmr_closure',
			'content-kit',
			'member_query',
			'subscription',
		);
	}

}
