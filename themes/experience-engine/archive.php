<?php

get_header();

if ( ee_is_first_page() ):
	get_template_part( 'partials/archive/title' );
endif;

if ( have_posts() ) :
	echo '<div class="archive-tiles -grid -large content-wrap">';
		while ( have_posts() ) :
			the_post();
			get_template_part( 'partials/tile', get_post_type() );
		endwhile;
	echo '</div>';

	ee_load_more();
else :
	echo '<div class="content-wrap">';
		ee_the_have_no_posts();
	echo '</div>';
endif;

get_footer();
