<?php

if ( ! ee_is_first_page() ) :
	return;
endif;

?><div class="ad -leaderboard -centered">
	<?php do_action( 'dfp_tag', 'top-leaderboard', false, array( array( 'pos', 1 ) ) ); ?>
</div>
