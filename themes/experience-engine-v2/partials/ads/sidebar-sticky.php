<?php

if ( ee_is_whiz() ) :
	return;
endif;

?><aside class="ad -sticky">
	<div class="wrapper">
		<?php do_action( 'dfp_tag', 'right-rail' ); ?>
		<?php do_action( 'dimers_widget'); ?>
	</div>
</aside>
