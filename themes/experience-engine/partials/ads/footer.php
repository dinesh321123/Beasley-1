<?php

if ( ! ee_is_first_page() ) :
	return;
endif;

?><div class="ad -footer -centered">
	<div class="wrapper">
		<p class="ad-title">Advertisement</p>
		<?php do_action( 'dfp_tag', 'dfp_ad_leaderboard_pos2', false, array( array( 'pos', 2 ) ) ); ?>
	</div>
</div>
