<?php


/**
 * Class GM_TinyMCE
 *
 * Hooks into TinyMCE to provide a custom style sheet along with additional styles available to editors in TinyMCE
 *
 */
class GM_TinyMCE {

	public function __construct(){

		add_action( 'after_setup_theme', array( $this, 'tinymce_styles' ) );
		add_filter( 'mce_buttons_2', array( $this, 'mce_buttons' ) );
		add_filter( 'tiny_mce_before_init', array( $this, 'insert_formats' ) );

	}

	/**
	 * Function to add custom TinyMCE editor styles
	 */
	public function tinymce_styles() {
		add_editor_style( 'assets/css/gm_tinymce.css' );
	}

	/**
	 * Callback function to insert 'styleselect' into the $buttons array
	 *
	 * @param $buttons
	 *
	 * @return mixed
	 */
	public function mce_buttons( $buttons ) {
		array_unshift( $buttons, 'styleselect' );
		return $buttons;
	}

	/**
	 * Callback function to filter the MCE settings and add additional styles
	 *
	 * @param $init_array
	 *
	 * @return mixed
	 */
	public function insert_formats( $init_array ) {

		$style_formats = array(
			array(
				'title' => '.gm-test-class',
				'block' => 'blockquote',
				'classes' => 'gm-test-class',
				'wrapper' => true,

			),
			array(
				'title' => '⇠.rtl',
				'block' => 'blockquote',
				'classes' => 'gm-rtl',
				'wrapper' => true,
			),
			array(
				'title' => '.ltr⇢',
				'block' => 'blockquote',
				'classes' => 'gm-ltr',
				'wrapper' => true,
			),
		);
		$init_array['style_formats'] = json_encode( $style_formats );

		return $init_array;

	}

}

new GM_TinyMCE();