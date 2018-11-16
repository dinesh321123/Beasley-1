<?php

$query = ee_get_show_favorites();
if ( $query->have_posts() ) :
	ee_the_subtitle( 'Our Favorites' );

	?><div class="archive-tiles">
		<?php ee_the_query_tiles( $query ); ?>
	</div><?php
endif;
