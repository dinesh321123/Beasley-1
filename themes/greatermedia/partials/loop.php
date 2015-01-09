<?php

while ( have_posts() ) : 
	the_post();
	get_template_part( 'partials/entry', get_post_field( 'post_type', null ) );
endwhile;