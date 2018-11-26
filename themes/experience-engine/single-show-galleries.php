<?php

get_header();

the_post();

echo '<div class="', join( ' ', get_post_class() ), '">';
	if ( ee_is_first_page() ) :
		get_template_part( 'partials/show/header' );
		ee_the_subtitle( 'Galleries' );
	endif;

	get_template_part( 'partials/show/galleries' );
echo '</div>';

get_footer();
