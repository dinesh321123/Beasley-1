<?php

if ( ! ee_is_first_page() ) :
	return;
endif;

?><div class="ad -footer -centered">
	<?php do_action( 'dfp_tag', 'bottom-leaderboard', false, array( array( 'pos', 2 ) ) ); ?>
</div>
