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

$april15th = strtotime( '2018-04-15' );
$current_image_slug = get_query_var( 'view' );

echo '<ul class="gallery-listicle">';

	foreach ( $images as $index => $image ) :
		$image_html = ee_the_lazy_image( $image->ID, false );
		if ( empty( $image_html ) ) :
			continue;
		endif;

		echo '<li class="gallery-listicle-item">';
			echo $image_html;

			echo '<div>';
				echo '<h3>', esc_html( get_the_title( $image ) ), '</h3>';

				$attribution = trim( get_post_meta( $image->ID, 'gmr_image_attribution', true ) );
				if ( ! empty( $attribution ) ) :
					echo '<h4>', esc_html( $attribution ), '</h4>';
				endif;

				echo '<p>', get_the_excerpt( $image ), '</p>';
			echo '</div>';

			if ( $index > 0 && $index % $ads_interval == 0 ) :
				do_action( 'dfp_tag', 'dfp_ad_inlist_infinite' );
			endif;
		echo '</li>';
	endforeach;

echo '</ul>';
