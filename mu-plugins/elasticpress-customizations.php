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
	if ( ! is_admin() && $query->is_main_query() ) {
		$query->set( 'orderby', 'date' );
		$query->set( 'order', 'desc' );
	}
}, 10, 1 );