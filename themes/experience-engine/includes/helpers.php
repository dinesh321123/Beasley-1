<?php

add_filter( 'next_posts_link_attributes', 'ee_load_more_attributes' );
add_filter( 'get_the_archive_title', 'ee_update_archive_title' );

if ( ! function_exists( 'ee_get_date' ) ) :
	function ee_get_date( $timestamp, $gmt = 0 ) {
		$elapsed = current_time( 'timestamp', $gmt ) - $timestamp;
		$abs_elapsed = abs( $elapsed );

		if ( $abs_elapsed < DAY_IN_SECONDS ) {
			$text = '';
			if ( $abs_elapsed >= HOUR_IN_SECONDS ) {
				$number = floor( $abs_elapsed / HOUR_IN_SECONDS );
				$text = sprintf( $number == 1 ? 'an hour' : '%s hours', $number );
			} elseif ( $abs_elapsed >= MINUTE_IN_SECONDS ) {
				$number = floor( $abs_elapsed / MINUTE_IN_SECONDS );
				$text = sprintf( $number == 1 ? 'a minute' : '%d minutes', $number );
			} else {
				return 'just now';
			}

			return sprintf( $elapsed > 0 ? '%s ago' : 'in %s', $text );
		}

		$created_offset = $gmt
			? $timestamp + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS
			: $timestamp;

		$format = date( 'Y' ) == date( 'Y', $created_offset )
			? 'M jS'
			: 'M jS, Y';


		return date( $format, $created_offset );
	}
endif;

if ( ! function_exists( 'ee_the_date' ) ) :
	function ee_the_date( $post = null ) {
		$post = get_post( $post );
		if ( is_a( $post, '\WP_Post' ) ) {
			$created = mysql2date( 'G', $post->post_date_gmt );
			echo ee_get_date( $created, 1 );
		}
	}
endif;

if ( ! function_exists( 'ee_the_query_tiles' ) ) :
	function ee_the_query_tiles( $query ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'partials/tile', get_post_type() );
		}

		wp_reset_postdata();
	}
endif;

if ( ! function_exists( 'ee_load_more' ) ) :
	function ee_load_more( $query = null ) {
		if ( $query ) {
			$GLOBALS['wp_query'] = $query;
		}

		get_template_part( 'partials/load-more' );

		if ( $query ) {
			wp_reset_query();
		}
	}
endif;

if ( ! function_exists( 'ee_load_more_attributes' ) ) :
	function ee_load_more_attributes() {
		return 'class="load-more"';
	}
endif;

if ( ! function_exists( 'ee_is_first_page' ) ) :
	function ee_is_first_page() {
		return get_query_var( 'paged', 1 ) < 2;
	}
endif;

if ( ! function_exists( 'ee_update_archive_title' ) ) :
	function ee_update_archive_title( $title ) {
		if ( is_post_type_archive( 'tribe_events' ) ) {
			$title = 'Events';
		}

		$parts = explode( ':', $title, 2 );
		return array_pop( $parts );
	}
endif;

if ( ! function_exists( 'ee_the_subtitle' ) ) :
	function ee_the_subtitle( $subtitle ) {
		echo '<h2 class="section-head"><span>', esc_html( $subtitle ), '</span></h2>';
	}
endif;

if ( ! function_exists( 'ee_the_share_buttons' ) ) :
	function ee_the_share_buttons( $url = null, $title = null ) {
		$url = filter_var( $url, FILTER_VALIDATE_URL )
			? ' data-url="' . esc_attr( $url ) . '"'
			: '';

		$title = ! empty( $title )
			? ' data-title="' . esc_attr( trim( $title ) ) . '"'
			: '';

		echo '<div class="share-buttons"', $url, $title, '></div>';
	}
endif;

if ( ! function_exists( 'ee_filter_primary_category' ) ) :
	function ee_filter_primary_category( $categories, $post_id ) {
		$post = get_post( $post_id );
		$cat_id = get_post_meta( $post->ID, '_yoast_wpseo_primary_category', true );
		if ( $cat_id > 0 ) {
			foreach ( $categories as $category ) {
				if ( $category->term_id == $cat_id ) {
					return array( $category );
				}
			}
		}

		return array( current( $categories ) );
	}
endif;

if ( ! function_exists( 'ee_the_permalink' ) ) :
	function ee_the_permalink() {
		$post = get_post();
		if ( ! empty( $post->link ) ) {
			echo filter_var( $post->link, FILTER_VALIDATE_URL );
		} else {
			the_permalink( $post );
		}
	}
endif;

if ( ! function_exists( 'ee_is_network_domain' ) ) :
	function ee_is_network_domain( $url ) {
		static $domains = null;
		if ( is_null( $domains ) ) {
			$domains = wp_list_pluck( get_sites(), 'domain' );
		}

		return in_array( parse_url( $url, PHP_URL_HOST ), $domains );
	}
endif;
