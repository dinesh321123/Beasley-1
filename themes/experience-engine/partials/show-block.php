<?php

$show = ee_get_current_show();
if ( ! $show ) :
	return;
endif;

?><div>
	<?php get_template_part( 'partials/show-information' ); ?>
	<?php get_template_part( 'partials/show-navigation' ); ?>
</div>
