<?php

$query = \GreaterMedia\Shows\get_show_video_query();
if ( $query->have_posts() ) :
	?><div class="archive-tiles">
		<?php ee_the_query_tiles( $query ); ?>
	</div><?php

	ee_load_more( $query );
else :
	ee_the_have_no_posts();
endif;
