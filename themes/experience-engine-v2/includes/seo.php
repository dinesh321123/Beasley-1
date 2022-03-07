<?php

/**
 * Overrides the WP SEO Open graph article author.
 *
 * @return string|false
 */
function ee_update_opengraph_article_author() {
	if ( is_single() ) {
		$post_id        = get_the_ID();
		$article_author = ee_get_opengraph_article_author( $post_id );

		if ( ! empty( $article_author ) ) {
			return $article_author;
		}
	}

	return false;
}

/**
 * Returns the opengraph article author for the specified post id. If
 * the user does not have a facebook url in meta, uses the author
 * WP permalink instead.
 *
 * @param int $post_id The post id
 * @return string URL
 */
function ee_get_opengraph_article_author( $post_id ) {
	$post = get_post( $post_id );
	$author = $post->post_author;

	if ( ! empty( $author ) ) {
		$user = get_user_by( 'ID', $author );

		if ( ! empty( $user->facebook ) ) {
			return $user->facebook;
		} else {
			return get_author_posts_url( $author );
		}
	} else {
		return false;
	}
}

/**
 * Overrides WP SEO's article:author opengraph output.
 */
add_filter( 'wpseo_opengraph_author_facebook', 'ee_update_opengraph_article_author' );

/**
 * Enable article:published_time for custom post types
 */
add_filter( 'wpseo_opengraph_show_publish_date', '__return_true' );
