<?php

namespace GreaterMedia\MobileHomepageCuration\Feed\CurrentHomePageFeatured;

use function GreaterMedia\MobileHomepageCuration\recent_homepage_query;
use function GreaterMedia\MobileHomepageCuration\load_template;

add_action( 'init', __NAMESPACE__ . '\register' );
// Register feed.
function register() {
	add_feed( 'current_mobile_homepage_featured', __NAMESPACE__ . '\render' );
	add_action( 'pre_get_posts', __NAMESPACE__ . '\filter_query' );
}

/**
 * Filter feed query.
 *
 * @param $query
 */
function filter_query( $query ) {
	// Bail if $query is not an object or of incorrect class.
	if ( ! $query instanceof \WP_Query ) {
		return;
	}

	// Bail if filters are suppressed on this query.
	if ( $query->get( 'suppress_filters' ) ) {
		return;
	}

	// Bail if it's not our feed.
	if ( ! $query->is_feed( 'current_mobile_homepage_featured' ) ) {
		return;
	}

	// Change the feed query.
	$post_ids = explode( ',', recent_homepage_query( 'featured_meta_box' ) );

	$query->set( 'post__in', $post_ids );
	$query->set( 'post_type', 'any' );
	$query->set( 'orderby', 'post__in' );
	$query->set( 'ignore_sticky_posts', true );
	$query->set( 'posts_per_page', 50 );
	$query->set( 'posts_per_rss', 50 );

	$query->set( 'nopaging', true );

	add_action( 'rss2_item', __NAMESPACE__ . '\rss2_item' );
	add_action( 'rss2_ns', __NAMESPACE__ . '\rss2_ns' );

	return $query;
}

/**
 * render feed using wordpress rss2 template.
 */
function render() {
	load_template( 'feed-current-homepage-featured.php' );
}