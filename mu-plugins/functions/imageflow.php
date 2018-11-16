<?php

function bbgi_get_image_url( $image, $width, $height, $mode = 'crop', $max = false ) {
	$image_id = is_a( $image, '\WP_Post' ) ? $image->ID : $image;

	$data = wp_get_attachment_image_src( $image_id, 'original' );
	if ( ! $data ) {
		return false;
	}

	$args = array();

	if ( ( is_array( $max ) && ! empty( $max['width'] ) ) || filter_var( $max, FILTER_VALIDATE_BOOLEAN )  ) {
		$args['maxwidth'] = $width;
	} else {
		$args['width'] = $width;
	}

	if ( ( is_array( $max ) && ! empty( $max['height'] ) ) || filter_var( $max, FILTER_VALIDATE_BOOLEAN ) ) {
		$args['maxheight'] = $height;
	} else {
		$args['height'] = $height;
	}

	$aspect = ! empty( $data[2] ) ? $data[1] / $data[2] : 1;
	$args['anchor'] = $aspect < 1 ? 'topleft' : 'middlecenter';

	if ( $mode ) {
		$args['mode'] = $mode;
	}

	return add_query_arg( $args, $data[0] );
}

function bbgi_get_post_thumbnail_url( $post, $use_fallback, $width, $height, $mode = 'crop', $max = false ) {
	$thumbnail_id = get_post_thumbnail_id( $post );
	if ( ! $thumbnail_id && $use_fallback ) {
		$thumbnail_id = greatermedia_get_fallback_thumbnail_id( $post );
	}

	if ( $thumbnail_id ) {
		$url = bbgi_get_image_url( $thumbnail_id, $width, $height, $mode, $max );
		if ( $url ) {
			return $url;
		}
	}

	return false;
}

function bbgi_post_thumbnail_url( $post, $use_fallback, $width, $height, $mode = 'crop', $max = false ) {
	$url = bbgi_get_post_thumbnail_url( $post, $use_fallback, $width, $height, $mode, $max );
	if ( $url ) {
		echo esc_url( $url );
	}
}

function bbgi_the_image_attribution( $image_id = null ) {
	if ( empty( $image_id ) ) {
		$image_id = get_post_thumbnail_id();
	}

	$attribution = get_post_meta( $image_id, 'gmr_image_attribution', true );
	if ( ! empty( $attribution ) ) {
		$img_link = filter_var( $attribution, FILTER_VALIDATE_URL );
		if ( $img_link ) {
			echo '<div class="image-attribution">';
				echo '<a href="' . esc_url( $attribution ) . '" class="image-attribution--link">Photo Credit</a>';
			echo '</div>';
		} else {
			echo '<div class="image-attribution">';
				echo wp_kses_post( $attribution );
			echo '</div>';
		}
	}
}
