<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
class NewsletterSignupForm {
	function __construct()
	{
		add_action( 'init', array( $this, 'wp_init_nsf' ), 1 );
	}
	public function wp_init_nsf() {
		add_action( 'wp_enqueue_scripts', array( $this, 'nsf_register_scripts' ), 1 );
		add_shortcode( 'nsf-show', array( $this, 'nsf_function' ) );

	}
	public function nsf_register_scripts() {
		//Script for front end
		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

		wp_register_style('nsf-style',GENERAL_SETTINGS_CPT_URL . "assets/css/newsletter-signup-form". $postfix .".css", array(), GENERAL_SETTINGS_CPT_VERSION, 'all');
		wp_enqueue_style('nsf-style');

		wp_register_script('nsf-script', GENERAL_SETTINGS_CPT_URL . 'assets/js/newsletter-signup-form'. $postfix .'.js', array('jquery'), '1.0');

        wp_enqueue_script('nsf-script');
	}

	public function nsf_function() {

		$html = '';
		$html .= '<div class="nsf-container" id="root">';
			$html .= '<div class="nsf-image-container" >';
				$html .= $this->ee_the_subheader_logo_html('desktop', 154, 88);
			$html .= '</div>';
			$html .= '<div class="nsf-form-container">';
				$html .= '<h1 class="nsf-header">Join the Family</h1>';
				$html .= '<h2 class="nsf-subheader">Get Our Latest Articles in Your Inbox</h2>';
				$html .= '<form id="nsf-form" class="nsf-form" name="nsf_form" action="" method="POST">';
					$html .= '<div class="nsf-input-container">';
						$html .= '<div class="input-label"><label>First Name</label><span class="nsf-name-error">required</span></div>';
						$html .= '<div class="input-field"><input type="text" name="nsf_first_name" class="nsf-first-name" /></div>';
					$html .= '</div>';
					$html .= '<div class="nsf-input-container">';
						$html .= '<div class="input-label"><label>Last Name</label></div>';
						$html .= '<div class="input-field"><input type="text" name="nsf_last_name" class="nsf-last-name" /></div>';
					$html .= '</div>';
					$html .= '<div class="nsf-input-container">';
						$html .= '<div class="input-label"><label>Email</label><span class="nsf-email-error">required</span></div>';
						$html .= '<div class="input-field"><input type="text" name="nsf_email" class="nsf-email" /><span class="nsf-email-error-msg"></span></div>';
					$html .= '</div>';
					$html .= '<div class="nsf-action-container">';
						$html .= '<button class="nsf-form-submit" type="submit">Subscribe</button>';
					$html .= '</div>';
				$html .= '</form>';
			$html .= '</div>';
		$html .= '</div>';
		return $html;

	}

	public function ee_the_subheader_logo_html( $mobile_or_desktop, $base_w = 150, $base_h = 150 ) {
	    $html = '';
		$field_name = 'ee_subheader_' . $mobile_or_desktop . '_logo';
	    $atag_class_name = $mobile_or_desktop . '-subheader-logo-link';
		$site_logo_id = get_option( $field_name, 0 );
		if ( $site_logo_id ) {
			$site_logo = bbgi_get_image_url( $site_logo_id, $base_w, $base_h, false );
			if ( $site_logo ) {
				$alt = get_bloginfo( 'name' ) . ' | ' . get_bloginfo( 'description' );
				$site_logo_2x = bbgi_get_image_url( $site_logo_id, 2 * $base_w, 2 * $base_h, false );
				$html .= '<a href="'.esc_url( home_url() ). '" class="'. $atag_class_name. '" rel="home" itemprop="url">';
				$html .= '<img src="'.esc_url( $site_logo ).'" srcset="'.esc_url( $site_logo_2x ).' 2x" alt="'.esc_attr( $alt ).'" class="custom-logo" itemprop="logo">';
				$html .= '</a>';
			}
		}
		return $html;
	}

}
new NewsletterSignupForm();