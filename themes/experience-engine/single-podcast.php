<?php

get_header();

the_post();
echo '<div class="', join( ' ', get_post_class() ), '">';
	if ( ee_is_first_page() ) :
		get_template_part( 'partials/show/header' );
		get_template_part( 'partials/podcast/header' );
		get_template_part( 'partials/podcast/meta' );
	endif;

	get_template_part( 'partials/podcast/episodes' );
echo '</div>';

get_footer();
