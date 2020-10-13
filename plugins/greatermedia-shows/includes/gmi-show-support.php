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

function uses_custom_menu( $show_id ) {
	return (bool) filter_var( get_post_meta( $show_id, 'show_homepage_custom_menu', true ), FILTER_VALIDATE_BOOLEAN );
}

function assigned_custom_menu_id( $show_id ) {
	return (int) filter_var( get_post_meta( $show_id, 'show_assigned_custom_menu', true ), FILTER_VALIDATE_INT );
}

function available_nav_menus() {
	return wp_get_nav_menus();
}

function get_about_permalink( $show_id ) {
	return trailingslashit( get_the_permalink( $show_id ) ) . "about/";
}

function home_link_html( $show_id, $link_text = 'Home' ) {
	$class = get_post_type() === 'show' && '' === get_query_var( 'show_section' ) ? 'current-menu-item' : '';
	?><li class="<?php echo esc_attr( $class ); ?>"><a href="<?php echo esc_url( get_permalink( $show_id ) ); ?>"><?php echo esc_html( $link_text ); ?></a></li><?php
}

function about_link_html( $show_id, $link_text = 'About' ) {
	$class = 'about' == get_query_var( 'show_section' ) ? 'current-menu-item' : '';
	?><li class="<?php echo esc_attr( $class ); ?>"><a href="<?php echo esc_url( get_about_permalink( $show_id ) ); ?>"><?php echo esc_html( $link_text ); ?></a></li><?php
}

/**
 * Renders the article link HTML if single page that was linked to a
 * show. Outputs HTML and returns void.
 *
 * @param int $show_id The post ID of show
 * @param string $link_text Optional link text
 * @return void
 */
function article_link_html( $show_id, $link_text = 'Articles' ) {
	if ( is_singular() && get_post_type() === 'post' ) {
		?><li class="current-menu-item">
			<a href="<?php echo esc_url( get_permalink( get_the_ID() ) ); ?>">
				<?php echo esc_html( $link_text ); ?>
			</a>
		</li><?php
	}
}

function get_galleries_permalink( $show_id ) {
	return trailingslashit( get_the_permalink( $show_id ) ) . "galleries/";
}

function galleries_link_html( $show_id, $link_text = 'Galleries' ) {
	if ( supports_galleries( $show_id ) ) {
		$class = 'galleries' == get_query_var( 'show_section' ) || ( is_singular() && get_post_type() === 'gmr_gallery' )
			? 'current-menu-item'
			: '';

		?><li class="<?php echo esc_attr( $class ); ?>">
			<a href="<?php echo esc_url( get_galleries_permalink( $show_id ) ); ?>">
				<?php echo esc_html( $link_text ); ?>
			</a>
		</li><?php
	}
}

function get_podcasts_permalink( $show_id ) {
	return trailingslashit( get_the_permalink( $show_id ) ) . "podcasts/";
}

function podcasts_link_html( $show_id, $link_text = 'Podcasts' ) {
	if ( supports_podcasts( $show_id ) ) {
		$class = 'podcasts' == get_query_var( 'show_section' ) || ( is_singular() && get_post_type() === 'episode' )
			? 'current-menu-item'
			: '';

		?><li class="<?php echo esc_attr( $class ); ?>">
			<a href="<?php echo esc_url( get_podcasts_permalink( $show_id ) ); ?>">
				<?php echo esc_html( $link_text ); ?>
			</a>
		</li><?php
	}
}

function get_videos_permalink( $show_id ) {
	return trailingslashit( get_the_permalink( $show_id ) ) . "videos/";
}

function videos_link_html( $show_id, $link_text = 'Videos' ) {
	if ( supports_videos( $show_id ) ) {
		$class = 'videos' == get_query_var( 'show_section' ) ? 'current-menu-item' : '';
		?><li class="<?php echo esc_attr( $class ); ?>"><a href="<?php echo esc_url( get_videos_permalink( $show_id ) ); ?>"><?php echo esc_html( $link_text ); ?></a></li><?php
	}
}

function get_live_links_permalink( $show_id ) {
	return trailingslashit( get_the_permalink( $show_id ) ) . "live-links/";
}

// No live-links html because this doesn't show up in the show menu ever

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

