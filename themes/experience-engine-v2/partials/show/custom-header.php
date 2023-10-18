<?php

$show = ee_get_current_show();
if ( ! $show || ee_is_whiz()) :
	return;
endif;

?>
<div id="top_header" class="top_header">
	<div class="cm-slimmer-menu"></div>
	<?php get_template_part( 'partials/show/custom-information' ); ?>
	<?php get_template_part( 'partials/show/custom-navigation' ); ?>
</div>
