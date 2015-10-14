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
 * @param $image_id
 */
function image_attribution( $image_id = null, $echo = true ) {
	if ( ! $image_id ) {
		$image_id = get_post_thumbnail_id();
	}

	$image_attribution = get_post_meta( $image_id, 'gmr_image_attribution', true );
	$img_link = filter_var( $image_attribution, FILTER_VALIDATE_URL );

	$attribution = '';
	if ( ! empty( $image_attribution ) ) {
		if ( $img_link ) {
			$attribution .= '<div class="image__attribution">';
			$attribution .= '<a href="' . wp_kses_post( $image_attribution ) . '" class="image__attribution--link">Photo Credit</a>';
			$attribution .= '</div>';
		} else {
			$attribution .= '<div class="image__attribution">';
			$attribution .= wp_kses_post( $image_attribution );
			$attribution .= '</div>';
		}
	}

	if ( $echo ) {
		echo $attribution;
	}

	return $attribution;
}