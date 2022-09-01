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

		$query->set('search_fields', array(
			'post_title',
			'post_content',
			'post_excerpt',
			'meta' => array(
				'am_item_name',
				'am_item_description',
				'cpt_item_name',
				'cpt_item_description',
				'gallery-image',
			)));
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

/**
 * Galleries With Metadata Of 'gallery-image' initially hold only post-ids of Attachment Images.
 * Join back to post table and replace with Excerpts.
 */
add_filter( 'ep_prepare_meta_data', function( $meta, $post ) {
	// If $meta['gallery-image'] is still an array of post ids and not converted to a string yet
	if ($post->post_type === 'gmr_gallery' && $meta['gallery-image'] != null && is_array($meta['gallery-image'])) {
		// Swap in post_excerpts for gallery-image array elements currently holding Post IDs
		for ($i=0; $i<count($meta['gallery-image']); $i++) {
			if (is_numeric($meta['gallery-image'][$i])) {
				$meta['gallery-image'][$i] = get_the_excerpt($meta['gallery-image'][$i]);
			}
		}

		// Convert $meta['gallery-image'] to a single string rather than array so that the above swap is not attempted again
		$meta['gallery-image'] = implode(' ', $meta['gallery-image']);
	}
	return $meta;
}, 10, 2 );
