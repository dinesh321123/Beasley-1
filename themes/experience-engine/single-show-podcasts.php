<?php

get_header();

the_post();

echo '<div class="', join( ' ', get_post_class() ), '">';
	if ( ee_is_first_page() ) :
		get_template_part( 'partials/show/header' );
	endif;

	echo '<div class="content-wrap">';
		ee_the_subtitle( 'Podcasts' );
		echo '<div class="archive-tiles -grid -small">';
			get_template_part( 'partials/show/episodes' );
		echo '</div>';
	echo '</div>';
echo '</div>';

get_footer();
