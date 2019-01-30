<?php

namespace GreaterMedia\MobileHomepageCuration\Feed\CurrentHomepage;

use function GreaterMedia\MobileHomepageCuration\recent_homepage_query;

add_action( 'init', __NAMESPACE__ . '\register' );
// Register feed.
function register() {
	add_feed( 'current_mobile_homepage', __NAMESPACE__ . '\render' );
	add_action( 'pre_get_posts', __NAMESPACE__ . '\filter_query' );
}

/**
 * Filter feed query.
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
	if ( ! $query->is_feed( 'current_mobile_homepage' ) ) {
		return;
	}

	$types = array( 'featured', 'dont_miss', 'events' );
	if ( isset( $_REQUEST['type'] ) && false === empty( $_REQUEST['type'] ) ) {
		$types = explode( ',', $_REQUEST['type'] );
	}
	// Change the feed query.
	$post_ids                    = array();
	$GLOBALS['current_mobile_homepage'] = array();

	if ( in_array( 'featured', $types ) ) {
		$GLOBALS['current_mobile_homepage']['featured'] = explode( ',', recent_homepage_query( 'featured_meta_box' ) );
		$post_ids                                = array_merge( $post_ids, $GLOBALS['current_mobile_homepage']['featured'] );
	}
	if ( in_array( 'dont_miss', $types ) ) {
		$GLOBALS['current_mobile_homepage']['dont_miss'] = explode( ',', recent_homepage_query( 'dont_miss_meta_box' ) );
		$post_ids                                 = array_merge( $post_ids, $GLOBALS['current_mobile_homepage']['dont_miss'] );
	}
	if ( in_array( 'events', $types ) ) {
		$GLOBALS['current_mobile_homepage']['events'] = explode( ',', recent_homepage_query( 'events_meta_box' ) );
		$post_ids                              = array_merge( $post_ids, $GLOBALS['current_mobile_homepage']['events'] );
	}


	$query->set( 'post__in', $post_ids );
	$query->set( 'post_type', 'any' );
	$query->set( 'orderby', 'post__in' );
	$query->set( 'ignore_sticky_posts', true );
	$query->set( 'posts_per_page', 50 );
	$query->set( 'posts_per_rss', 50 );

	$query->set( 'nopaging', true);

	add_action( 'rss2_item', __NAMESPACE__ . '\rss2_item' );

	return $query;
}

/**
 * render feed using wordpress rss2 template.
 */
function render() {
	load_template( ABSPATH . WPINC . '/feed-rss2.php' );
}

/**
 * Add aditional fields
 */
function rss2_item() {
	$current_type = '';
	$id           = get_the_ID();
	foreach ( $GLOBALS['current_homepage'] as $type => $ids ) {
		if ( in_array( $id, $ids ) ) {
			$current_type = $type;
			break;
		}
	}
	?>
	<post_format><?php echo esc_html( get_post_format() ); ?></post_format>
	<featured_section><?php echo $current_type; ?></featured_section>
	<?php
}