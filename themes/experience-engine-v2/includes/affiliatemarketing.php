<?php
add_filter( 'bbgi_am_cotnent', 'ee_update_incontent_am', 10, 15 );

if ( ! function_exists( 'ee_get_affiliatemarketing_html' ) ) :
	function ee_get_affiliatemarketing_html( $affiliatemarketing_post_object, $am_item_name, $am_item_description, $am_item_photo, $am_item_imagetype, $am_item_imagecode,	$am_item_order, $am_item_unique_order, $am_item_getitnowtext, $am_item_buttontext, $am_item_buttonurl, $am_item_getitnowfromname, $am_item_getitnowfromurl, $am_item_type, $source_post_object = null, $from_embed = false  ) {
		$am_image_slug = get_query_var( 'view' );
		$current_post_id = get_post_thumbnail_id ($affiliatemarketing_post_object);
		$id_pretext = $from_embed ? "embed-am" : "am";

		$ads_interval = filter_var( get_field( 'images_per_ad', $affiliatemarketing_post_object ), FILTER_VALIDATE_INT, array( 'options' => array(
			'min_range' => 1,
			'max_range' => 100,
			'default'   => 3,
		) ) );

		ob_start();

		$checkID = $affiliatemarketing_post_object->ID;
		if( !empty($source_post_object) && $source_post_object !== null ) {
			$checkID = $source_post_object->ID;
		}

		if(get_field( 'display_segmentation', $checkID )) {
			$segmentation_ordering_type = get_field( 'segmentation_ordering', $checkID );
			if($segmentation_ordering_type == 'header') {
				$header_items = array_keys( array_filter ($am_item_type, function ($var) { return ($var == "header"); } ) );
				$total_segment_header = count ( $header_items );

				if($total_segment_header > 0) {
					echo '<div class="pagination-head-section" style="padding: 1rem 0 1rem 0; position: sticky; top: 0; background-color: white; z-index: 1;">';
					$header_index = 1;
					for ($shi=1; $shi <= $total_segment_header; $shi++) {
						if($am_item_name[$header_items[$shi-1]] !== "") {
							echo '<button onclick=" scrollToSegmentation(\''.$id_pretext. '\', null, ' . $header_index . '); " class="btn" style="display: inline-block; color: white;margin-bottom: 0.5rem;margin-right: 1rem;">'. $am_item_name[$header_items[$shi-1]] . '</button>';
							$header_index++;
						}
					}
					echo "</div>";
				}
			} else {
				$segment_item_total = count ( array_filter ($am_item_type, function ($var) { return ($var !== "header"); } ) );
				$total_segment = ceil( $segment_item_total / 10 );
				$is_desc = ($segmentation_ordering_type != '' && $segmentation_ordering_type == 'desc') ? 1 : 0;
				$start_index = $is_desc ? $total_segment : 1;

				if($total_segment > 0) {
					echo '<div class="pagination-head-section" style="padding: 1rem 0 1rem 0; position: sticky; top: 0; background-color: white; z-index: 1;">';

					for ($i=1; $i <= $total_segment; $i++) {
								$diff = $segment_item_total - (( $i - 1 ) * 10);
								$diff = ($diff % 10 == 0) ? $diff - 1 : $diff;
								$scroll_to = $is_desc ? ( floor( $diff / 10 ) * 10 ) : ( ($i - 1) * 10 );

						$from_display = $is_desc ? ( $start_index * 10 ) : ( ( ($start_index - 1) * 10 ) + 1 );
						$to_display =  $is_desc ? ( ( ($start_index - 1) * 10 ) + 1 ) : ( $start_index * 10 );

								echo '<button onclick=" scrollToSegmentation(\''.$id_pretext. '\', ' . ( $scroll_to + 1 ) .'); " class="btn" style="display: inline-block; color: white;margin-bottom: 0.5rem;margin-right: 1rem;">'. $from_display . ' - ' . $to_display . '</button>';
						$start_index = $is_desc ? ($start_index - 1) : ($start_index + 1);
					}
					echo "</div>";
				}
			}
		}

		echo '<ul class="affiliate-marketingmeta">';

		$segment_item_index = 0;
		$segment_header_index = 0;
		foreach ( $am_item_name as $index => $am_item_name_data ) {
			if( isset( $am_item_name_data ) && $am_item_name_data != "" ) {
				$am_tracking_code = $am_item_imagetype[$index] == 'imagecode' ? $am_item_unique_order[$index] : $am_item_order[$index]+1 ;
				if( $am_item_type[$index] == 'header' ) {
					$segment_header_index++;
					$segment_li_class = $id_pretext."-segment-header-item-".$segment_header_index;
				} else {
					$segment_item_index++;
					$segment_li_class = $id_pretext."-segment-item-".$segment_item_index;
				}
				if( isset( $am_item_photo[$index] ) && $am_item_photo[$index] !== '' )
				{
					$images_details = get_post( $am_item_photo[$index] );
					$amimage_title	= $images_details->post_name;
				} else {
					$amimage_title	= '';
				}
				$amitembuttonurl = $am_item_buttonurl[$index]
				?	$amitembuttonurl = $am_item_buttonurl[$index]
				: $amitembuttonurl = '#';
				echo '<li id="', $segment_li_class, '" class="affiliate-marketingmeta-item', $am_image_slug == $am_tracking_code ? ' scroll-to' : '', $am_item_type[$index] == 'header' ? ' am-header-item' : '', '">';
					// Start code for Affiliate marketing meta data
					echo '<div class="am-meta">';
						echo '<div class="wrapper">';
							echo '<div class="caption">';
								if($am_item_type[$index] == "header") {
									echo '<h2><span>', $am_item_name_data, '<span></h2>';
								} else {
								echo '<h3>', '<a href="', $amitembuttonurl, '" target="_blank" rel="noopener">', $am_item_name_data, '</a></h3>';
								}

								static $urls = array();

								if ( empty( $urls[ $affiliatemarketing_post_object->ID ] ) ) {
									$urls[ $affiliatemarketing_post_object->ID ] = trailingslashit( get_permalink( $affiliatemarketing_post_object->ID ) );
								}
								$image_full_url = $urls[ $affiliatemarketing_post_object->ID ] . 'view/' . urlencode( $am_tracking_code ) . '/';
								$tracking_url = ! $is_first ? $image_full_url : '';
								$update_lazy_image = function( $html ) {
									return str_replace( '<div ', '<div data-autoheight="1" ', $html );
								};

								add_filter( '_ee_the_lazy_image', $update_lazy_image );
								$image_html = ee_the_lazy_image( $am_item_imagetype[$index] == 'imagecode' && ! empty( $am_item_imagecode[$index] ) ? $current_post_id : $am_item_photo[$index], false );
								remove_filter( '_ee_the_lazy_image', $update_lazy_image );

								$is_common_mobile = ee_is_common_mobile();
								if($is_common_mobile){
									echo '<div class="common-mobile-ga-info track" data-location="' . esc_attr( $tracking_url ) . '"></div>';
								} else {
									echo '<div class="ga-track-location" data-tracking="' . esc_attr( $tracking_url ) . '"></div>';
								}
								$amItemImageType = "";
								if($am_item_imagetype[$index] == 'imagecode' && ! empty( $am_item_imagecode[$index] ) ) {
									$amItemImageType = '<div class="am_imagecode">' . $image_html . $am_item_imagecode[$index] . '</div>';
								}

								if ($am_item_imagetype[$index] == 'imageurl' && ! empty( $image_html ) ) {
									$amItemImageType = '<div>' . '<a href="' . $amitembuttonurl . '" target="_blank" rel="noopener">' . $image_html . '</a></div>';
								}

								if($amItemImageType != "") {
									echo $amItemImageType;
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
									echo '<div>', apply_filters('the_content', $am_item_description[$index]),'</div>';
								}

								if($am_item_type[$index] !== "header") {
								echo '<div class="shop-button">';
									if( isset( $am_item_getitnowfromname[$index] ) && $am_item_getitnowfromname[$index] !== "" ) {
										$get_it_now_from_url = $am_item_getitnowfromurl[$index] ? $am_item_getitnowfromurl[$index] : '#' ;

										echo '<div class="get-it-now-meta">', $am_item_getitnowtext[$index], ' <a class="get-it-now-button" href="', $get_it_now_from_url, '" target="_blank" rel="noopener">', $am_item_getitnowfromname[$index], '</a></div>';
									}
									if( isset( $am_item_buttontext[$index] ) && $am_item_buttontext[$index] !== "" )
									{
										echo '<div class="shop-now-button-meta">', '<a class="shop-now-button" href="', $amitembuttonurl, '" target="_blank" rel="noopener">', $am_item_buttontext[$index], '</a>', '</div>';
									}
								echo '</div>';
								}
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
		$is_common_mobile = ee_is_common_mobile();

		$image = sprintf(
			$is_common_mobile
				? '<div class="non-lazy-image"><img src="%s" width="%s" height="%s" alt="%s"><div class="non-lazy-image-attribution">%s</div></div>'
				: '<div class="lazy-image" data-src="%s" data-width="%s" data-height="%s" data-alt="%s" data-attribution="%s"></div>',
			esc_attr( $url ),
			esc_attr( $width ),
			esc_attr( $height ),
			esc_attr( $alt ),
			esc_attr( $attribution )
		);

		$image = apply_filters( '_ee_the_affiliate_marketing_image', $image, $is_common_mobile, $url, $width, $height, $alt );

		return $image;
	}
endif;

if ( ! function_exists( 'ee_the_affiliate_marketing_image' ) ) :
	function ee_the_affiliate_marketing_image( $image_id, $echo = true ) {
		$html = '';
		if ( ! empty( $image_id ) ) {
			$alt = trim( strip_tags( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ) );
			$attribution = get_post_meta( $image_id, 'gmr_image_attribution', true );

			if ( ee_is_common_mobile() ) {
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

if ( ! function_exists( 'ee_update_incontent_am' ) ) :
	function ee_update_incontent_am( $affiliatemarketing_post_object, $am_item_name, $am_item_description, $am_item_photo, $am_item_imagetype, $am_item_imagecode, $am_item_order, $am_item_unique_order, $am_item_getitnowtext, $am_item_buttontext, $am_item_buttonurl, $am_item_getitnowfromname, $am_item_getitnowfromurl, $am_item_type, $source_post_object ) {
		// do not render affiliate marketing if it has been called before <body> tag
		if ( ! did_action( 'beasley_after_body' ) ) {
			return '<!-- -->';
		}

		$html = ee_get_affiliatemarketing_html( $affiliatemarketing_post_object, $am_item_name, $am_item_description, $am_item_photo, $am_item_imagetype, $am_item_imagecode, $am_item_order, $am_item_unique_order, $am_item_getitnowtext, $am_item_buttontext, $am_item_buttonurl, $am_item_getitnowfromname, $am_item_getitnowfromurl, $am_item_type, $source_post_object, true );

		// we need to to inject embed code later
		$placeholder = '<div><!-- affiliate marketing:' . sha1( $html ) . ' --></div>';
		$replace_filter = function( $content ) use ( $placeholder, $html ) {
			return str_replace( $placeholder, $html, $content );
		};

		add_filter( 'the_content', $replace_filter, 150 );

		return $placeholder;
	}
endif;