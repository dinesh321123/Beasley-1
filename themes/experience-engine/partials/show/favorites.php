<?php

$query = \GreaterMedia\Shows\get_show_favorites_query();
if ( $query->have_posts() ) :
	ee_the_subtitle( 'Our Favorites' );

	?><div class="archive-tiles -small -carousel swiper-container">
		<div class="swiper-wrapper">
			<?php ee_the_query_tiles( $query, true ); ?>
		</div>
		<div class="swiper-button-prev"></div><div class="swiper-button-next"></div>
	</div><?php

	do_action( 'dfp_tag', 'in-list' );
endif;
