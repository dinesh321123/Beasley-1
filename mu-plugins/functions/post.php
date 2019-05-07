<?php

/**
 * Returns TRUE if a post has [gallery] shortcode, otherwise FALSE.
 *
 * @param int|\WP_Post $post The post to check.
 * @return boolean TRUE if a post has gallery, otherwise FALSE.
 */
function bbgi_post_has_gallery( $post = null ) {
	$post = get_post( $post );
	return is_a( $post, '\WP_Post' )
		? stripos( $post->post_content, '[gallery' ) !== false
		: false;
}

function bbgi_featured_image_layout_is( $post = null, $feature_image_preference = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return;
	}

	$preference = get_post_meta( $post->ID, 'post_feature_image_preference', true );
	if ( $preference ) {
		return $feature_image_preference === $preference;
	}

	return 'top' === $feature_image_preference;
}
