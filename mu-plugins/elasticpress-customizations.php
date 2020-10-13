<?php
/*
 * Plugin Name: Elasticpress Customizations
 * Description: Here goes the query customizations specific to the site
 * Version: 1.0
 * Author: 10up
 * Author URI: http://10up.com
 */

/**
 * Forces the query to be sorted by date instead of relevancy
 */
add_action( 'pre_get_posts', function ( $query ) {
	if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
		$query->set( 'orderby', 'date' );
		$query->set( 'order', 'desc' );
	}
}, 10, 1 );

/**
 * Forces exact matching
 */
add_filter( 'ep_formatted_args', function ( $formatted_args, $args  ) {
	if ( ! is_admin() && is_search() ) {
		if ( ! empty( $formatted_args['query']['bool']['should'] ) ) {
			$formatted_args['query']['bool']['must'] = $formatted_args['query']['bool']['should'];
			$formatted_args['query']['bool']['must'][0]['multi_match']['operator'] = 'AND';
			unset( $formatted_args['query']['bool']['should'] );
			unset( $formatted_args["query"]["bool"]["must"][0]["multi_match"]["type"] );
		}
	}
	return $formatted_args;
}, 10, 2 );
