<?php

add_filter( 'bbgi_gallery_cotnent', 'ee_update_incontent_gallery', 10, 4 );

if ( ! function_exists( 'ee_setup_gallery_view_metadata' ) ) :
	function ee_setup_gallery_view_metadata() {
		if ( ! class_exists( '\GreaterMediaGallery' ) ) {
			return;
		}

		$view = get_query_var( 'view' );
		if ( empty( $view ) ) {
			return;
		}

		$featured_image = $dimension_width = $dimension_height = false;
		$current_gallery = get_queried_object();
		$ids = \GreaterMediaGallery::get_attachment_ids_for_post( $current_gallery );
		if ( ! is_array( $ids ) ) {
			$ids = array();
		}

		$images = array_values( array_filter( array_map( 'get_post', array_values( $ids ) ) ) );
		if ( ! empty( $images ) ) {
			$view = strtolower( $view );
			foreach ( $images as $image ) {
				if ( strtolower( $image->post_name ) == $view ) {
					$src = wp_get_attachment_image_src( $image->ID, 'full' );
					if ( ! empty( $src ) && count( $src ) >= 3 ) {
						$featured_image = $src[0];
						$dimension_width = $src[1];
						$dimension_height = $src[2];
						break;
					}
				}
			}
		}

		if ( ! empty( $featured_image ) ) :
			add_action( 'wpseo_add_opengraph_images', function( \WPSEO_OpenGraph_Image $opengraph ) use ( $featured_image ) {
				$opengraph->add_image( $featured_image );
				add_filter( 'wpseo_opengraph_image', '__return_empty_string' );
			} );

			add_filter( 'wpseo_og_og_image', function( $content ) use ( $dimension_width, $dimension_height ) {
				! empty( $dimension_width )  && printf( '<meta property="og:image:width" content="%s">', esc_attr( $dimension_width ) );
				! empty( $dimension_height ) && printf( '<meta property="og:image:height" content="%s">', esc_attr( $dimension_height ) );
				return $content;
			} );

			add_filter( 'wpseo_twitter_image', function( $image ) use ( $featured_image ) {
				return ! empty( $featured_image ) ? $featured_image : $image;
			} );
		endif;

		$permalink = untrailingslashit( get_permalink( $current_gallery->ID ) );
		$new_url = sprintf( '%s/view/%s/', $permalink, urlencode( $view ) );
		$replace_url = function() use ( $new_url ) {
			return $new_url;
		};

		add_filter( 'wpseo_og_og_url', $replace_url );
		add_filter( 'wpseo_canonical', $replace_url );
	}
endif;

if ( ! function_exists( 'ee_get_galleries_query' ) ) :
	function ee_get_galleries_query( $album = null, $args = array() ) {
		$album = get_post( $album );
		$args = wp_parse_args( $args );

		return new \WP_Query( array_merge( $args, array(
			'post_type'   => 'gmr_gallery',
			'post_parent' => $album->ID,
		) ) );
	}
endif;

if ( ! function_exists( 'ee_get_gallery_image_html' ) ) :
	function ee_get_gallery_image_html( $image, $gallery, $is_sponsored = false, $is_first = false, $gallery_author = '' ) {
		static $urls = array();

		if ( empty( $urls[ $gallery->ID ] ) ) {
			$urls[ $gallery->ID ] = trailingslashit( get_permalink( $gallery->ID ) );
		}

		$image_full_url = $urls[ $gallery->ID ] . 'view/' . urlencode( $image->post_name ) . '/';
		$tracking_url = ! $is_first ? $image_full_url : '';
		$update_lazy_image = function( $html ) use ( $urls,  $gallery ) {
			return str_replace( '<div ', '<div data-autoheight="1" data-referrer="' . esc_attr( $urls[ $gallery->ID ] ) . '" ', $html );
		};

		add_filter( '_ee_the_lazy_image', $update_lazy_image );
		$image_html = ee_the_lazy_image( $image->ID, false );
		remove_filter( '_ee_the_lazy_image', $update_lazy_image );

		if ( empty( $image_html ) ) {
			return false;
		}

		$title = get_the_title( $image );
		$is_common_mobile = ee_is_common_mobile();

		ob_start();

		echo $image_html;
		if($is_common_mobile){
			echo '<div class="common-mobile-ga-info track" data-embed-author="' . esc_attr( $gallery_author ) . '" data-location="' . esc_attr( $tracking_url ) . '"></div>';
		} else {
			echo '<div class="ga-track-location" data-referrer="' . esc_attr( $urls[ $gallery->ID ] ) . '" data-author="' . esc_attr( $gallery_author ) .'" data-tracking="' . esc_attr( $tracking_url ) . '"></div>';
		}

		echo '<div class="gallery-meta">';
			echo '<div class="wrapper">';
				echo '<div class="caption">';
					echo '<h3>', esc_html( $title ), '</h3>';

					echo '<div class="share-wrap">';

						if ( ! $is_sponsored ) :
							if ( false === get_field( 'hide_download_link', $gallery ) ) :
								echo '<a class="share-download-button" href="', esc_url( wp_get_attachment_image_url( $image->ID, 'full' ) ), '" class="-download" download target="_blank" rel="noopener">Download</a>';
							endif;

							if ( ! get_field( 'hide_social_share', $gallery ) ) :
								$url = get_field( 'share_photos', $gallery ) ? $image_full_url : $urls[ $gallery->ID ];
								echo '<div class="share-wrap-icons">';
									echo '<span class="label">Share</span>';
									ee_the_share_buttons( $url, $title );
								echo '</div>';
							endif;
						endif;

					echo '</div>';

				echo '</div>';

				echo '<p class="excerpt">', get_the_excerpt( $image ), '</p>';

			echo '</div>';

		echo '</div>';

		return ob_get_clean();
	}
