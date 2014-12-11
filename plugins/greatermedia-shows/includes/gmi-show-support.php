<?php

namespace GreaterMedia\Shows;

function supports_homepage( $show_id ) {
	return (bool) filter_var( get_post_meta( $show_id, 'show_homepage', true ), FILTER_VALIDATE_BOOLEAN );
}

function supports_albums( $show_id ) {
	return (bool) filter_var( get_post_meta( $show_id, 'show_homepage_albums', true ), FILTER_VALIDATE_BOOLEAN );
}

function supports_podcasts( $show_id ) {
	return (bool) filter_var( get_post_meta( $show_id, 'show_homepage_podcasts', true ), FILTER_VALIDATE_BOOLEAN );
}

function supports_videos( $show_id ) {
	return (bool) filter_var( get_post_meta( $show_id, 'show_homepage_videos', true ), FILTER_VALIDATE_BOOLEAN );
}

function show_page_title( $show_id = null ) {
	if ( is_null( $show_id ) ) {
		$show_id = get_the_ID();
	}

	$section = get_query_var( 'show_section' );
	$base_title = get_the_title( $show_id );
	switch( $section ) {
		case 'about':
			$title = "About " . $base_title;
			break;
		case 'podcasts':
		case 'albums':
		case 'videos':
			$title = $base_title . " " . $section;
			break;
		default:
			$title = $base_title;
			break;
	}

	echo esc_html( $title );
}

function about_link_html( $show_id, $link_text = 'About' ) {
	$class = 'about' == get_query_var( 'show_section' ) ? 'current-menu-item' : '';
	?><li class="<?php echo esc_attr( $class ); ?>"><a href="<?php echo get_the_permalink( $show_id ); ?>/about/"><?php echo esc_html( $link_text ); ?></a></li><?php
}

function albums_link_html( $show_id, $link_text = 'Albums' ) {
	if ( supports_albums( $show_id ) ) {
		$class = 'albums' == get_query_var( 'show_section' ) ? 'current-menu-item' : '';
		?><li class="<?php echo esc_attr( $class ); ?>"><a href="<?php echo get_the_permalink( $show_id ); ?>/albums/"><?php echo esc_html( $link_text ); ?></a></li><?php
	}
}

function podcasts_link_html( $show_id, $link_text = 'Podcasts' ) {
	if ( supports_podcasts( $show_id ) ) {
		$class = 'podcasts' == get_query_var( 'show_section' ) ? 'current-menu-item' : '';
		?><li class="<?php echo esc_attr( $class ); ?>"><a href="<?php echo get_the_permalink( $show_id ); ?>/podcasts/"><?php echo esc_html( $link_text ); ?></a></li><?php
	}
}

function videos_link_html( $show_id, $link_text = 'Videos' ) {
	if ( supports_videos( $show_id ) ) {
		$class = 'videos' == get_query_var( 'show_section' ) ? 'current-menu-item' : '';
		?><li class="<?php echo esc_attr( $class ); ?>"><a href="<?php echo get_the_permalink( $show_id ); ?>/videos/"><?php echo esc_html( $link_text ); ?></a></li><?php
	}
}

/**
 * Gets pagination links for a specific show endpoint query.
 *
 * @param \WP_Query $query The query to get the pagination links for.
 *
 * @return array|string
 */
function get_show_endpoint_pagination_links( \WP_Query $query ) {
	$current_page = $query->get( 'paged' );
	$total_pages = $query->max_num_pages;

	$args = array(
		'current' => $current_page,
		'total' => $total_pages,
	);

	return paginate_links( $args );
}

/**
 * Gets an instance of WP_Query that corresponds to the current page of the podcast endpoints for shows
 *
 * @return \WP_Query
 */
function get_show_podcast_query() {
	$show_term = \TDS\get_related_term( get_the_ID() );
	$current_page = get_query_var( 'show_section_page' ) ?: 1;

	$podcast_args = array(
		'post_type' => \GMP_CPT::PODCAST_POST_TYPE,
		'tax_query' => array(
			array(
				'taxonomy' => \ShowsCPT::SHOW_TAXONOMY,
				'field' => 'term_taxonomy_id',
				'terms' => $show_term->term_taxonomy_id,
			),
		),
		'paged' => $current_page,
	);

	$podcast_query = new \WP_Query( $podcast_args );

	return $podcast_query;
}
