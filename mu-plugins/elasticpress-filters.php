<?php
/**
 * Plugin Name: Greater Media ElasticPress Filters
 * Description: Adds additional functionality to the base ElasticPress Installation
 * Author: 10up
 * Author URI: http://10up.com
 */

namespace GreaterMedia\ElasticPress;

/**
 * Filters the post types that will be indexed by ElasticPress.
 *
 * @param array $post_types Current post types to index.
 *
 * @return array All post types that are not explicitly excluded from search.
 */
function filter_post_types( $post_types ) {
	// Index all post types that are not excluded from search
	$post_types = get_post_types( array( 'exclude_from_search' => false ) );

	return $post_types;
}
add_filter( 'ep_indexable_post_types', __NAMESPACE__ . '\filter_post_types' );
