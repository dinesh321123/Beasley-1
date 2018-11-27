<?php

get_header();

the_post();

echo '<div class="', join( ' ', get_post_class() ), '">';
	echo '<div class="content-wrap">';
		if ( ee_is_first_page() ) :
			get_template_part( 'partials/show/header' );
			get_template_part( 'partials/show/featured' );
			get_template_part( 'partials/show/favorites' );
			do_action( 'dfp_tag', 'dfp_ad_inlist_infinite' );
		endif;

		get_template_part( 'partials/show/recent' );
	echo '</div>';
echo '</div>';

get_footer();
