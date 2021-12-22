<?php

if ( ! function_exists( 'ee_get_listiclecpt_html' ) ) :
	function ee_get_listiclecpt_html( $cpt_post_object, $cpt_item_name,	$cpt_item_description, $cpt_item_order ) {
		$cpt_image_slug = get_query_var( 'view' );
		$current_post_id = get_post_thumbnail_id ($cpt_post_object);

		$ads_interval = filter_var( get_field( 'images_per_ad', $cpt_post_object ), FILTER_VALIDATE_INT, array( 'options' => array(
			'min_range' => 1,
			'max_range' => 100,
			'default'   => 3,
		) ) );

		ob_start();

		$total_segment = ceil( count($cpt_item_name) / 10 );
		if(get_field( 'display_segmentation', $cpt_post_object->ID )) {
			$is_desc = (get_field( 'segmentation_ordering', $cpt_post_object->ID ) != '' && get_field( 'segmentation_ordering', $cpt_post_object->ID ) == 'desc') ? 1 : 0;
			$start_index = $is_desc ? $total_segment : 1;

			echo '<div style="padding: 1rem 0 1rem 0; position: sticky; top: 0; background-color: white; z-index: 1;">';

			for ($i=1; $i <= $total_segment; $i++) {
				$diff = count($cpt_item_name) - ($start_index * 10);
				$scroll_to = $is_desc ? ( $diff < 0 ? 0 : $diff  ) : ( ($i - 1) * 10 );

				$from_display = $is_desc ? ( $start_index * 10 ) : ( ( ($start_index - 1) * 10 ) + 1 );
				$to_display =  $is_desc ? ( ( ($start_index - 1) * 10 ) + 1 ) : ( $start_index * 10 );
				
				echo '<button onclick=" scrollToSegmentation(' . ( $cpt_item_order[ $scroll_to ] + 1 ) .'); " class="btn" style="display: inline-block; color: white;margin-bottom: 0.5rem;margin-right: 1rem;">'. $from_display . ' - ' . $to_display . '</button>';
				
				$start_index = $is_desc ? ($start_index - 1) : ($start_index + 1);
			}
			echo "</div>";
		}

		echo '<ul class="listicle-main-ul-item">';

		foreach ( $cpt_item_name as $index => $cpt_item_name_data ) {
			if( isset( $cpt_item_name_data ) && $cpt_item_name_data != "" ) {
				$cpt_tracking_code = $cpt_item_order[$index]+1 ;

				echo '<li id="segment-item-', $cpt_tracking_code, '" class="listicle-item', $cpt_image_slug == $cpt_tracking_code ? ' scroll-to' : '', '">';
					// Start code for listicle meta data
					echo '<div class="am-meta">';
						echo '<div class="wrapper">';
							echo '<div class="caption">';
								echo '<h3>', $cpt_item_name_data, '</h3>';

								static $urls = array();

								if ( empty( $urls[ $cpt_post_object->ID ] ) ) {
									$urls[ $cpt_post_object->ID ] = trailingslashit( get_permalink( $cpt_post_object->ID ) );
								}
								$image_full_url = $urls[ $cpt_post_object->ID ] . 'view/' . urlencode( $cpt_tracking_code ) . '/';
								$tracking_url = ! $is_first ? $image_full_url : '';
								$update_lazy_image = function( $html ) use ( $tracking_url ) {
									return str_replace( '<div ', '<div data-autoheight="1" data-tracking="' . esc_attr( $tracking_url ) . '" ', $html );
								};

								add_filter( '_ee_the_lazy_image', $update_lazy_image );
								$image_html = ee_the_lazy_image( $current_post_id, false );
								remove_filter( '_ee_the_lazy_image', $update_lazy_image );

								$amItemImageType = '<div class="am_imagecode">' . $image_html . '</div>';
									echo $amItemImageType;

									if( isset( $cpt_item_description[$index] ) && $cpt_item_description[$index] !== "" ) {
									echo '<div>', apply_filters('the_content', $cpt_item_description[$index]),'</div>';
								}
							echo '</div>';
						echo '</div>';
					echo '</div>';

					if ( $index > 0 && ( $index + 1 ) % $ads_interval == 0 ) :
						do_action( 'dfp_tag', 'in-list-gallery' );
					endif;
				echo '</li>';
			}
		}
		echo '</ul>';
		return ob_get_clean();
	}
endif;
