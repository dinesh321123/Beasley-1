<?php
/**
 * The live player sidebar
 *
 * @package Greater Media
 * @since   0.1.0
 */
?>
<aside class="live-player">

	<div class="now-playing--logo">

	</div>

	<div id="live-player" class="live-player--container">
		<div id="live-player--listen_now" class="live-player--listen_btn" style="visibility:visible">Listen Now</div>

		<?php

			do_action( 'gm_live_player_test_ui' );

			do_action( 'gm_live_player' );

		?>
	</div>

	<div class="now-playing--title">

	</div>

	<div class="now-playing--artist">

	</div>

	<div class="live-player--social">

		<ul>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
		</ul>

	</div>

	<div class="live-player--next">
		Up Next: <span class="live-player--next--artist">Pierre Robert</span>
	</div>

</aside>