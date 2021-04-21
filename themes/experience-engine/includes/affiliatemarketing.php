<?php

if ( ! function_exists( 'ee_get_affiliatemarketing_html' ) ) :
	function ee_get_affiliatemarketing_html( $affiliatemarketing_post_object, $am_item_name, $am_item_description, $am_item_photo, $am_item_buttontext, $am_item_buttonurl, $am_item_getitnowfromname, $am_item_getitnowfromurl ) {
		$am_image_slug = get_query_var( 'view' );

		$ads_interval = filter_var( get_field( 'images_per_ad', $affiliatemarketing_post_object ), FILTER_VALIDATE_INT, array( 'options' => array(
			'min_range' => 1,
			'max_range' => 100,
			'default'   => 3,
		) ) );

		ob_start();

		echo '<ul class="affiliate-marketingmeta">';

		foreach ( $am_item_name as $index => $am_item_name_data ) {
			if( isset( $am_item_name_data ) && $am_item_name_data != "" ) {
				if( isset( $am_item_photo[$index] ) && $am_item_photo[$index] !== '' )
				{
					$images_details = get_post( $am_item_photo[$index] );
					$amimage_title	= $images_details->post_name;
				} else {
					$amimage_title	= '';
				}
				echo '<li class="affiliate-marketingmeta-item', $am_image_slug == $amimage_title ? ' scroll-to' : '', '">';
					// Start code for Affiliate marketing meta data
					echo '<div class="am-meta">';
						echo '<div class="wrapper">';
							echo '<div class="caption">';
								echo '<h3>', $am_item_name_data, '</h3>';
								
								static $urls = array();

								if ( empty( $urls[ $affiliatemarketing_post_object->ID ] ) ) {
									$urls[ $affiliatemarketing_post_object->ID ] = trailingslashit( get_permalink( $affiliatemarketing_post_object->ID ) );
								}

								$image_full_url = $urls[ $affiliatemarketing_post_object->ID ] . 'view/' . urlencode( $amimage_title ) . '/';
								$tracking_url = ! $is_first ? $image_full_url : '';
								$update_lazy_image = function( $html ) use ( $tracking_url ) {
									return str_replace( '<div ', '<div data-autoheight="1" data-tracking="' . esc_attr( $tracking_url ) . '" ', $html );
								};
								add_filter( '_ee_the_lazy_image', $update_lazy_image );
								$image_html = ee_the_lazy_image( $am_item_photo[$index], false );
								remove_filter( '_ee_the_lazy_image', $update_lazy_image );

								if ( ! empty( $image_html ) ) {
									echo '<div>', $image_html, '</div>';
									
									echo '<div class="share-wrap">';

											if ( ! get_field( 'hide_social_share', $affiliatemarketing_post_object ) ) :
												$url = get_field( 'share_photos', $affiliatemarketing_post_object ) ? $image_full_url : $urls[ $affiliatemarketing_post_object->ID ];
												echo '<div class="share-wrap-icons">';
													echo '<span class="label">Share</span>';
													ee_the_share_buttons( $url, $am_item_name_data );
												echo '</div>';
											endif;
				
									echo '</div>';
								}
								if( isset( $am_item_description[$index] ) && $am_item_description[$index] !== "" ) {
									echo '<div>', $am_item_description[$index],'</div>';
								}
								echo '<div class="shop-button">';
									if( isset( $am_item_description[$index] ) && $am_item_description[$index] !== "" ) {
										echo '<div class="get-it-now-meta"> Get it now from <a class="get-it-now-button" href="', $am_item_getitnowfromurl[$index], '" target="_blank" rel="noopener">', $am_item_getitnowfromname[$index], '</a></div>';
									}
									if( isset( $am_item_buttontext[$index] ) && $am_item_buttontext[$index] !== "" )
									{
										$amitembuttonurl = $am_item_buttonurl[$index] 
										?	$amitembuttonurl = $am_item_buttonurl[$index] 
										: $amitembuttonurl = '#';	
											echo '<div class="shop-now-button-meta">', '<a class="shop-now-button" href="', $amitembuttonurl, '" target="_blank" rel="noopener">', $am_item_buttontext[$index], '</a>', '</div>';
									}
								echo '</div>';
							echo '</div>';
						echo '</div>';
					echo '</div>';

					/* if ( $index > 0 && ( $index + 1 ) % $ads_interval == 0 ) :
						do_action( 'dfp_tag', 'in-list-affiliate-marketing' );
					endif; */ 
				echo '</li>';
			}
		}

		echo '</ul>';

		return ob_get_clean();
	}
endif;

if ( ! function_exists( '_ee_the_affiliate_marketing_image' ) ) :
	function _ee_the_affiliate_marketing_image( $url, $width, $height, $alt = '', $attribution = '' ) {
		$is_jacapps = ee_is_jacapps();

		$image = sprintf(
			$is_jacapps
				? '<div class="non-lazy-image"><img src="%s" width="%s" height="%s" alt="%s"><div class="non-lazy-image-attribution">%s</div></div>'
				: '<div class="lazy-image" data-src="%s" data-width="%s" data-height="%s" data-alt="%s" data-attribution="%s"></div>',
			esc_attr( $url ),
			esc_attr( $width ),
			esc_attr( $height ),
			esc_attr( $alt ),
			esc_attr( $attribution )
		);

		$image = apply_filters( '_ee_the_affiliate_marketing_image', $image, $is_jacapps, $url, $width, $height, $alt );

		return $image;
	}
endif;

if ( ! function_exists( 'ee_the_affiliate_marketing_image' ) ) :
	function ee_the_affiliate_marketing_image( $image_id, $echo = true ) {
		$html = '';
		if ( ! empty( $image_id ) ) {
			$alt = trim( strip_tags( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ) );
			$attribution = get_post_meta( $image_id, 'gmr_image_attribution', true );

			if ( ee_is_jacapps() ) {
				$width = 800;
				$height = 500;
				$url = bbgi_get_image_url( $image_id, $width, $height );

				$html = _ee_the_affiliate_marketing_image( $url, $width, $height, $alt, $attribution );
			} else {
				$img = wp_get_attachment_image_src( $image_id, 'original' );
				if ( ! empty( $img ) ) {
					$html = _ee_the_affiliate_marketing_image( $img[0], $img[1], $img[2], $alt, $attribution );
				}
			}
		}

		if ( $echo ) {
			echo $html;
		}

		return $html;
	}
endif;
