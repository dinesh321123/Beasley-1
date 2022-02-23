<?php

global $wp_query;

$posts_per_page     = 10;
$posts_between_ads  = 6;

$current_page       = intval( $wp_query->query_vars['paged'] );
$current_page       = max( 1, $current_page );
$current_post_index = ( ( $current_page - 1 ) * $posts_per_page ) + 1;

while ( have_posts() ) :
	the_post();
	//get_template_part( 'partials/entry', get_post_field( 'post_type', null ) );

	get_template_part( 'partials/entry' );

	if ( $current_post_index % $posts_between_ads === 0 ) {
		get_template_part( 'partials/ad-in-loop' );
	}

	$current_post_index++;
endwhile;

