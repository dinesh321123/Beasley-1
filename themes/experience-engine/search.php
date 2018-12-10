<?php

get_header();

if ( ee_is_first_page() ) :
	get_template_part( 'partials/search/header' );
endif;

if ( have_posts() ) :
	echo '<div class="content-wrap">';
		echo '<div class="archive-tiles -grid -small">';
			while ( have_posts() ) :
				the_post();
				get_template_part( 'partials/tile', get_post_type() );
			endwhile;
			echo '</div>';
		ee_load_more();
	echo '</div>';

else :
	ee_the_have_no_posts();
endif;

get_footer();
