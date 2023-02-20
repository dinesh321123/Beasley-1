<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
class 	MyAccount {
	function __construct()
	{
		add_action( 'init', array( $this, 'configuration_init' ), 1 );
		add_shortcode('cancel_account', 'render_cancel_account_button');

		// Register scripts
		// add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_scripts' ), 1 );
	}
	public function configuration_init() {
		$myaccount	= get_page_by_path( 'my-account', OBJECT );
		if ( !isset( $myaccount ) ) {
			$myaccount_title	= 'My Account';							// Post title
			$myaccount_content	= '';									// Post Description
//			 $myaccount_template	= USER_ACCOUNT_CONFIGURATION_PATH . 'templates/myaccount-template.php';			// Add template Name Here
			$myaccount_exist	= get_page_by_title( $myaccount_title );	// My Account Exist
			$myaccount_array	= array(
				'post_type'		=> 'page',
				'post_title'	=> $myaccount_title,
				'post_content'	=> $myaccount_content,
				'post_status'	=> 'publish',
				'post_author'	=> 1,
			);

			if ( !isset( $myaccount_exist->ID ) ) {
				$myaccount_id	= wp_insert_post($myaccount_array);
				if ( !empty( $myaccount_template ) ) {
					update_post_meta( $myaccount_id, '_wp_page_template', $myaccount_template );
				}
			}
		}
	}
	public static function register_scripts() {
		/* $min = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_script( 'user-account-configuration-script', USER_ACCOUNT_CONFIGURATION_URL . "assets/js/user-account-configuration{$min}.js", array( 'jquery' ), USER_ACCOUNT_CONFIGURATION_VERSION, true ); */
	}

	function render_cancel_account_button(): string
	{
		return "<div class='accountCancellation' />";
	}
}
new MyAccount();
