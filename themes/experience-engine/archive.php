<?php

get_header();

if ( ee_is_first_page() ):
	get_template_part( 'partials/archive/title' );
endif;

if ( have_posts() ) :
	echo '<div class="archive-tiles', ! is_post_type_archive( 'contest' ) ? ' -grid' : '', ' -large content-wrap">';
		while ( have_posts() ) :
			the_post();
			get_template_part( 'partials/tile', get_post_type() );
		endwhile;
	echo '</div>';

	echo '<div class="content-wrap">';
		ee_load_more();
	echo '</div>';
else :
	echo '<div class="content-wrap">';
		ee_the_have_no_posts();
	echo '</div>';
endif;

get_footer();
