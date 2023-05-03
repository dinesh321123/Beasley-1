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
		add_action( 'bbgi_register_settings', array( $this, 'nsf_register_settings' ) , 10, 2 );
	}
	public function nsf_register_scripts() {
		//Script for front end
		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

		wp_register_style('nsf-style',GENERAL_SETTINGS_CPT_URL . "assets/css/newsletter-signup-form". $postfix .".css", array(), GENERAL_SETTINGS_CPT_VERSION, 'all');
		wp_enqueue_style('nsf-style');

		wp_register_script('nsf-script', GENERAL_SETTINGS_CPT_URL . 'assets/js/newsletter-signup-form'. $postfix .'.js', array('jquery'), '1.0');

        wp_enqueue_script('nsf-script');
	}

	public function nsf_function($attr) {

		global $nsf_output_hide;

		$attr = shortcode_atts(
			array(
				'nsf_label' => '',
				'nsf_description' => '',
				'nsf_subscription_attributes' => '',
				'nsf_subscription_id' => '',
				'nsf_mailing_list_name' => '',
				'nsf_mailing_list_description' => '',
				'nsf_template_token' => '',
			), $attr );

		if ( !$nsf_output_hide ) {

			$html = '';
			$hidden_fields = '';

			$nsf_label						= $attr['nsf_label'] != '' ? sanitize_text_field($attr['nsf_label']) : (get_option('nsf_label') != '' ? get_option('nsf_label') : 'Join the Family');
			$nsf_description				= $attr['nsf_description'] != '' ? sanitize_text_field($attr['nsf_description']) : (get_option('nsf_description') != '' ? get_option('nsf_description') : 'Get Our Latest Articles in Your Inbox');
			$nsf_subscription_attributes	= get_option('nsf_subscription_attributes');
			$nsf_subscription_ID			= get_option('nsf_subscription_ID');
			$nsf_mailing_list_name			= get_option('nsf_mailing_list_name');
			$nsf_mailing_list_description	= get_option('nsf_mailing_list_description');
			$nsf_template_token				= get_option('nsf_template_token');

			$hidden_fields .= $attr['nsf_subscription_attributes'] != '' ? '<input type="hidden" name="nsf_subscription_attributes" value="'.esc_attr($attr['nsf_subscription_attributes']).'">' : ($nsf_subscription_attributes != '' ? '<input type="hidden" name="nsf_subscription_attributes" value="'.$nsf_subscription_attributes.'">' : '');

			$hidden_fields .= $attr['nsf_subscription_id'] != '' ? '<input type="hidden" name="nsf_subscription_ID" value="'.esc_attr($attr['nsf_subscription_id']).'">' : ($nsf_subscription_ID != '' ? '<input type="hidden" name="nsf_subscription_ID" value="'.$nsf_subscription_ID.'">' : '');

			$hidden_fields .= $attr['nsf_mailing_list_name'] != '' ? '<input type="hidden" name="nsf_mailing_list_name" value="'.esc_attr($attr['nsf_mailing_list_name']).'">' : ($nsf_mailing_list_name != '' ? '<input type="hidden" name="nsf_mailing_list_name" value="'.$nsf_mailing_list_name.'">' : '');

			$hidden_fields .= $attr['nsf_mailing_list_description'] != '' ? '<input type="hidden" name="nsf_mailing_list_description" value="'.esc_attr($attr['nsf_mailing_list_description']).'">' : ($nsf_mailing_list_description != '' ? '<input type="hidden" name="nsf_mailing_list_description" value="'.$nsf_mailing_list_description.'">' : '');

			$hidden_fields .= $attr['nsf_template_token'] != '' ? '<input type="hidden" name="nsf_template_token" value="'.esc_attr($attr['nsf_template_token']).'">' : ($nsf_template_token != '' ? '<input type="hidden" name="nsf_template_token" value="'.$nsf_template_token.'">' : '');

			$html .= '<div class="nsf-container" id="root">';
				$html .= '<div class="nsf-image-container" >';
					$html .= $this->ee_the_subheader_logo_html('desktop', 154, 88);
				$html .= '</div>';
				$html .= '<div class="nsf-form-container">';
					$html .= '<h1 class="nsf-header">'.$nsf_label.'</h1>';
					$html .= '<h2 class="nsf-subheader">'.$nsf_description.'</h2>';
					$html .= '<form id="nsf-form" class="nsf-form" name="nsf_form" action="" method="POST">';
						$html .= $hidden_fields;
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
			$nsf_output_hide = true;
			return $html;
		}

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

	public function nsf_register_settings( $group, $page ) {
		$section_id = 'beasley_newsletter_signup_form';

		add_settings_section( $section_id, 'Newsletter signup forms', '__return_false', $page );
		add_settings_field('nsf_label', 'Label', 'bbgi_input_field', $page, $section_id, 'name=nsf_label');
		add_settings_field('nsf_description', 'Description', 'bbgi_input_field', $page, $section_id, 'name=nsf_description');
		add_settings_field('nsf_subscription_attributes', 'Subscription Attributes', 'bbgi_input_field', $page, $section_id, 'name=nsf_subscription_attributes');
		add_settings_field('nsf_subscription_ID', 'Subscription ID', 'bbgi_input_field', $page, $section_id, 'name=nsf_subscription_ID');
		add_settings_field('nsf_mailing_list_name', 'Mailing list name', 'bbgi_input_field', $page, $section_id, 'name=nsf_mailing_list_name');
		add_settings_field('nsf_mailing_list_description', 'Mailing list description', 'bbgi_textarea_field', $page, $section_id, 'name=nsf_mailing_list_description');
		add_settings_field('nsf_template_token', 'Template token', 'bbgi_input_field', $page, $section_id, 'name=nsf_template_token');

		register_setting( $group, 'nsf_label', 'sanitize_text_field' );
		register_setting( $group, 'nsf_description', 'sanitize_text_field' );
		register_setting( $group, 'nsf_subscription_attributes', 'sanitize_text_field' );
		register_setting( $group, 'nsf_subscription_ID', 'sanitize_text_field' );
		register_setting( $group, 'nsf_mailing_list_name', 'sanitize_text_field' );
		register_setting( $group, 'nsf_mailing_list_description', 'sanitize_text_field' );
		register_setting( $group, 'nsf_template_token', 'sanitize_text_field' );
	}

}
new NewsletterSignupForm();
