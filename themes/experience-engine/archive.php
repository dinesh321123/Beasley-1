<?php

get_header();

if ( ee_is_first_page() ):
	get_template_part( 'partials/archive/title' );
endif;

if ( have_posts() ) :
	if ('contest' === get_post_type()):
		echo '<div class="archive-tiles -list content-wrap">';
			while ( have_posts() ) :
				the_post();
				get_template_part( 'partials/tile', get_post_type() );
			endwhile;
		echo '</div>';
	else:
		echo '<div class="archive-tiles -grid -large content-wrap">';
			while ( have_posts() ) :
				the_post();
				get_template_part( 'partials/tile', get_post_type() );
			endwhile;
		echo '</div>';
	endif;

	echo '<div class="content-wrap">';
		ee_load_more();
	echo '</div>';
else :
	echo '<div class="content-wrap">';
		ee_the_have_no_posts();
	echo '</div>';
endif;

get_footer();
