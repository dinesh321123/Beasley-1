<?php

if ( ! class_exists( 'GreaterMediaGallery' ) ) :
	return;
endif;

$current_gallery = get_queried_object();
$ids = \GreaterMediaGallery::get_attachment_ids_for_post( $current_gallery );
if ( ! is_array( $ids ) ) :
	$ids = array();
endif;

$sponsored_image = get_field( 'sponsored_image', $current_gallery );
if ( ! empty( $sponsored_image ) ) :
	array_unshift( $ids, $sponsored_image );
endif;

$images = array_values( array_filter( array_map( 'get_post', array_values( $ids ) ) ) );
if ( empty( $images ) ) :
	return;
endif;

$ads_interval = filter_var( get_field( 'images_per_ad', $current_gallery ), FILTER_VALIDATE_INT, array( 'options' => array(
	'min_range' => 1,
	'max_range' => 99,
	'default'   => 3,
) ) );

$current_gallery_url = trailingslashit( get_permalink( $current_gallery->ID ) );
$current_image_slug = get_query_var( 'view' );

echo '<ul class="gallery-listicle">';

	foreach ( $images as $index => $image ) :
		$image_html = ee_the_lazy_image( $image->ID, false );
		if ( empty( $image_html ) ) :
			continue;
		endif;

		$title = get_the_title( $image );

		echo '<li class="gallery-listicle-item', $current_image_slug == $image->post_name ? ' scroll-to' : '', '">';
			echo $image_html;

			echo '<div>';
				echo '<h3>', esc_html( $title ), '</h3>';
				$attribution = trim( get_post_meta( $image->ID, 'gmr_image_attribution', true ) );
				if ( ! empty( $attribution ) ) :
					echo '<h4>', esc_html( $attribution ), '</h4>';
				endif;

				if ( $sponsored_image != $image->ID ) :
					if ( ! get_field( 'hide_download_link', $current_gallery ) ) :
						echo '<p>';
							echo '<a href="', esc_url( wp_get_attachment_image_url( $image->ID, 'full' ) ), '" class="-download" download target="_blank" rel="noopener">download</a>';
						echo '</p>';
					endif;

					if ( ! get_field( 'hide_social_share', $current_gallery ) ) :
						$url = get_field( 'share_photos', $current_gallery )
							? $current_gallery_url . 'view/' . urlencode( $image->post_name ) . '/'
							: $current_gallery_url;

						ee_the_share_buttons( $url, $title );
					endif;
				endif;

				echo '<p>', get_the_excerpt( $image ), '</p>';
			echo '</div>';

			if ( $index > 0 && $index % $ads_interval == 0 ) :
				do_action( 'dfp_tag', 'dfp_ad_inlist_infinite' );
			endif;
		echo '</li>';
	endforeach;

echo '</ul>';
