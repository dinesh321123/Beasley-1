<?php

$query = \GreaterMedia\Shows\get_show_favorites_query();
if ( $query->have_posts() ) :
	ee_the_subtitle( 'Our Favorites' );

	?><div class="archive-tiles -small -carousel">
		<?php ee_the_query_tiles( $query ); ?>
	</div><?php

	do_action( 'dfp_tag', 'dfp_ad_inlist_infinite' );
endif;
