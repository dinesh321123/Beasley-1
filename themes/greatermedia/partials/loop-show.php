<?php

global $gmr_loadmore_num_pages, $gmr_loadmore_post_count, $gmr_loadmore_paged;

$main_query = \GreaterMedia\Shows\get_show_main_query();
if ( $main_query->have_posts() ) :
	while( $main_query->have_posts() ):
		$main_query->the_post();
		get_template_part('partials/entry');
	endwhile;

	wp_reset_query();

	$gmr_loadmore_paged = get_query_var( 'paged', 1 );
	if ( $gmr_loadmore_paged < 2 ) :
		greatermedia_load_more_button( array(
			'query'              => $main_query,
			'partial_slug'       => 'partials/loop',
			'partial_name'       => 'show',
		) );
	endif;

	$gmr_loadmore_num_pages = $main_query->max_num_pages;
	$gmr_loadmore_post_count = $main_query->post_count;
endif;