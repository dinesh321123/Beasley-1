<?php

namespace GreaterMedia\HomepageCuration;

use \WP_Query;

function get_featured_query() {
	$ids = explode( ',', get_option( 'gmr-homepage-featured' ) );

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
	$ids = explode( ',', get_option( 'gmr-homepage-community' ) );

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
	$ids = array_filter( explode( ',', get_option( 'gmr-homepage-events' ) ) );

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