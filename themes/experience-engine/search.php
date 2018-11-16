<?php

get_header();

if ( ee_is_first_page() ) :
	get_search_form();
endif;

echo '<div class="archive-tiles">';
	while ( have_posts() ) :
		the_post();
		get_template_part( 'partials/tile', get_post_type() );
	endwhile;
echo '</div>';

ee_load_more();

get_footer();
