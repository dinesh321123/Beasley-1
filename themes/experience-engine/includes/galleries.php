<?php

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
	function ee_get_gallery_image_html( $image, $gallery, $is_sponsored = false ) {
		static $urls = array();

		$image_html = ee_the_lazy_image( $image->ID, false );
		if ( empty( $image_html ) ) {
			return false;
		}

		$title = get_the_title( $image );
		$attribution = trim( get_post_meta( $image->ID, 'gmr_image_attribution', true ) );
		if ( empty( $urls[ $gallery->ID ] ) ) {
			$urls[ $gallery->ID ] = trailingslashit( get_permalink( $gallery->ID ) );
		}

		ob_start();

		echo $image_html;

		echo '<div>';
			echo '<h3>', esc_html( $title ), '</h3>';
			if ( ! empty( $attribution ) ) :
				echo '<h4>', esc_html( $attribution ), '</h4>';
			endif;

			if ( ! $is_sponsored ) :
				if ( ! get_field( 'hide_download_link', $gallery ) ) :
					echo '<p>';
						echo '<a href="', esc_url( wp_get_attachment_image_url( $image->ID, 'full' ) ), '" class="-download" download target="_blank" rel="noopener">download</a>';
					echo '</p>';
				endif;

				if ( ! get_field( 'hide_social_share', $gallery ) ) :
					$url = get_field( 'share_photos', $gallery )
						? $urls[ $gallery->ID ] . 'view/' . urlencode( $image->post_name ) . '/'
						: $urls[ $gallery->ID ];

					ee_the_share_buttons( $url, $title );
				endif;
			endif;

			echo '<p>', get_the_excerpt( $image ), '</p>';
		echo '</div>';

		return ob_get_clean();
	}
endif;

if ( ! function_exists( 'ee_get_gallery_html' ) ) :
	function ee_get_gallery_html( $gallery, $ids ) {
		$sponsored_image = get_field( 'sponsored_image', $gallery );
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

		echo '<ul class="gallery-listicle">';

		foreach ( $images as $index => $image ) {
			$html = ee_get_gallery_image_html(
				$image,
				$gallery,
				$sponsored_image == $image->ID
			);

			if ( ! empty( $html ) ) {
				echo '<li class="gallery-listicle-item', $image_slug == $image->post_name ? ' scroll-to' : '', '">';
					echo $html;

					if ( $index > 0 && $index % $ads_interval == 0 ) :
						do_action( 'dfp_tag', 'dfp_ad_inlist_infinite' );
					endif;
				echo '</li>';
			}
		}

		echo '</ul>';

		return ob_get_clean();
	}
endif;
