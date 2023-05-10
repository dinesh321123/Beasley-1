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
		add_action( 'wp_ajax_newsletter_signup_form_data_submit', array( $this, 'newsletter_signup_form_data_submit_action' ) );
		add_action( 'wp_ajax_nopriv_newsletter_signup_form_data_submit', array( $this, 'newsletter_signup_form_data_submit_action' ) );
	}
	public function nsf_register_scripts() {
		//Script for front end
		$nsf_ajax_nonce = wp_create_nonce( 'nsf-ajax-nonce' );
		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

		wp_register_style('nsf-style',GENERAL_SETTINGS_CPT_URL . "assets/css/newsletter-signup-form". $postfix .".css", array(), GENERAL_SETTINGS_CPT_VERSION, 'all');
		wp_enqueue_style('nsf-style');

		// wp_register_script('nsf-script', GENERAL_SETTINGS_CPT_URL . 'assets/js/newsletter-signup-form'. $postfix .'.js', array('jquery'), '1.0');
		wp_register_script('nsf-script', GENERAL_SETTINGS_CPT_URL . 'assets/js/newsletter-signup-form.js', array('jquery'), '1.0');
		wp_localize_script( 
			'nsf-script',
			'nsf_ajax_object',
			array( 
				'url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => $nsf_ajax_nonce,
				) 
		);

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

			$hidden_fields .= $attr['nsf_subscription_attributes'] != '' ? '<input type="hidden" name="nsf_subscription_attributes" id="nsf_subscription_attributes" value="'.esc_attr($attr['nsf_subscription_attributes']).'">' : ($nsf_subscription_attributes != '' ? '<input type="hidden" name="nsf_subscription_attributes" id="nsf_subscription_attributes" value="'.$nsf_subscription_attributes.'">' : '');

			$hidden_fields .= $attr['nsf_subscription_id'] != '' ? '<input type="hidden" name="nsf_subscription_ID" id="nsf_subscription_ID" value="'.esc_attr($attr['nsf_subscription_id']).'">' : ($nsf_subscription_ID != '' ? '<input type="hidden" name="nsf_subscription_ID" id="nsf_subscription_ID" value="'.$nsf_subscription_ID.'">' : '');

			$hidden_fields .= $attr['nsf_mailing_list_name'] != '' ? '<input type="hidden" name="nsf_mailing_list_name" id="nsf_mailing_list_name" value="'.esc_attr($attr['nsf_mailing_list_name']).'">' : ($nsf_mailing_list_name != '' ? '<input type="hidden" name="nsf_mailing_list_name" id="nsf_mailing_list_name" value="'.$nsf_mailing_list_name.'">' : '');

			$hidden_fields .= $attr['nsf_mailing_list_description'] != '' ? '<input type="hidden" name="nsf_mailing_list_description" id="nsf_mailing_list_description" value="'.esc_attr($attr['nsf_mailing_list_description']).'">' : ($nsf_mailing_list_description != '' ? '<input type="hidden" name="nsf_mailing_list_description" id="nsf_mailing_list_description" value="'.$nsf_mailing_list_description.'">' : '');

			$hidden_fields .= $attr['nsf_template_token'] != '' ? '<input type="hidden" name="nsf_template_token" id="nsf_template_token" value="'.esc_attr($attr['nsf_template_token']).'">' : ($nsf_template_token != '' ? '<input type="hidden" name="nsf_template_token" id="nsf_template_token" value="'.$nsf_template_token.'">' : '');

			$html .= '<div class="nsf-container" id="root">';
				$html .= '<div class="nsf-image-container" >';
					$html .= $this->ee_the_subheader_logo_html('desktop', 154, 88);
				$html .= '</div>';
				$html .= '<div class="nsf-form-container">';
					$html .= '<h1 class="nsf-header">'.$nsf_label.'</h1>';
					$html .= '<h2 class="nsf-subheader">'.$nsf_description.'</h2>';
					$html .= '<form id="nsf-form" class="nsf-form" name="nsf_form" action="#" method="POST">';
						$html .= $hidden_fields;
						$html .= '<div class="nsf-input-container">';
							$html .= '<div class="input-label"><label>First Name</label><span> *</span></div>';
							$html .= '<div class="input-field"><input type="text" name="nsf_first_name" class="nsf-first-name" /></div>';
						$html .= '</div>';
						$html .= '<div class="nsf-input-container">';
							$html .= '<div class="input-label"><label>Last Name</label><span> *</span></div>';
							$html .= '<div class="input-field"><input type="text" name="nsf_last_name" id="nsf-last-name" class="nsf-last-name" /></div>';
						$html .= '</div>';
						$html .= '<div class="nsf-input-container">';
							$html .= '<div class="input-label"><label>Email</label><span> *</span></div>';
							$html .= '<div class="input-field"><input type="text" name="nsf_email" class="nsf-email" /><span class="nsf-email-error-msg"></span></div>';
						$html .= '</div>';
						$html .= '<div class="nsf-action-container">';
							$html .= '<button class="nsf-form-submit" type="submit">Subscribe</button>';
						$html .= '</div>';
					$html .= '</form>';
					$html .= '<div class="nsf-spinner"><div class="spinner"></div></div><p class="response-error-container" style="font-size:14px;"></p>';
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

	public function newsletter_signup_form_data_submit_action () {

		if ( ! wp_verify_nonce( sanitize_text_field($_POST['nonce']), 'nsf-ajax-nonce' ) ) {
			wp_send_json_error( 'Invalid Nonce.' );
		}

		$data_array = array(
			'nsf_name' 						=> sanitize_text_field($_POST['name']),
			'nsf_email' 					=> sanitize_email($_POST['email']),
			'nsf_last_name' 				=>sanitize_text_field($_POST['nsf_last_name']),
			'nsf_subscription_attributes' 	=> sanitize_text_field($_POST['nsf_subscription_attributes']),
			'nsf_subscription_ID' 			=> sanitize_text_field($_POST['nsf_subscription_ID']),
			'nsf_mailing_list_name' 		=> sanitize_text_field($_POST['nsf_mailing_list_name']),
			'nsf_mailing_list_description' 	=> sanitize_text_field($_POST['nsf_mailing_list_description']),
			'nsf_template_token' 			=> sanitize_text_field($_POST['nsf_template_token']),
		);

		// Make an HTTP POST request using wp_remote_post()
		$args = array(
			'headers' => array(
				'Content-Type' => 'application/json'
			),
			'body' => json_encode($data_array)
		);
		
		$response = wp_remote_post( 'https://experience.bbgi.com/v1/email/signup', $args );

		if ( ! is_wp_error( $response ) ) {
			wp_send_json($response);
		} else {
			wp_send_json_error( $response->get_error_message() );
		}

	}

}
new NewsletterSignupForm();
