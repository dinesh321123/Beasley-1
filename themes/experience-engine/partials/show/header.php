<?php

$show = ee_get_current_show();
if ( ! $show ) :
	return;
endif;

?><header class="show-header">
	<?php get_template_part( 'partials/show/information' ); ?>
	<?php get_template_part( 'partials/show/navigation' ); ?>
</header>
