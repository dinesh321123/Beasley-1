<?php

$query = ee_get_show_featured();
if ( $query->have_posts() ) :
	ee_the_subtitle( 'Featured' );

	?><div class="archive-tiles -large -carousel">
		<?php ee_the_query_tiles( $query ); ?>
	</div><?php
endif;
