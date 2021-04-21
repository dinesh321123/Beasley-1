<?php

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
