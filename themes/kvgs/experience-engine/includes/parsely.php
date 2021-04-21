<?php

/**
 * Outputs canonical meta tags needed by parsely,
 *
 * 1. Is Single Post
 *
 */
function parsely_meta_tags() {

	if ( is_single() ){

		$post_id = get_queried_object_id();
		$post = get_post( $post_id );
		// Publish canonical url
		echo "\n<meta name=\"parsely-network-canonical\" content=\"https://content.bbgi.com/" . parsely_clean_name($post->post_name ) . "/\"/>\n";

	}
}

/**
 * Removes indication that the post is a duplicate
 *
 * @param $post_name
 * @return string post name with -1 or -2 removed.
 */
function parsely_clean_name($post_name): string
{
	$pattern = "/^(?P<cleanedname>[a-zA-Z0-9_-]+?)($|-\d$)/";
	$matches = preg_match( $pattern, $post_name, $out );
	return $out ? $out['cleanedname'] : $post_name;
}

add_action( 'wp_head', 'parsely_meta_tags', 1 );
