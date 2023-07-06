<?php 

class NewsLetterSignup {

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	function __construct()
	{
		add_action( 'admin_init', array( $this, 'wp_admin_init' ), 1 );
	}

	public function wp_admin_init() {
		add_action( 'admin_enqueue_scripts',array($this, 'newslettersignup_tinymce_enqueue_scripts' ) );
		add_filter( 'mce_external_plugins',array($this, 'newslettersignup_add_buttons' ) );
		add_filter( 'mce_buttons',array($this, 'newslettersignup_register_buttons' ) );		
	}

	public function newslettersignup_tinymce_enqueue_scripts() {
		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_script(
			'newslettersignup-tinymce-scripts',
			GFF_URL.'/assets/js/newslettersignup-tinymce'.$postfix.'.js',
			array('jquery'),
			'1.0.0',
			true
		);
		wp_localize_script( 'newslettersignup-tinymce-scripts', 'tinyMCE_object_NSF', array(
			'button_name' => esc_html__('', 'newslettersignup'),
			'button_title' => esc_html__('Newsletter signup form', 'newslettersignup'),
			'image' =>GFF_URL.'/assets/image/mail.png',
		));
	}

	public function newslettersignup_add_buttons( $plugin_array ) {
		$plugin_array['newslettersignup-button'] = GFF_URL.'/assets/js/newslettersignup-tinymce.js';
        return $plugin_array;
    }

	public function newslettersignup_register_buttons( $buttons ) {
		array_push( $buttons, 'newslettersignup-button' );
        return $buttons;
    }

	
}
new NewsLetterSignup();