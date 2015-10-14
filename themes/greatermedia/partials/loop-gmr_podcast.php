<?php

global $wp_query;

$podcast_query = \GreaterMedia\Shows\get_show_podcast_query();
while( $podcast_query->have_posts() ) :
	$podcast_query->the_post();
	get_template_part( 'partials/entry', get_post_type() );
endwhile;

$wp_query = $podcast_query;
