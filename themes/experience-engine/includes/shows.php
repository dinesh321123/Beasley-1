<?php

if ( ! function_exists( 'ee_get_current_show' ) ) :
	function ee_get_current_show( $post = null ) {
		static $shows = array();

		$post = get_post( $post );
		if ( ! is_a( $post, '\WP_Post' ) ) {
			return null;
		}

		if ( $post->post_type == 'episode' ) {
			$post = get_post( $post->post_parent );
		}

		if ( ! empty( $shows[ $post->ID ] ) ) {
			return $shows[ $post->ID ];
		}

		$terms = get_the_terms( $post->ID, \ShowsCPT::SHOW_TAXONOMY );
		if ( ! is_array( $terms ) || empty( $terms ) ) {
			return null;
		}

		foreach ( $terms as $show ) {
			$show = \TDS\get_related_post( $show );
			if ( $show && \GreaterMedia\Shows\supports_homepage( $show->ID ) ) {
				$shows[ $post->ID ] = $show;
				return $show;
			}
		}

		return null;
	}
endif;

if ( ! function_exists( 'ee_get_show_meta' ) ) :
	function ee_get_show_meta( $show, $meta_key ) {
		$show = get_post( $show );
		if ( ! is_a( $show, '\WP_Post' ) ) {
			return false;
		}

		switch ( $meta_key ) {
			case 'logo':
				return get_post_meta( $show->ID, 'logo_image', true );

			case 'show-time':
				$show_day = get_post_meta( $show->ID, 'show_days', true );
				$show_time = get_post_meta( $show->ID, 'show_times', true );
				return trim( "{$show_day} {$show_time}" );

			case 'facebook':
				return filter_var( get_post_meta( $show->ID, 'show/social_pages/facebook', true ), FILTER_SANITIZE_URL );

			case 'twitter':
				return filter_var( get_post_meta( $show->ID, 'show/social_pages/twitter', true ), FILTER_SANITIZE_URL );

			case 'instagram':
				return filter_var( get_post_meta( $show->ID, 'show/social_pages/instagram', true ), FILTER_SANITIZE_URL );

			case 'google':
				return filter_var( get_post_meta( $show->ID, 'show/social_pages/google', true ), FILTER_SANITIZE_URL );
		}

		return false;
	}
endif;

if ( ! function_exists( 'ee_get_show_query' ) ) :
	function ee_get_show_query() {
		return \GreaterMedia\Shows\get_show_main_query();
	}
endif;

if ( ! function_exists( 'ee_get_show_favorites' ) ) :
	function ee_get_show_favorites() {
		return \GreaterMedia\Shows\get_show_favorites_query();
	}
endif;

if ( ! function_exists( 'ee_get_show_featured' ) ) :
	function ee_get_show_featured() {
		return \GreaterMedia\Shows\get_show_featured_query();
	}
endif;
