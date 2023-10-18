<?php

get_header();

the_post();

echo '<div class="', join( ' ', get_post_class() ), '">';
	echo '<div id="show-header-container" class="article-inner-container show-header-container">';
		echo '<div class="content-wrap">';
			echo '<div class="content-wrap">';
				if ( ee_is_first_page() ) :			
					get_template_part( 'partials/show/custom-header' );
					get_template_part( 'partials/show/featured' );
					get_template_part( 'partials/show/favorites' );
				endif;

				get_template_part( 'partials/show/recent' );
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

get_footer();
