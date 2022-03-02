<?php
/**
 * The live player sidebar
 *
 * @package Greater Media
 * @since   0.1.0
 */

$streams = apply_filters( 'gmr_live_player_streams', array() );
$active_stream = key( $streams );
if ( empty( $active_stream ) ) {
	$active_stream = 'None';
}

?><aside id="live-player__sidebar" class="live-player">

	<?php // start the live streaming section of the live player
	$liveplayer_disabled = get_option( 'gmr_liveplayer_disabled' );

	if ( $liveplayer_disabled != 1 ) {
	?>
	<div class="live-stream">
		<nav class="live-player__stream">
			<ul class="live-player__stream--list">
				<li class="live-player__stream--current">
					<div class="live-player__stream--title"><?php _e( 'Stream:', 'greatermedia' ); ?></div>
					<div class="live-player__stream--current-name"><?php echo esc_html_e( $active_stream ); ?></div>
					<ul class="live-player__stream--available">
						<?php foreach ( $streams as $stream => $description ) : ?>
						<li class="live-player__stream--item">
							<div class="live-player__stream--name"><?php echo esc_html_e( $stream ); ?></div>
							<div class="live-player__stream--desc"><?php echo esc_html_e( $description ); ?></div>
						</li>
						<?php endforeach; ?>
					</ul>
				</li>
			</ul>
		</nav>

		<?php do_action( 'gmr_live_audio_link' ); ?>

		<div id="live-player" class="live-player__container">

			<div id="on-air" class="on-air" data-endpoint="<?php echo esc_url( home_url( '/on-air/' ) ); ?>">
				<div class="on-air__title"></div>
				<div class="on-air__show"></div>
			</div>

			<div id="live-player--more" class="live-player--more"><?php _e( '...', 'greatermedia' ); ?></div>
			<?php do_action( 'gm_live_player' ); ?>
			<div id="audio__time" class="audio__time">
				<div id="audio__progress-bar" class="audio__progress-bar">
					<span id="audio__progress" class="audio__progress"></span>
				</div>
				<div id="audio__time--elapsed" class="audio__time--elapsed"></div>
				<div id="audio__time--remaining" class="audio__time--remaining"></div>
			</div>
			<div id="nowPlaying" class="now-playing">
				<div id="trackInfo" class="now-playing__info"></div>
				<div id="npeInfo"></div>
			</div>

			<div id="live-stream__status" class="live-stream__status">
				<div id="live-stream__now-playing" class="live-stream__now-playing--btn"><?php _e( 'Now Playing', 'greatermedia' ); ?></div>
				<div id="live-stream__listen-now" class="live-stream__listen-now--btn"><?php _e( 'Listen Live', 'greatermedia' ); ?></div>
			</div>

		</div>
	</div>
	<?php } // start the live streaming section of the live player ?>

	<?php // start the live links section of the live player ?>
	<div id="live-links" class="live-links">

		<h3 class="widget--live-player__title"><?php do_action( 'gmr_livelinks_title' ); ?></h3>

		<div class="live-player--open__btn"></div>

		<?php dynamic_sidebar( 'liveplayer_sidebar' ); ?>

		<div id="live-links__widget--end"></div>
	</div>

	<?php

	/**
	 * Changes the url that will be used by the live links more button based on whether a checkbox has been checked in
	 * the Station Site Administration Screen
	 */
	$livelinks_redirect = get_option( 'gmr_livelinks_more_redirect' );
	$livelinks_redirect = filter_var( $livelinks_redirect, FILTER_VALIDATE_BOOLEAN);
	$livelinks_url = null;

	if ( $livelinks_redirect === true ) {
		$livelinks_url = home_url( '/stream/' . $active_stream );
	} else {
		$livelinks_url = home_url( '/live-links' );
	}

	?>
	<div class="live-links--more">
		<a href="<?php echo esc_url( $livelinks_url ); ?>" class="live-links--more__btn">More</a>
	</div>

</aside>