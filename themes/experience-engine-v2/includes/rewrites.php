<?php

add_filter( 'do_parse_request', 'ee_check_domain_in_request_url' );

if ( ! function_exists( 'ee_check_domain_in_request_url' ) ) :
	function ee_check_domain_in_request_url( $do_parse_request ) {
		global $ee_blog_id;

		$sites = get_sites();

		$domains = wp_list_pluck( $sites, 'domain' );
		$domains = array_map( 'preg_quote', $domains );
		$domains = implode( '|', $domains );
		if ( preg_match( "#^/({$domains})(/.+)#i", $_SERVER['REQUEST_URI'], $matches ) ) {
			foreach ( $sites as $site ) {
				if ( $site->domain == $matches[1] && !! $site->public ) {
					$ee_blog_id = $site->blog_id;
					$_SERVER['REQUEST_URI'] = $matches[2];
					add_action( 'pre_get_posts', 'ee_switch_to_original_blog_for_query' );
					break;
				}
			}
		}

		return $do_parse_request;
	}
endif;

if ( ! function_exists( 'ee_switch_to_original_blog_for_query' ) ) :
	function ee_switch_to_original_blog_for_query( $query ) {
		remove_action( 'pre_get_posts', 'ee_switch_to_original_blog_for_query' );
		if ( $query->is_main_query() && $query->is_single() ) {
			add_filter( 'posts_pre_query', 'ee_restore_current_blog' );
			ee_switch_to_article_blog();
		}
	}
endif;

if ( ! function_exists( 'ee_switch_to_original_blog_for_seo' ) ) :
	function ee_switch_to_original_blog_for_seo() {
		if ( is_singular() ) {
			ee_switch_to_article_blog();
		}
	}
endif;

if ( ! function_exists( 'ee_restore_current_blog' ) ) :
	function ee_restore_current_blog( $value ) {
		remove_filter( 'posts_pre_query', 'ee_restore_current_blog' );
		restore_current_blog();

		return $value;
	}
endif;

if ( ! function_exists( 'ee_switch_to_article_blog' ) ) :
	function ee_switch_to_article_blog() {
		global $ee_blog_id;
		if ( $ee_blog_id ) {
			switch_to_blog( $ee_blog_id );
		}
	}
endif;
