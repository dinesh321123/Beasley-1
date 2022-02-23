<?php

global $gmr_loadmore_num_pages, $gmr_loadmore_post_count, $gmr_loadmore_paged;

$main_query = \GreaterMedia\Shows\get_show_main_query();
if ( $main_query->have_posts() ) :
	$posts_per_page     = 10;
	$posts_between_ads  = 6;

	$current_page       = intval( $main_query->query_vars['paged'] );
	$current_page       = max( 1, $current_page );
	$current_post_index = ( ( $current_page - 1 ) * $posts_per_page ) + 1;

	while( $main_query->have_posts() ):
		$main_query->the_post();
		get_template_part( 'partials/entry', get_post_type() );

		if ( $current_post_index % $posts_between_ads === 0 ) {
			get_template_part( 'partials/ad-in-loop' );
		}

		$current_post_index++;
	endwhile;

	wp_reset_query();

	$gmr_loadmore_paged = get_query_var( 'paged', 1 );
	if ( $gmr_loadmore_paged < 2 ) :
		greatermedia_load_more_button( array(
			'query'        => $main_query,
			'partial_slug' => 'partials/loop',
			'partial_name' => 'show',
		) );
	endif;

	$gmr_loadmore_num_pages = $main_query->max_num_pages;
	$gmr_loadmore_post_count = $main_query->post_count;
endif;