<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class GreaterMediaSiteOptionsHelperFunctions {

	public static function init() {
		add_action( 'gmr_site_logo', array( __CLASS__, 'site_logo' ) );
		add_action( 'gmr_social_facebook', array( __CLASS__, 'facebook_link' ) );
		add_action( 'gmr_social_twitter', array( __CLASS__, 'twitter_link' ) );
		add_action( 'gmr_social_youtube', array( __CLASS__, 'youtube_link' ) );
		add_action( 'gmr_social_instagram', array( __CLASS__, 'instagram_link' ) );
		add_action( 'gmr_social', array( __CLASS__, 'social_links' ) );
	}

	public static function get_site_logo_id() {
		$site_logo_id = get_option( 'gmr_site_logo', 0 );

		return $site_logo_id;
	}

	public static function site_logo() {
		$site_logo_id = self::get_site_logo_id();
		if ( $site_logo_id ) {
			$site_logo = wp_get_attachment_image_src( $site_logo_id, 'full' );
			if ( $site_logo ) {
				echo '<img src="' . esc_url( $site_logo[0] ) . '" alt="' . get_bloginfo( 'name' ) . ' | ' . get_bloginfo( 'description' ) . '" class="header__logo--img">';
			}
		}
	}

	public static function facebook_link() {
		return get_option( 'gmr_facebook_url', '' );
	}

	public static function twitter_name() {
		return get_option( 'gmr_twitter_name', '' );
	}

	public static function youtube_link() {
		return get_option( 'gmr_youtube_url', '' );
	}

	public static function instagram_name() {
		return get_option( 'gmr_instagram_name', '' );
	}

	public static function social_links() {

		$facebook = self::facebook_link();
		$twitter = self::twitter_name();
		$youtube = self::youtube_link();
		$instagram = self::instagram_name();

		echo '<ul class="social__list">';
		if( $facebook ) {
			echo '<li><a class="social__link icon-facebook" href="' . esc_url( $facebook ) . '"></a></li>';
		}
		if( $twitter ) {
			echo '<li><a class="social__link icon-twitter" href="https://twitter.com/' . esc_html( $twitter ) . '"></a></li>';
		}
		if( $youtube ) {
			echo '<li><a class="social__link icon-youtube" href="' . esc_url( $youtube ) . '"></a></li>';
		}
		if( $instagram ) {
			echo '<li><a class="social__link icon-instagram" href="http://instagram.com/' . esc_url( $instagram ) . '"></a></li>';
		}
		echo '</ul>';

	}

}

GreaterMediaSiteOptionsHelperFunctions::init();