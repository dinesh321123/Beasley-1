// ### This is plugin that calls this code
<?php
/*
Plugin Name: CPT - Newsletter Signup Plugin
Description: Allows for embedding newsletter signhup forms on the site
Version: 0.0.1
Author: Rupesh Jorkar (RJ)
Author URI: https://bbgi.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
define( 'GENERAL_SETTINGS_CPT_VERSION', '0.0.1' );
define( 'GENERAL_SETTINGS_CPT_URL', plugin_dir_url( __FILE__ ) );
define( 'GENERAL_SETTINGS_CPT_PATH', dirname( __FILE__ ) );
define( 'GENERAL_SETTINGS_CPT_TEXT_DOMAIN', 'general_settings_textdomain' );

include __DIR__ . '/includes/newsletter-signup-form.php';


// ### This is the "/includes/newsletter-signup-form.php"
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
        add_shortcode( 'newsletter-signup', array( $this, 'nsf_function' ) );
        add_action( 'bbgi_register_settings', array( $this, 'nsf_register_settings' ) , 10, 2 );
        add_action( 'wp_ajax_newsletter_signup_form_data_submit', array( $this, 'newsletter_signup_form_data_submit_action' ) );
        add_action( 'wp_ajax_nopriv_newsletter_signup_form_data_submit', array( $this, 'newsletter_signup_form_data_submit_action' ) );
    }
    public function nsf_register_scripts() {
        //Script for front end
        $nsf_ajax_nonce = wp_create_nonce( 'nsf-ajax-nonce' );
        $postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

        wp_register_style('nsf-style',GENERAL_SETTINGS_CPT_URL . "assets/css/newsletter-signup-form". $postfix .".css", array(), '1.0.1', 'all');
        wp_enqueue_style('nsf-style');

        wp_register_script('nsf-script', GENERAL_SETTINGS_CPT_URL . 'assets/js/newsletter-signup-form'. $postfix .'.js', array('jquery'), '1.0.3');
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
                'label' => '',
                'description' => '',
                'color' => '',
                'subscription_attributes' => '',
                'subscription_id' => '',
                'mailing_list_name' => '',
                'mailing_list_description' => '',
                'template_token' => '',
            ), $attr );

        if ( !$nsf_output_hide ) {

            $html = '';
            $hidden_fields = '';

            $nsf_label                         = $attr['label'] != '' ? sanitize_text_field($attr['label']) : (get_option('nsf_label') != '' ? get_option('nsf_label') : 'Join the Family');
            $nsf_description                = $attr['description'] != '' ? sanitize_text_field($attr['description']) : (get_option('nsf_description') != '' ? get_option('nsf_description') : 'Get Our Latest Articles in Your Inbox');
            $nsf_color                     = $attr['color'] != '' ? sanitize_text_field($attr['color']) : (get_option('nsf_color') != '' ? get_option('nsf_color') : '#000000');
            $nsf_subscription_attributes    = get_option('nsf_subscription_attributes');
            $nsf_subscription_ID            = get_option('nsf_subscription_ID');
            $nsf_mailing_list_name          = get_option('nsf_mailing_list_name');
            $nsf_mailing_list_description    = get_option('nsf_mailing_list_description');
            $nsf_template_token                = get_option('nsf_template_token');
            $nsf_checkbox_content            = get_option('nsf_checkbox_content') ? get_option('nsf_checkbox_content') : 'By clicking "Subscribe" I agree to the website\'s terms of Service and Privacy Policy. I understand I can unsubscribe at any time.';
            $siteid                         = (get_option( 'ee_publisher') != '') ? get_option( 'ee_publisher') : '';
            $domain                         = get_site_url();
            $page_path                      = get_permalink();

            $hidden_fields .= $siteid != '' ? '<input type="hidden" name="nsf_siteid" id="nsf_siteid" value="'.esc_attr($siteid).'">' : '';
            $hidden_fields .= $domain != '' ? '<input type="hidden" name="nsf_domain" id="nsf_domain" value="'.esc_attr($domain).'">' : '';
            $hidden_fields .= $page_path != '' ? '<input type="hidden" name="nsf_page_path" id="nsf_page_path" value="'.esc_attr($page_path).'">' : '';
            $hidden_fields .= $attr['subscription_attributes'] != '' ? '<input type="hidden" name="nsf_subscription_attributes" id="nsf_subscription_attributes" value="'.esc_attr($attr['subscription_attributes']).'">' : ($nsf_subscription_attributes != '' ? '<input type="hidden" name="nsf_subscription_attributes" id="nsf_subscription_attributes" value="'.$nsf_subscription_attributes.'">' : '');
            $hidden_fields .= $attr['subscription_id'] != '' ? '<input type="hidden" name="nsf_subscription_ID" id="nsf_subscription_ID" value="'.esc_attr($attr['subscription_id']).'">' : ($nsf_subscription_ID != '' ? '<input type="hidden" name="nsf_subscription_ID" id="nsf_subscription_ID" value="'.$nsf_subscription_ID.'">' : '');
            $hidden_fields .= $attr['mailing_list_name'] != '' ? '<input type="hidden" name="nsf_mailing_list_name" id="nsf_mailing_list_name" value="'.esc_attr($attr['mailing_list_name']).'">' : ($nsf_mailing_list_name != '' ? '<input type="hidden" name="nsf_mailing_list_name" id="nsf_mailing_list_name" value="'.$nsf_mailing_list_name.'">' : '');
            $hidden_fields .= $attr['mailing_list_description'] != '' ? '<input type="hidden" name="nsf_mailing_list_description" id="nsf_mailing_list_description" value="'.esc_attr($attr['mailing_list_description']).'">' : ($nsf_mailing_list_description != '' ? '<input type="hidden" name="nsf_mailing_list_description" id="nsf_mailing_list_description" value="'.$nsf_mailing_list_description.'">' : '');
            $hidden_fields .= $attr['template_token'] != '' ? '<input type="hidden" name="nsf_template_token" id="nsf_template_token" value="'.esc_attr($attr['template_token']).'">' : ($nsf_template_token != '' ? '<input type="hidden" name="nsf_template_token" id="nsf_template_token" value="'.$nsf_template_token.'">' : '');

            $html .= '<style>
                        .nsf-container label,
                        .nsf-container h2.nsf-subheader,
                        .nsf-container .input-label,
                        .nsf-container span.nsf-name-error,
                        .nsf-container span.nsf-email-error,
                        .nsf-container button.nsf-form-submit,
                        .nsf-container .response-error-container,
                        .nsf-container .nsf-checkbox-container { color : '.$nsf_color.'; }
                    </style>';
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
                                $html .= '<div class="input-field"><input type="text" name="nsf_first_name" class="nsf-first-name" /><span class="nsf-fname-error-msg"></span></div>';
                            $html .= '</div>';
                            $html .= '<div class="nsf-input-container">';
                                $html .= '<div class="input-label"><label>Last Name</label></div>';
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
                    $html .= '<p class="nsf-checkbox-container"><input type="checkbox" class="nsf-checkbox-content" id="nsf-checkbox-content" name="nsf_checkbox_content"> '.$nsf_checkbox_content.'</p>';
                    $html .= '<div class="nsf-spinner"><div class="spinner"></div></div>';
                    $html .= '<p class="response-error-container" style="font-size:14px;"></p>';
                $html .= '</div>';
            $html .= '</div>';
            $nsf_output_hide = true;
            return $html;
        }

    }

    public function ee_the_subheader_logo_html( $mobile_or_desktop, $base_w = 150, $base_h = 150 ) {
        $html = '';
        $field_name = get_option('ee_newsletter_logo') ? 'ee_newsletter_logo' : 'gmr_site_logo';
        $atag_class_name = $mobile_or_desktop . '-mewsletter-logo-link';
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

        add_settings_field( 'ee_newsletter_logo', 'Logo', 'bbgi_image_field', $page, $section_id, 'name=ee_newsletter_logo' );
        add_settings_section( $section_id, 'Newsletter signup forms', '__return_false', $page );
        add_settings_field('nsf_label', 'Label', 'bbgi_input_field', $page, $section_id, 'name=nsf_label&default=Join the Family');
        add_settings_field('nsf_description', 'Description', 'bbgi_input_field', $page, $section_id, 'name=nsf_description&default=Get Our Latest Articles in Your Inbox');
        add_settings_field('nsf_color', 'Text-Color', 'bbgi_input_field', $page, $section_id, 'name=nsf_color&default=#000000');
        add_settings_field('nsf_subscription_attributes', 'Subscription Attributes', 'bbgi_input_field', $page, $section_id, 'name=nsf_subscription_attributes');
        add_settings_field('nsf_subscription_ID', 'Subscription ID', 'bbgi_input_field', $page, $section_id, 'name=nsf_subscription_ID');
        add_settings_field('nsf_mailing_list_name', 'Mailing list name', 'bbgi_input_field', $page, $section_id, 'name=nsf_mailing_list_name');
        add_settings_field('nsf_mailing_list_description', 'Mailing list description', 'bbgi_textarea_field', $page, $section_id, 'name=nsf_mailing_list_description');
        add_settings_field('nsf_template_token', 'Template token', 'bbgi_input_field', $page, $section_id, 'name=nsf_template_token');
        add_settings_field('nsf_checkbox_content', 'Terms of Service and Privacy Policy Content', 'bbgi_textarea_field', $page, $section_id, 'name=nsf_checkbox_content&default= By clicking "Subscribe" I agree to the website\'s terms of Service and Privacy Policy. I understand I can unsubscribe at any time.');

        register_setting( $group, 'ee_newsletter_logo', 'intval' );
        register_setting( $group, 'nsf_label', 'sanitize_text_field' );
        register_setting( $group, 'nsf_description', 'sanitize_text_field' );
        register_setting( $group, 'nsf_color', 'sanitize_text_field' );
        register_setting( $group, 'nsf_subscription_attributes', 'sanitize_text_field' );
        register_setting( $group, 'nsf_subscription_ID', 'sanitize_text_field' );
        register_setting( $group, 'nsf_mailing_list_name', 'sanitize_text_field' );
        register_setting( $group, 'nsf_mailing_list_description', 'sanitize_text_field' );
        register_setting( $group, 'nsf_template_token', 'sanitize_text_field' );
        register_setting( $group, 'nsf_checkbox_content', 'sanitize_text_field' );

    }

    public function newsletter_signup_form_data_submit_action () {

        if ( ! wp_verify_nonce( sanitize_text_field($_POST['nonce']), 'nsf-ajax-nonce' ) ) {
            wp_send_json_error( 'Invalid Nonce.' );
        }

        $data_array = array(
            'nsf_name'                         => sanitize_text_field($_POST['name']),
            'nsf_email'                     => sanitize_email($_POST['email']),
            'nsf_last_name'                 =>sanitize_text_field($_POST['nsf_last_name']),
            'nsf_subscription_attributes'     => sanitize_text_field($_POST['nsf_subscription_attributes']),
            'nsf_subscription_ID'             => sanitize_text_field($_POST['nsf_subscription_ID']),
            'nsf_mailing_list_name'         => sanitize_text_field($_POST['nsf_mailing_list_name']),
            'nsf_mailing_list_description'     => sanitize_text_field($_POST['nsf_mailing_list_description']),
            'nsf_template_token'             => sanitize_text_field($_POST['nsf_template_token']),
            'domain'                        => esc_url($_POST['nsf_domain']),
            'siteid'                        => sanitize_text_field($_POST['nsf_siteid']),
            'pagepath'                        => esc_url($_POST['nsf_page_path']),
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