endif;

if ( ! function_exists( 'ee_get_gallery_html' ) ) :
	function ee_get_gallery_html( $gallery, $ids, $from_embed = false, $embed_gallery_object = null ) {
		$gallery_author = '';
		$sponsored_image = get_field( 'sponsored_image', $gallery );
		$id_pretext = $from_embed ? "embed-gallery" : "gallery";
		if ( ! empty( $sponsored_image ) ) {
			array_unshift( $ids, $sponsored_image );
		}

		$images = array_values( array_filter( array_map( 'get_post', array_values( $ids ) ) ) );
		if ( empty( $images ) ) {
			return false;
		}

		$image_slug = get_query_var( 'view' );

		$ads_interval = filter_var( get_field( 'images_per_ad', $gallery ), FILTER_VALIDATE_INT, array( 'options' => array(
			'min_range' => 1,
			'max_range' => 100,
			'default'   => 3,
		) ) );

		ob_start();

		// Add segment navigation before start display the list
		if(get_field( 'display_segmentation', $gallery->ID )) {
			$segmentation_ordering_type = get_field( 'segmentation_ordering', $gallery->ID );

			$total_segment = ceil( count($images) / 10 );
			$is_desc = ($segmentation_ordering_type != '' && $segmentation_ordering_type == 'desc') ? 1 : 0;
			$start_index = $is_desc ? $total_segment : 1;

			if( $segmentation_ordering_type !== 'header' && $total_segment > 0 ) {
				echo '<div style="padding: 1rem 0 1rem 0; position: sticky; top: 0; background-color: white; z-index: 1;">';

				for ($i=1; $i <= $total_segment; $i++) {
					$diff = count($images) - (( $i - 1 ) * 10);
					$diff = ($diff % 10 == 0) ? $diff - 1 : $diff;
					$scroll_to = $is_desc ? ( floor( $diff / 10 ) * 10 ) : ( ($i - 1) * 10 );

					$from_display = $is_desc ? ( $start_index * 10 ) : ( ( ($start_index - 1) * 10 ) + 1 );
					$to_display =  $is_desc ? ( ( ($start_index - 1) * 10 ) + 1 ) : ( $start_index * 10 );

					echo '<button onclick=" scrollToSegmentation(\''.$id_pretext. '\', ' . ( $scroll_to + 1 ) . '); " class="btn" style="display: inline-block; color: white;margin-bottom: 0.5rem;margin-right: 1rem;">'. $from_display . ' - ' . $to_display . '</button>';
					$start_index = $is_desc ? ($start_index - 1) : ($start_index + 1);
				}
				echo "</div>";
			}
		}

		if(isset($embed_gallery_object) && !empty($embed_gallery_object)) {
			$primary_author = get_field( 'primary_author_cpt', $embed_gallery_object );
			$primary_author = $primary_author ? $primary_author : $embed_gallery_object->post_author;

			$gallery_author = get_the_author_meta( 'login', $primary_author);
		}

		echo '<ul class="gallery-listicle">';

		$segment_gallery_item = 0;
		foreach ( $images as $index => $image ) {
			$html = ee_get_gallery_image_html(
				$image,
				$gallery,
				$sponsored_image == $image->ID,
				$index == 0,
				$gallery_author
			);

			if ( ! empty( $html ) ) {
				$segment_gallery_item++;
				echo '<li id="', $id_pretext, '-segment-item-', $segment_gallery_item,'" class="gallery-listicle-item', $image_slug == $image->post_name ? ' scroll-to' : '', '">';
					echo $html;

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

if ( ! function_exists( 'ee_update_incontent_gallery' ) ) :
	function ee_update_incontent_gallery( $html, $gallery, $ids, $embed_gallery_object = null ) {
		// do not render gallery if it has been called before <body> tag
		if ( ! did_action( 'beasley_after_body' ) ) {
			return '<!-- -->';
		}

		$html = ee_get_gallery_html( $gallery, $ids, true, $embed_gallery_object );

		// we need to to inject embed code later
		$placeholder = '<div><!-- gallery:' . sha1( $html ) . ' --></div>';
		$replace_filter = function( $content ) use ( $placeholder, $html ) {
			return str_replace( $placeholder, $html, $content );
		};

		add_filter( 'the_content', $replace_filter, 150 );

		return $placeholder;
	}
endif;
