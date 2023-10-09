<?php 
if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
class SlimmerMenuButton {
    function __construct()
    {
        add_action( 'init', array( $this, 'wp_init_morebuttom' ), 1 );
    }
    public function wp_init_morebuttom() {
        add_action( 'wp_enqueue_scripts', array( $this, 'more_button_scripts' ), 1 );
    }
    public function more_button_scripts() {
        //Script for front end
       
        $morebutton_nonce = wp_create_nonce( 'slimmer_menu-ajax-nonce' );
        $postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';      
        // wp_register_script('slimmer_menu', GENERAL_SETTINGS_CPT_URL . 'assets/js/slimmer-menu-menu'. $postfix .'.js', array('jquery'), '1.0.1');
        wp_register_script('slimmer_menu', GENERAL_SETTINGS_CPT_URL . 'assets/js/slimmer-menu.js', array('jquery'), '1.0.2');
        wp_localize_script(
            'slimmer_menu',
            'slimmer_menu',
            array('nonce'    => $morebutton_nonce
                )
        );

        wp_enqueue_script('slimmer_menu');
    }
}
new SlimmerMenuButton();