<?php

get_header();

the_post();

echo '<div class="', join( ' ', get_post_class() ), '">';
	if ( ee_is_first_page() ) :
		get_template_part( 'partials/show/header' );
		get_template_part( 'partials/show/featured' );
		get_template_part( 'partials/show/favorites' );
	endif;

	get_template_part( 'partials/show/recent' );
echo '</div>';

get_footer();
