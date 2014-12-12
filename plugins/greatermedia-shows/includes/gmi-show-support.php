<?php

namespace GreaterMedia\Shows;

function supports_homepage( $show_id ) {
	return (bool) filter_var( get_post_meta( $show_id, 'show_homepage', true ), FILTER_VALIDATE_BOOLEAN );
}

function supports_galleries( $show_id ) {
	return (bool) filter_var( get_post_meta( $show_id, 'show_homepage_galleries', true ), FILTER_VALIDATE_BOOLEAN );
}

function supports_podcasts( $show_id ) {
	return (bool) filter_var( get_post_meta( $show_id, 'show_homepage_podcasts', true ), FILTER_VALIDATE_BOOLEAN );
}

function supports_videos( $show_id ) {
	return (bool) filter_var( get_post_meta( $show_id, 'show_homepage_videos', true ), FILTER_VALIDATE_BOOLEAN );
}

function about_link_html( $show_id, $link_text = 'About' ) {
	$class = 'about' == get_query_var( 'show_section' ) ? 'current-menu-item' : '';
	?><li class="<?php echo esc_attr( $class ); ?>"><a href="<?php echo get_the_permalink( $show_id ); ?>/about/"><?php echo esc_html( $link_text ); ?></a></li><?php
}

function galleries_link_html( $show_id, $link_text = 'Galleries' ) {
	if ( supports_galleries( $show_id ) ) {
		$class = 'galleries' == get_query_var( 'show_section' ) ? 'current-menu-item' : '';
		?><li class="<?php echo esc_attr( $class ); ?>"><a href="<?php echo get_the_permalink( $show_id ); ?>/galleries/"><?php echo esc_html( $link_text ); ?></a></li><?php
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

/**
 * Gets an instance of WP_Query that corresponds to the current page of the videos endpoints for shows
 *
 * @return \WP_Query
 */
function get_show_video_query() {
	$show_term = \TDS\get_related_term( get_the_ID() );
	$current_page = get_query_var( 'show_section_page' ) ?: 1;

	$video_args = array(
		'post_type' => 'post',
		'tax_query' => array(
			'relation' => 'AND',
			array(
				'taxonomy' => \ShowsCPT::SHOW_TAXONOMY,
				'field' => 'term_taxonomy_id',
				'terms' => $show_term->term_taxonomy_id,
			),
			array(
				'taxonomy' => 'post_format',
				'field'    => 'slug',
				'terms'    => array( 'post-format-video' ),
			),
		),
		'paged' => $current_page,
	);

	$video_query = new \WP_Query( $video_args );

	return $video_query;
}

/**
 * Gets an instance of WP_Query that corresponds to the current page of the galleries endpoints for shows
 *
 * @return \WP_Query
 */
function get_show_gallery_query() {
	$show_term = \TDS\get_related_term( get_the_ID() );
	$current_page = get_query_var( 'show_section_page' ) ?: 1;

	$album_args = array(
		'post_type' => 'albums', // todo is this post type coming from migration scripts? Need to dynamically grab this post type if we can
		'post_parent' => 0,
		'tax_query' => array(
			'relation' => 'AND',
			array(
				'taxonomy' => \ShowsCPT::SHOW_TAXONOMY,
				'field' => 'term_taxonomy_id',
				'terms' => $show_term->term_taxonomy_id,
			)
		),
		'paged' => $current_page,
	);

	$album_query = new \WP_Query( $album_args );

	return $album_query;
}

function get_show_events() {
	$show_term = \TDS\get_related_term( get_the_ID() );

	$event_args = array(
		'eventDisplay'=>'upcoming',
		'tax_query' => array(
			'relation' => 'AND',
			array(
				'taxonomy' => \ShowsCPT::SHOW_TAXONOMY,
				'field' => 'term_taxonomy_id',
				'terms' => $show_term->term_taxonomy_id,
			)
		),
		'posts_per_page' => 3,
	);

	$events = \tribe_get_events( $event_args );

	return $events;
}

function get_show_main_query() {
	$show_term = \TDS\get_related_term( get_the_ID() );
	$current_page = get_query_var( 'show_section_page' ) ?: 1;

	$show_args = array(
		'post_type' => 'post',
		'tax_query' => array(
			'relation' => 'AND',
			array(
				'taxonomy' => \ShowsCPT::SHOW_TAXONOMY,
				'field' => 'term_taxonomy_id',
				'terms' => $show_term->term_taxonomy_id,
			)
		),
		'paged' => $current_page,
	);

	$show_query = new \WP_Query( $show_args );

	return $show_query;
}
