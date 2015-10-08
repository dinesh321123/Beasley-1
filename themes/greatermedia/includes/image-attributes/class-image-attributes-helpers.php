<?php

/**
 * helper function to return a string for image attribution
 *
 * @param $post_id
 *
 * @return mixed
 */
function get_image_attribution( $post_id ) {

	$image_attribution = get_post_meta( $post_id, 'gmr_image_attribution', true );

	if ( ! empty( $image_attribution ) ) {
		return esc_attr( $image_attribution );
	}

}

/**
 * helper function to echo the image attribution
 *
 * @param $post_id
 */
function image_attribution() {

	$image_attribution = get_post_meta( get_post_thumbnail_id(), 'gmr_image_attribution', true );
	$img_link = filter_var( $image_attribution, FILTER_VALIDATE_URL );

	if ( ! empty( $image_attribution ) ) {
		if ( $img_link ) {
			echo '<div class="image__attribution">';
			echo '<a href="' . wp_kses_post( $image_attribution ) . '" class="image__attribution--link">Photo Credit</a>';
			echo '</div>';
		} else {
			echo '<div class="image__attribution">';
			echo wp_kses_post( $image_attribution );
			echo '</div>';
		}
	}

}