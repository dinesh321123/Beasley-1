<?php

namespace GreaterMedia\HomepageCuration;

use \WP_Query;

function get_featured_query() {
	$ids = explode( ',', get_option( 'gmr-homepage-featured' ) );

	$args = array(
		'post_type' => 'any',
		'post__in' => $ids,
		'orderby' => 'post__in',
	);

	$query = new WP_Query( $args );

	return $query;
}
