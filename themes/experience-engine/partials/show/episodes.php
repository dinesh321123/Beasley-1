<?php

$query = \GreaterMedia\Shows\get_show_podcast_query();
if ( $query->have_posts() ) :
	?><div class="archive-tiles -grid -small">
		<?php ee_the_query_tiles( $query ); ?>
	</div><?php

	ee_load_more( $query );
else :
	ee_the_have_no_posts();
endif;
