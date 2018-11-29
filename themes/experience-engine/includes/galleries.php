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
				if ( ! empty( $dimension_width ) ) {
					echo '<meta property="og:image:width" content="', esc_attr( $dimension_width ), '">', "\n";
				}

				if ( ! empty( $dimension_height ) ) {
					echo '<meta property="og:image:height" content="', esc_attr( $dimension_height ), '">', "\n";
				}

				return $content;
			} );

			add_filter( 'wpseo_twitter_image', function( $image ) use ( $featured_image ) {
				return ! empty( $featured_image ) ? $featured_image : $image;
			} );
		endif;

		add_filter( 'wpseo_canonical', function() use ( $view, $current_gallery ) {
			return sprintf(
				'%s/view/%s/',
				untrailingslashit( get_permalink( $current_gallery->ID ) ),
				urlencode( $view )
			);
		} );
	}
endif;
