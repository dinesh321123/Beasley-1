<?php

$query = \GreaterMedia\Shows\get_show_featured_query();
if ( $query->have_posts() ) :
	ee_the_subtitle( 'Featured' );

	?><div class="archive-tiles -large -carousel swiper-container">
		<div class="swiper-wrapper">
			<?php ee_the_query_tiles( $query, true ); ?>
		</div>
		<div class="swiper-button-prev"></div><div class="swiper-button-next"></div>
	</div><?php
endif;
