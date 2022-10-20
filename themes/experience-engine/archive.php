<?php

if ( ee_is_first_page() ):
	get_header();
	get_template_part( 'partials/archive/title' );
	get_template_part( 'partials/archive/meta' );
else :
	echo '<div id="inner-content">';
endif;

if ( have_posts() ) :
	echo '<div class="archive-tiles content-wrap ', ! is_post_type_archive( 'contest' ) && ! is_post_type_archive( 'tribe_events' ) ? '-grid -large' : '-list', '">';
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
if ( ee_is_first_page() ):
	get_footer();
else:
	echo '</div>';
endif;
