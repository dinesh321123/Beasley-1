<?php

class ContentRestrictedShortcodes {

	function register() {
		add_action(
			'admin_enqueue_scripts', array( $this, 'add_base_plugin' )
		);

		add_filter(
			'mce_external_plugins', array( $this, 'add_plugins' )
		);

		add_filter(
			'mce_buttons', array( $this, 'add_buttons' )
		);

		add_filter(
			'mce_css', array( $this, 'add_css' )
		);
	}

	function add_base_plugin() {
		if ( $this->can_add_base_plugin() ) {
			wp_enqueue_script(
				'jquery-date-format',
				$this->get_plugin_url( 'date.format/date-format' ),
				array( 'jquery' ),
				'0.1.0',
				true
			);

			wp_enqueue_script(
				'jquery-datetimepicker',
				$this->get_plugin_url( 'datetimepicker/jquery-datetimepicker' ),
				array( 'jquery' ),
				'0.1.0',
				true
			);

			wp_enqueue_script(
				'visual-shortcode',
				$this->get_plugin_url( 'visual-shortcode' ),
				array( 'jquery' ),
				'0.1.0',
				false
			);

			wp_enqueue_style(
				'visual-shortcode-admin',
				$this->get_css_url( 'visual-shortcode-admin' )
			);
		}
	}

	function can_add_base_plugin() {
		global $post;
		return is_admin() && ! empty( $post ) &&
			(
				post_type_supports( $post->post_type, 'age-restricted-content' ) ||
				post_type_supports( $post->post_type, 'login-restricted-content' ) ||
				post_type_supports( $post->post_type, 'time-restricted-content' )
			);
	}

	function add_plugins( $plugins ) {
		$plugins['ageRestricted']   = $this->get_plugin_url( 'age-restricted' );
		$plugins['timeRestricted']  = $this->get_plugin_url( 'time-restricted' );
		$plugins['loginRestricted'] = $this->get_plugin_url( 'login-restricted' );

		return $plugins;
	}

	function add_buttons( $buttons ) {
		$buttons[] = 'ageRestricted';
		$buttons[] = 'timeRestricted';
		$buttons[] = 'loginRestricted';

		return $buttons;
	}

	function add_css( $styles ) {
		$styles   = explode( ',', $styles );
		$styles[] = $this->get_css_url( 'visual-shortcode' );
		$styles[] = $this->get_css_url( 'datetimepicker/jquery-datetimepicker', 'js' );
		$styles   = implode( ',', $styles );

		return $styles;
	}

	/* helpers */
	function get_plugin_url( $name, $dir = 'js' ) {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$extension = '.js';
		} else {
			$extension = '.min.js';
		}

		return plugins_url( "{$dir}/{$name}{$extension}", VISUAL_SHORTCODE_PLUGIN );
	}

	function get_css_url( $name, $dir = 'css' ) {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$extension = '.css';
		} else {
			$extension = '.min.css';
		}

		return plugins_url( "{$dir}/{$name}{$extension}", VISUAL_SHORTCODE_PLUGIN );
	}

}
