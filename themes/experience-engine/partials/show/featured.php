<?php

$query = \GreaterMedia\Shows\get_show_featured_query();
if ( $query->have_posts() ) :
	ee_the_subtitle( 'Featured' );

	?><div class="archive-tiles -large -carousel">
		<?php ee_the_query_tiles( $query ); ?>
	</div><?php
endif;
