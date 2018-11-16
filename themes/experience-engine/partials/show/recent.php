<?php

$query = ee_get_show_query();
if ( $query->have_posts() ) :
	ee_the_subtitle( 'Recent' );

	?><div class="archive-tiles">
		<?php ee_the_query_tiles( $query ); ?>
	</div><?php

	ee_load_more( $query );
endif;