function _get_show_children_ids( $type ) {
	$show_id = get_the_ID();
	$key = 'show-children-ids-' . $show_id . $type;

	$ids = wp_cache_get( $key, 'bbgi:show' );
	if ( empty( $ids ) ) {
		$query = new \WP_Query();
		$show = \TDS\get_related_term( $show_id );

		$args = array(
			'post_type'      => $type,
			'posts_per_page' => 500,
			'fields'         => 'ids',
			'tax_query'      => array(
				array(
					'taxonomy' => \ShowsCPT::SHOW_TAXONOMY,
					'field' => 'term_taxonomy_id',
					'terms' => $show->term_taxonomy_id,
				),
			),
		);

		$ids = $query->query( $args );
		wp_cache_set( $key, $ids, 'bbgi:show', HOUR_IN_SECONDS );
	}

	return $ids;
}

/**
 * Returns podcast ids associated with the current show.
 */
function get_show_podcast_ids() {
	return _get_show_children_ids( \GMP_CPT::PODCAST_POST_TYPE );
}

function get_show_album_ids() {
	return _get_show_children_ids( 'gmr_album' );
}

function _get_show_children_query( $type, $parents, $per_page ) {
	if ( ! empty( $parents ) ) {
		$current_page = get_query_var( 'paged', 1 );

		$args = array(
			'post_type'       => $type,
			'post_parent__in' => $parents,
			'paged'           => $current_page,
			'posts_per_page'  => $per_page,
		);

		$query = new \WP_Query( $args );

		return $query;
	} else {
		return new \WP_Query();
	}
}

/**
 * Gets an instance of WP_Query that corresponds to the current page of the podcast endpoints for shows
 *
 * @return \WP_Query
 */
function get_show_podcast_query( $per_page = 10 ) {
	$parents = get_show_podcast_ids();
	return _get_show_children_query( \GMP_CPT::EPISODE_POST_TYPE, $parents, $per_page );
}

/**
 * Gets an instance of WP_Query that corresponds to the current page of the videos endpoints for shows
 *
 * @return \WP_Query
 */
