<?php

class NewsLetterSignup {

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	function __construct()
	{
		add_action( 'wp_loaded', array( $this, 'load_newslettersignup' ), 0 );
		add_action( 'admin_init', array( $this, 'wp_admin_init' ), 1 );
	}
	public function load_newslettersignup(){
		$roles = [ 'administrator' ];

		foreach ( $roles as $role ) {
			$role_obj = get_role( $role );

			if ( is_a( $role_obj, \WP_Role::class ) ) {
				$role_obj->add_cap( 'manage_newsletter_signup_editor_icon', false );
			}
		}
	}

	public function wp_admin_init() {
		if(current_user_can('manage_newsletter_signup_editor_icon')) {
			add_action('admin_enqueue_scripts', array($this, 'newslettersignup_tinymce_enqueue_scripts'));
			add_filter('mce_external_plugins', array($this, 'newslettersignup_add_buttons'));
			add_filter('mce_buttons', array($this, 'newslettersignup_register_buttons'));
		}
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
		$logo = get_option('ee_newsletter_logo'); 
		if(empty($logo)){
			$logo =  get_option('gmr_site_logo');			
		}
		$site_logo = bbgi_get_image_url( $logo, 150, 150, false );
		$subscription_attributes = get_option('nsf_subscription_attributes'); ;
		$subscription_id = get_option('nsf_subscription_ID'); ;


		wp_localize_script( 'newslettersignup-tinymce-scripts', 'tinyMCE_object_NSF', array(
			'button_name' => esc_html__('', 'newslettersignup'),
			'button_title' => esc_html__('Newsletter signup form', 'newslettersignup'),
			'image' =>GFF_URL.'/assets/image/mail.png',
			'logo' => $site_logo,
			'subscription_attributes' => $subscription_attributes,
			'subscription_id'=>$subscription_id,
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
