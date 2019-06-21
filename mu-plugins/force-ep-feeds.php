<?php

/**
 * Force EP integrate on XML feed queries
 *
 * @param $query The WP_Query to change
 */
function beasley_force_ep_on_feeds( $query ) {
	if ( $query->is_main_query() && $query->is_feed() ) {
		$query->set( 'ep_integrate', true );
	}

	return $query;
}

add_action( 'pre_get_posts', 'beasley_force_ep_on_feeds' );
