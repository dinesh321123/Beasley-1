<?php

$show = ee_get_current_show();
if ( ! $show || ee_is_whiz()) :
	return;
endif;

?>
<div class="show-header">
	<?php get_template_part( 'partials/show/information' ); ?>
	<?php get_template_part( 'partials/show/navigation' ); ?>
</div>
