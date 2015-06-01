<?php

global $gmr_loadmore_num_pages, $gmr_loadmore_post_count, $gmr_loadmore_paged;

$main_query = \GreaterMedia\Shows\get_show_video_query();
if ( $main_query->have_posts() ) :
	while( $main_query->have_posts() ):
		$main_query->the_post();
		get_template_part( 'partials/entry', get_post_field( 'post_type', null ) );
	endwhile;

	wp_reset_query();

	$gmr_loadmore_num_pages = $main_query->max_num_pages;
	$gmr_loadmore_post_count = $main_query->post_count;
endif;