function get_show_video_query( $per_page = 10 ) {
	$show_term = \TDS\get_related_term( get_the_ID() );
	$current_page = get_query_var( 'paged', 1 );

	$video_args = array(
		'post_type'      => 'post',
		'paged'          => $current_page,
		'posts_per_page' => $per_page,
		'tax_query'      => array(
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
	);

	$video_query = new \WP_Query( $video_args );

	return $video_query;
}

/**
 * Gets an instance of WP_Query that corresponds to the current page of the galleries endpoints for shows
 *
 * @return \WP_Query
 */
function get_show_gallery_query( $per_page = 10 ) {
	$show_term = \TDS\get_related_term( get_the_ID() );
	$current_page = get_query_var( 'paged', 1 );

	$args = array(
		'post_type'      => 'gmr_gallery',
		'paged'          => $current_page,
		'posts_per_page' => $per_page,
		'tax_query'      => array(
			array(
				'taxonomy' => \ShowsCPT::SHOW_TAXONOMY,
				'field' => 'term_taxonomy_id',
				'terms' => $show_term->term_taxonomy_id,
			),
		),
	);

	return new \WP_Query( $args );
}

function get_show_events() {
	$show_term = \TDS\get_related_term( get_the_ID() );

	$event_args = array(
		'posts_per_page' => 3,
		'eventDisplay'   => 'list',
		'tax_query'      => array(
			'relation'   => 'AND',
			array(
				'taxonomy' => \ShowsCPT::SHOW_TAXONOMY,
				'field'    => 'term_taxonomy_id',
				'terms'    => $show_term->term_taxonomy_id,
			)
		),
		'posts_per_page' => 2,
	);

	if ( !function_exists( '\tribe_get_events' ) ) {
		return array();
	}

	$events = \tribe_get_events( $event_args );

	return $events;
}

function get_show_featured_query() {
	$curated_ids = explode( ',', get_post_meta( get_the_ID(), 'gmr_featured_post_ids', true ) );

	$args = array(
		'post__in'            => $curated_ids,
		'post_type'           => 'any', // since we have IDs
		'orderby'             => 'post__in',
		'ignore_sticky_posts' => true,
	);

	$query = new \WP_Query( $args );

	return $query;
}

function get_show_favorites_query() {
	$curated_ids = explode( ',', get_post_meta( get_the_ID(), 'gmr_favorite_post_ids', true ) );

	$args = array(
		'post__in'            => $curated_ids,
		'post_type'           => 'any', // since we have IDs
		'orderby'             => 'post__in',
		'ignore_sticky_posts' => true,
	);

	$query = new \WP_Query( $args );

	return $query;
}

function get_show_live_links_query( $show = null, $page = 1 ) {
	$show = get_post( $show );
	$episode = \gmrs_get_current_show_episode();

	$taxonomy = get_taxonomy( \ShowsCPT::SHOW_TAXONOMY );
	$term = \TDS\get_related_term( $show );

	$args = array(
		'post_type'			  => GMR_LIVE_LINK_CPT,
		'paged'               => $page,
		'posts_per_page'      => 10,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'tax_query'           => array(
			array(
				'taxonomy' => \ShowsCPT::SHOW_TAXONOMY,
				'terms'    => $term->term_id,
			),
		),
	);

	if ( $episode && $show->ID == $episode->post_parent && \gmrs_is_episode_onair( $episode ) ) {
		$args['date_query'] = array(
			array(
				'before' => $episode->post_date_gmt,
				'column' => 'post_date_gmt',
			),
		);
	}

	return new \WP_Query( $args );
}

function get_show_live_links_archive_query() {
	$episode = \gmrs_get_current_show_episode();

	$show_term = \TDS\get_related_term( get_the_ID() );

	$current_page = get_query_var( 'paged', 1 );

	$args = array(
		'post_type' => GMR_LIVE_LINK_CPT,
		'paged' => $current_page,
		'posts_per_page' => 20,
		'ignore_sticky_posts' => true,
		'tax_query' => array(
			array(
				'taxonomy' => \ShowsCPT::SHOW_TAXONOMY,
				'terms'    => $show_term->term_id,
			),
		),
	);

	if ( $episode && get_the_ID() == $episode->post_parent && \gmrs_is_episode_onair( $episode ) ) {
		$args['date_query'] = array(
			array(
				'before' => $episode->post_date_gmt,
				'column' => 'post_date_gmt',
			),
		);
	}

	$query = new \WP_Query( $args );

	return $query;
}

function get_show_main_query( $per_page = 10 ) {
	$show_term = \TDS\get_related_term( get_the_ID() );
	$current_page = get_query_var( 'paged' ) ?: 1;

	$post_types = array( 'post' );
	if ( class_exists( '\GreaterMediaGalleryCPT' ) ) {
		$post_types[] = \GreaterMediaGalleryCPT::GALLERY_POST_TYPE;
	}

	$show_args = array(
		'post_type'      => $post_types,
		'paged'          => $current_page,
		'posts_per_page' => $per_page,
		'tax_query'      => array(
			'relation' => 'AND',
			array(
				'taxonomy' => \ShowsCPT::SHOW_TAXONOMY,
				'field' => 'term_taxonomy_id',
				'terms' => $show_term->term_taxonomy_id,
			)
		),
	);

	add_filter( 'posts_where', '\GreaterMedia\Shows\adjust_show_main_query' );
	$show_query = new \WP_Query( $show_args );

	return $show_query;
}

function adjust_show_main_query( $where ) {
	global $wpdb;

	remove_filter( 'posts_where', '\GreaterMedia\Shows\adjust_show_main_query' );

	if ( class_exists( '\GMP_CPT' ) ) {
		$podcasts = get_show_podcast_ids();
		if ( ! empty( $podcasts ) ) {
			$where = sprintf(
				" AND ((1 = 1%1\$s) OR (%2\$s.post_type = '%3\$s' AND %2\$s.post_parent IN (%4\$s) AND (%2\$s.post_status = 'publish')))",
				$where,
				$wpdb->posts,
				\GMP_CPT::EPISODE_POST_TYPE,
				implode( ',', $podcasts )
			);
		}
	}

	return $where;
}

function get_show_days( $object_id ) {

	$days = get_post_meta( $object_id, 'show_days', true );

	if ( $days ) {
		return $days;
	}

}

function get_show_times( $object_id ) {
	$times = get_post_meta( $object_id, 'show_times', true );

	if ( $times ) {
		return $times;
	}

}
