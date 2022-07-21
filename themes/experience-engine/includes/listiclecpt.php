<?php
add_filter( 'bbgi_listicle_cotnent', 'ee_update_incontent_listicle', 10, 6 );

if ( ! function_exists( 'ee_get_listiclecpt_html' ) ) :
	function ee_get_listiclecpt_html( $cpt_post_object, $cpt_item_name,	$cpt_item_description, $cpt_item_order, $cpt_item_type, $source_post_object = null, $from_embed = false ) {
		$cpt_image_slug = get_query_var( 'view' );
		$current_post_id = get_post_thumbnail_id ($cpt_post_object);
		$id_pretext = $from_embed ? "embed-listicle" : "listicle";

		$ads_interval = filter_var( get_field( 'images_per_ad', $cpt_post_object ), FILTER_VALIDATE_INT, array( 'options' => array(
			'min_range' => 1,
			'max_range' => 100,
			'default'   => 3,
		) ) );

		ob_start();

		$checkID = $cpt_post_object->ID;
		if( !empty($source_post_object) && $source_post_object !== null ) {
			$checkID = $source_post_object->ID;
		}

		if(get_field( 'display_segmentation', $checkID )) {
			$segmentation_ordering_type = get_field( 'segmentation_ordering', $checkID );
			if($segmentation_ordering_type == 'header') {
				$header_items = array_keys( array_filter ($cpt_item_type, function ($var) { return ($var == "header"); } ) );
				$total_segment_header = count ( $header_items );
				
				if($total_segment_header > 0) {
					echo '<div style="padding: 1rem 0 1rem 0; position: sticky; top: 0; background-color: white; z-index: 1;">';
					$header_index = 1;
					for ($shi=1; $shi <= $total_segment_header; $shi++) {
						if($cpt_item_name[$header_items[$shi-1]] !== "") {
							echo '<button onclick=" scrollToSegmentation(\''.$id_pretext. '\', null, ' . $header_index . '); " class="btn" style="display: inline-block; color: white;margin-bottom: 0.5rem;margin-right: 1rem;">'. $cpt_item_name[$header_items[$shi-1]] . '</button>';
							$header_index++;
						}
					}
					echo "</div>";
				}
			} else {
				$segment_item_total = count ( array_filter ($cpt_item_type, function ($var) { return ($var !== "header"); } ) );
				$total_segment = ceil( $segment_item_total / 10 );
				$is_desc = ($segmentation_ordering_type != '' && $segmentation_ordering_type == 'desc') ? 1 : 0;
				$start_index = $is_desc ? $total_segment : 1;
	
				if($total_segment > 0) {
					echo '<div style="padding: 1rem 0 1rem 0; position: sticky; top: 0; background-color: white; z-index: 1;">';
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

		echo '<ul class="listicle-main-ul-item">';

		$segment_item_index = 0;
		$segment_header_index = 0;
		foreach ( $cpt_item_name as $index => $cpt_item_name_data ) {
			if( isset( $cpt_item_name_data ) && $cpt_item_name_data != "" ) {
				$cpt_tracking_code = $cpt_item_order[$index]+1 ;
				if( $cpt_item_type[$index] == 'header' ) {
					$segment_header_index++;
					$segment_li_class = $id_pretext."-segment-header-item-".$segment_header_index;
				} else {
					$segment_item_index++;
					$segment_li_class = $id_pretext."-segment-item-".$segment_item_index;
				}

				echo '<li id="', $segment_li_class, '" class="listicle-item', $cpt_image_slug == $cpt_tracking_code ? ' scroll-to' : '', $cpt_item_type[$index] == 'header' ? ' listicle-header-item' : '', '">';
					// Start code for listicle meta data
					echo '<div class="am-meta">';
						echo '<div class="wrapper">';
							echo '<div class="caption">';
								if($cpt_item_type[$index] == "header") {
									echo '<h2><span>', $cpt_item_name_data, '<span></h2>';
								} else {
									echo '<h3>', $cpt_item_name_data, '</h3>';
								}

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

								$is_common_mobile = ee_is_common_mobile();
								if($is_common_mobile){
									echo '<div class="common-mobile-ga-info track" data-location="' . esc_attr( $tracking_url ) . '"></div>';
								}

								$amItemImageType = '<div class="am_imagecode">' . $image_html . '</div>';
									echo $amItemImageType;

									if( isset( $cpt_item_description[$index] ) && $cpt_item_description[$index] !== "" ) {
									echo '<div class="am-meta-item-description">', apply_filters('the_content', $cpt_item_description[$index]),'</div>';
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

if ( ! function_exists( 'ee_update_incontent_listicle' ) ) :
	function ee_update_incontent_listicle( $cpt_post_object, $cpt_item_name, $cpt_item_description, $cpt_item_order, $cpt_item_type, $source_post_object ) {
		// do not render listicle if it has been called before <body> tag
		if ( ! did_action( 'beasley_after_body' ) ) {
			return '<!-- -->';
		}

		$html = ee_get_listiclecpt_html( $cpt_post_object, $cpt_item_name, $cpt_item_description, $cpt_item_order, $cpt_item_type, $source_post_object, true );

		// we need to to inject embed code later
		$placeholder = '<div><!-- listicle:' . sha1( $html ) . ' --></div>';
		$replace_filter = function( $content ) use ( $placeholder, $html ) {
			return str_replace( $placeholder, $html, $content );
		};

		add_filter( 'the_content', $replace_filter, 150 );

		return $placeholder;
	}
endif;
