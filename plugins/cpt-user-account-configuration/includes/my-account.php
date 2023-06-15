<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
class MyAccount {
	function __construct(){

		add_action( 'init', array( $this, 'configuration_init' ), 1 );
		add_shortcode('cancel_account', [$this,'render_cancel_account_button']);
	
	}

	public function configuration_init() {
		$myaccount	= get_page_by_path( 'my-account', OBJECT );
		if ( !isset( $myaccount ) ) {
			$myaccount_title	= 'My Account';							// Post title
			$myaccount_content	= '';									// Post Description
			$myaccount_template	= 'templates/myaccount.php';			// Add template Name Here
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

	function render_cancel_account_button() {
		$html = '<div class="accountCancellation"></div>';
		return $html;
	}

}
new MyAccount();
