<?php

namespace GreaterMedia\LiveFyre;

class MediaWallShortCode {

	public $livefyre_options = null;
	public $counter = 0;
	public $rendered_wall;

	public $livefyre_params = array(
		'bodyFontSize',
		'bodyLineHeight',
		'buttonBorderColor',
		'buttonTextColor',
		'cardBackgroundColor',
		'displayNameColor',
		'fontFamily',
		'footerTextColor',
		'linkAttachmentBackgroundColor',
		'linkColor',
		'textColor',
		'titleFontSize',
		'titleLineHeight',
		'sourceLogoColor',
		'usernameColor',
		'network',
		'siteId',
		'articleId',
	);

	public $defaults   = array(
		'initial' => 9,
		'columns' => 3,
	);

	function register() {
		add_shortcode( 'media-wall', array( $this, 'render' ) );
		add_action( 'wp_footer', array( $this, 'on_footer' ) );

		wp_register_script(
			'livefyre_loader',
			$this->get_livefyre_loader(),
			array()
		);

		wp_register_script(
			'livefyre_media_wall_app',
			plugins_url( 'js/media_wall_app.js', GMR_LIVEFYRE_PLUGIN_FILE ),
			array( 'livefyre_loader', 'jquery' ),
			GMR_LIVEFYRE_VERSION
		);
	}

	function render( $params ) {
		$wall_id = $this->counter++;
		$params  = $this->parse( $params );
		$container = "<div id='media-wall-{$wall_id}'></div>";

		$this->rendered_wall = $wall_id;

		$wall_key = 'livefyre_media_wall_' . $wall_id;
		$wall_data = array( 'data' => $params );

		wp_localize_script(
			'livefyre_media_wall_app', $wall_key, $wall_data
		);

		return $container;
	}

	function parse( $params ) {
		if ( ! is_array( $params ) ) {
			$params = array();
		}

		$output_params = shortcode_atts( $this->defaults, $params );

		// allows pass-through parameters
		foreach ( $params as $name => $value ) {
			if ( $name === 'initial' || $name === 'columns' ) {
				$value = intval( $value );
			}

			$output_params[ $name ] = $value;
		}

		foreach ( $this->livefyre_params as $livefyre_param ) {
			$lower_name = strtolower( $livefyre_param );

			if ( array_key_exists( $lower_name, $output_params ) && $livefyre_param !== $lower_name ) {
				$output_params[ $livefyre_param ] = $output_params[ $lower_name ];
			}
		}

		return $output_params;
	}

	function on_footer() {
		if ( $this->did_render() ) {
			wp_localize_script(
				'livefyre_media_wall_app',
				'livefyre_media_wall_options',
				$this->get_media_wall_options()
			);

			wp_enqueue_script( 'livefyre_loader' );
			wp_enqueue_script( 'livefyre_media_wall_app' );
		}
	}

	function get_livefyre_loader() {
		$protocol = is_ssl() ? 'https' : 'http';
		return "{$protocol}://cdn.livefyre.com/Livefyre.js";
	}

	function did_render() {
		return ! is_null( $this->rendered_wall );
	}

	function get_media_wall_options() {
		$post = $this->get_current_post();

		return array(
			'data'             => array(
				'network_name' => $this->get_livefyre_option( 'network_name' ),
				'site_id'      => $this->get_livefyre_option( 'site_id' ),
				'article_id'   => strval( $post->ID ),
				'environment'  => $this->get_environment(),
				'wall'         => $this->rendered_wall,
			)
		);
	}

	function get_current_post() {
		global $post;

		if ( $post instanceof \WP_Post ) {
			return $post;
		} else {
			return null;
		}
	}

	function get_environment() {
		$network_name = $this->get_livefyre_option( 'network_name' );

		if ( strstr( $network_name, '-int-0' ) !== false ) {
			return 't402.livefyre.com';
		} else {
			return 'livefyre.com';
		}
	}

	// KLUDGE: Duplication
	function get_livefyre_options() {
		if ( is_null( $this->livefyre_options ) ) {
			$options = get_option( 'livefyre_settings' );
			$options = json_decode( $options, true );

			$this->livefyre_options = $options;
		}

		return $this->livefyre_options;
	}

	function get_livefyre_option( $name ) {
		$options = $this->get_livefyre_options();

		if ( array_key_exists( $name, $options ) ) {
			return $options[ $name ];
		} else {
			return '';
		}
	}

	function has_livefyre_option( $name ) {
		return $this->get_livefyre_option( $name ) !== '';
	}

}
