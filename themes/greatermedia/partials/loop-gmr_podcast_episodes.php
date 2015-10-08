<?php

global $gmr_loadmore_num_pages, $gmr_loadmore_post_count, $gmr_loadmore_paged;

$gmr_loadmore_paged = get_query_var( 'paged', 1 );
$episodes_query = new WP_Query( array(
	'post_type'      => GMP_CPT::EPISODE_POST_TYPE,
	'post_parent'    => get_the_ID(),
	'paged'          => $gmr_loadmore_paged,
	'posts_per_page' => get_option( 'posts_per_page', 10 ),
) );

if ( $episodes_query->have_posts() ) :

	while( $episodes_query->have_posts() ) :
		$episodes_query->the_post();
		get_template_part( 'partials/entry', get_post_type() );
	endwhile;

	wp_reset_query();

	if ( $gmr_loadmore_paged < 2 ) :
		greatermedia_load_more_button( array(
			'page_link_template' => str_replace( PHP_INT_MAX, '%d', get_pagenum_link( PHP_INT_MAX ) ),
			'partial_slug'       => 'partials/loop',
			'partial_name'       => 'gmr_podcast_episodes',
			'auto_load'          => false,
			'query'              => $episodes_query,
		) );
	endif;

	$gmr_loadmore_num_pages = $episodes_query->max_num_pages;
	$gmr_loadmore_post_count = $episodes_query->post_count;

endif;