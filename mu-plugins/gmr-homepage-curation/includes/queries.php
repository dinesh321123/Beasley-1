<?php

namespace GreaterMedia\HomepageCuration;

use \WP_Query;

function get_featured_query() {
	$ids = explode( ',', recent_homepage_query( 'featured_meta_box' ) );

	$args = array(
		'post_type'           => 'any',
		'post__in'            => $ids,
		'orderby'             => 'post__in',
		'ignore_sticky_posts' => true,
	);

	$query = new WP_Query( $args );

	return $query;
}

function get_community_query() {
	$ids = explode( ',', recent_homepage_query( 'dont_miss_meta_box' ) );

	$args = array(
		'post_type'           => 'any',
		'post__in'            => $ids,
		'orderby'             => 'post__in',
		'ignore_sticky_posts' => true,
	);

	$query = new WP_Query( $args );

	return $query;
}

function get_events_query() {
	$ids = array_filter( explode( ',', recent_homepage_query( 'events_meta_box' ) ) );

	if ( count( $ids ) ) {
		$args = array(
			'post_type'           => 'any',
			'post__in'            => $ids,
			'orderby'             => 'post__in',
			'ignore_sticky_posts' => true,
		);

		$query = new WP_Query( $args );
	} else {
		$event_args = array(
			'eventDisplay'        => 'list',
			'posts_per_page'      => 2,
			'ignore_sticky_posts' => true,
		);

		if ( function_exists( '\tribe_get_events' ) ) {
			$query = \tribe_get_events( $event_args, true );
		} else {
			// Return a query that results in nothing
			$query = new WP_Query( array( 'post__in' => array( 0 ) ) );
		}

	}

	return $query;
}

function get_contests_query() {
	$ids = explode( ',', get_option( 'gmr-homepage-contests' ) );
	$post_types = array(
		GMR_CONTEST_CPT,
	);

	$args = array(
		'post_type'           => $post_types,
		'post__in'            => $ids,
		'orderby'             => 'post__in',
		'ignore_sticky_posts' => true,
	);

	$query = new WP_Query( $args );

	return $query;
}

function get_current_homepage() {
	static $homepage = null;

	if ( is_null( $homepage ) ) {
		$homepages = new WP_Query( array(
			'post_type'              => gmr_homepages_slug(),
			'posts_per_page'         => 1,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_term_cache' => false,
		) );

		$homepage = $homepages->have_posts()
			? $homepages->next_post()
			: false;
	}

	return $homepage;
}

function recent_homepage_query( $meta_key ) {
	$posts = '';
	$homepages = get_preview_homepage();

	$homepage = false;
	if ( false === $homepages ) {
		$homepage = get_current_homepage();
	} elseif ( $homepages->have_posts() ) {
		$homepage = $homepages->next_post();
	}

	if ( $homepage ) {
		$posts = get_post_meta( $homepage->ID, $meta_key, true );
	} else {
		// fallback to old style with options
		switch ( $meta_key ) {
			case 'featured_meta_box':
				$posts = get_option( 'gmr-homepage-featured' );
				break;
			case 'dont_miss_meta_box':
				$posts = get_option( 'gmr-homepage-community' );
				break;
			case 'events_meta_box':
				$posts = get_option( 'gmr-homepage-events' );
				break;
		}
	}

	$limit = 0;
	if ( 'featured_meta_box' == $meta_key ) {
		$ee_featured_item_count_setting = (int)get_option( 'ee_featured_item_count_setting', '10' );
		$limit = apply_filters( 'gmr-homepage-featured-limit', $ee_featured_item_count_setting, $homepage );
	} elseif ( 'dont_miss_meta_box' == $meta_key ) {
		$ee_dont_miss_item_count_setting = (int)get_option( 'ee_dont_miss_item_count_setting', '10' );
		$limit = apply_filters( 'gmr-homepage-community-limit', $ee_dont_miss_item_count_setting, $homepage );
	} elseif ( 'events_meta_box' == $meta_key ) {
		$limit = apply_filters( 'gmr-homepage-events-limit', 2, $homepage );
	}

	if ( $limit > 0 ) {
		$posts = implode( ',', array_slice( explode( ',', $posts ), 0, $limit ) );
	}

	return $posts;
}
