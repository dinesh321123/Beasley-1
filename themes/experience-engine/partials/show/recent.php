<?php

$query = ee_get_show_query();
if ( $query->have_posts() ) :
	if ( ee_is_first_page() ) :
		ee_the_subtitle( 'Recent' );
	endif;

	?><div class="archive-tiles">
		<?php ee_the_query_tiles( $query ); ?>
	</div><?php

	ee_load_more( $query );
endif;
