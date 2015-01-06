<?php

$post_count = 0;
while ( have_posts() ) : the_post();
	if ( 0 == ++$post_count % 5 ) :
		?><div class='entry2-ad-wrap'><?php
	endif;

	get_template_part( 'partials/entry' );
	
	if ( 0 == $post_count % 5 ):
			?><div class='entry2-ad-wrap__ad mobile'>
				<img src='http://placehold.it/180x150'>
			</div>
			<div class='entry2-ad-wrap__ad desktop'>
				<img src='http://placehold.it/300x250'>
			</div>
		</div><?php
	endif;
endwhile;