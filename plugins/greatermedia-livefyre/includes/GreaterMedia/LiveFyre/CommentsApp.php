<?php

namespace GreaterMedia\LiveFyre;

use Livefyre\Livefyre;

class CommentsApp {

	public $livefyre_options = null;

	function register() {
		add_filter(
			'comments_template', array( $this, 'change_comments_template' ), 99
		);
	}

	function change_comments_template( $template_path ) {
		if ( ! $this->is_livefyre_configured() ) {
			return $template_path;
		}

		$comments_data = array(
			'ajax_url'                      => admin_url( 'admin-ajax.php' ),
			'get_livefyre_auth_token_nonce' => wp_create_nonce( 'get_livefyre_auth_token' ),
			'data'                          => $this->get_comments_data()
		);

		wp_enqueue_script(
			'livefyre_loader',
			$this->get_livefyre_loader(),
			array()
		);

		wp_enqueue_script(
			'livefyre_comments',
			plugins_url( 'js/comments_app.js', GMR_LIVEFYRE_PLUGIN_FILE ),
			array( 'livefyre_loader', 'jquery', 'wp_ajax_api', 'cookies-js' ),
			GMR_LIVEFYRE_VERSION
		);

		wp_localize_script(
			'livefyre_comments',
			'livefyre_comments_data',
			$comments_data
		);

		return $this->get_comments_template_path();
	}

	function get_livefyre_loader() {
		$protocol = is_ssl() ? 'https' : 'http';
		return "{$protocol}://cdn.livefyre.com/Livefyre.js";
	}

	function get_comments_template_path() {
		return GMR_LIVEFYRE_PATH . '/templates/comments.php';
	}

	function is_livefyre_configured() {
		$options = $this->get_livefyre_options();

		return
			$options !== false &&
			is_array( $options ) &&
			array_key_exists( 'network_name', $options ) &&
			array_key_exists( 'site_id', $options );
	}

	function get_livefyre_options() {
		if ( is_null( $this->livefyre_options ) ) {
			$options = get_option( 'livefyre_settings' );
			if ( $options !== false ) {
				$options = json_decode( $options, true );
			}

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

	function get_comments_data() {
		$current_post = $this->get_current_post();
		$data         = array();

		if ( $this->has_comments( $current_post ) ) {
			$data['livefyre_enabled'] = true;
			$data['livefyre_options'] = $this->options_for_post( $current_post );
			$data['tokens']           = $this->tokens_for_post( $current_post );
		} else {
			$data['livefyre_enabled'] = false;
		}

		return $data;
	}

	function get_current_post() {
		global $post;

		if ( $post instanceof \WP_Post ) {
			return $post;
		} else {
			return null;
		}
	}

	function has_comments( $post ) {
		return ! is_null( $post ) && ! is_preview();
	}

	function tokens_for_post( $post ) {
		$options = $this->get_livefyre_options();
		$builder = new TokenBuilder( $options );

		return $builder->tokens_for( $post );
	}

	function options_for_post( $post ) {
		$options = array(
			'network_name'  => $this->get_livefyre_option( 'network_name' ),
			'site_id'       => $this->get_livefyre_option( 'site_id' ),
			'article_id'    => strval( $post->ID ),
			'article_path'  => $this->url_to_path( get_permalink( $post->ID ) ),
			'article_title' => get_the_title( $post->ID ),
			'read_only'     => ! comments_open( $post->ID )
		);

		return $options;
	}

	function url_to_path( $url ) {
		$parts = parse_url( $url );
		return $parts['path'];
	}

}
