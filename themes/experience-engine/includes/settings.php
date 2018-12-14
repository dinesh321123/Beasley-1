<?php

add_action( 'customize_register', 'ee_register_customizer' );
add_action( 'bbgi_register_settings', 'ee_register_settings', 1, 2 );

if ( ! function_exists( 'ee_register_settings' ) ) :
	function ee_register_settings( $group, $page ) {
		add_settings_section( 'ee_site_settings', 'Station Settings', '__return_false', $page );

		add_settings_field( 'ee_newsletter_signup_page', 'Newsletter Signup Page', 'wp_dropdown_pages', $page, 'ee_site_settings', array(
			'name'              => 'ee_newsletter_signup_page',
			'selected'          => get_option( 'ee_newsletter_signup_page' ),
			'show_option_none'  => '&#8212;',
			'option_none_value' => '0',
		) );

		add_settings_field( 'ee_publisher', 'Publisher', 'ee_render_publisher_select', $page, 'ee_site_settings', array(
			'name'     => 'ee_publisher',
			'selected' => get_option( 'ee_publisher' ),
		) );

		register_setting( $group, 'ee_newsletter_signup_page', 'intval' );
		register_setting( $group, 'ee_publisher', 'sanitize_text_field' );
	}
endif;

if ( ! function_exists( 'ee_has_newsletter_page' ) ) :
	function ee_has_newsletter_page() {
		$page_id = get_option( 'ee_newsletter_signup_page' );
		if ( $page_id < 1 ) {
			return false;
		}

		$page = get_post( $page_id );
		if ( ! is_a( $page, '\WP_Post' ) || $page->post_type != 'page' ) {
			return false;
		}

		return true;
	}
endif;

if ( ! function_exists( 'ee_the_newsletter_page_permalink' ) ) :
	function ee_the_newsletter_page_permalink() {
		$page_id = get_option( 'ee_newsletter_signup_page' );
		the_permalink( $page_id );
	}
endif;

if ( ! function_exists( 'ee_render_publisher_select' ) ) :
	function ee_render_publisher_select( $args ) {
		$publishers = \Bbgi\Module::get( 'experience-engine' )->get_publisher_list();

		?><select name="<?php echo esc_attr( $args['name'] ); ?>">
			<option value="">—</option>
			<?php foreach ( $publishers as $publisher ): ?>
				<option
					value="<?php echo esc_attr( $publisher['id'] ); ?>"
					<?php selected( $args['selected'], $publisher['id'] ); ?>>
					<?php echo esc_html( $publisher['title'] ); ?>
				</option>
			<?php endforeach; ?>
		</select><?php
	}
endif;

if ( ! function_exists( 'ee_register_customizer' ) ) :
	function ee_register_customizer( $wp_customize ) {
		$wp_customize->add_section( 'beasley_theme_options', array(
			'title'       => 'Theme Options',
			'priority'    => 1,
			'capability'  => 'edit_theme_options',
			'description' => 'Select the theme version',
		) );

		$wp_customize->add_setting( 'ee_theme_version', array(
			'default'    => '-dark',
			'type'       => 'theme_mod',
			'capability' => 'edit_theme_options',
			'transport'  => 'refresh',
		) );

		$wp_customize->add_control( 'ee_theme_version', array(
			'type'    => 'radio',
			'label'   => 'Theme Version',
			'section' => 'beasley_theme_options',
			'choices' => array(
				'-light' => 'Light',
				'-dark'  => 'Dark',
			),
		) );
	}
endif;


