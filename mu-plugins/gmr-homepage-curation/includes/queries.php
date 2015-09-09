<?php

namespace GreaterMedia\HomepageCuration;

use \WP_Query;

function get_featured_query() {
	$ids = recent_homepage_query( 'featured_meta_box' );

	if ( '' !== $ids ) {
		$ids = explode( ',', $ids );
	} else {
		$ids = array();
	}

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
	$ids = recent_homepage_query( 'dont_miss_meta_box' );

	if ( '' !== $ids ) {
		$ids = explode( ',', $ids );
	} else {
		$ids = array();
	}

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
	$ids = recent_homepage_query( 'events_meta_box' );

	if ( '' !== $ids ) {
		$ids = explode( ',', $ids );
	} else {
		$ids = array();
	}


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
		GMR_SURVEY_CPT
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

function recent_homepage_query( $meta_key ) {
	$posts = '';
	$homepages = get_preview_homepage();

	if ( false === $homepages ) {
		$homepages = new WP_Query(
			array(
				'post_type'              => gmr_homepages_slug(),
				'posts_per_page'         => 1,
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_term_cache' => false
			)
		);
	}

	if ( $homepages->have_posts() ) {
		while ( $homepages->have_posts() ) {
			$homepages->the_post();
			$posts = get_post_meta( get_the_ID(), $meta_key, true );
		}
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

	wp_reset_postdata();
	return $posts;
}