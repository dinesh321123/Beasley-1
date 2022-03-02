<?php
get_header( 'player' );

/**
 * The live player sidebar
 *
 * @package Greater Media
 * @since   0.1.0
 */

$streams       = apply_filters( 'gmr_live_player_streams', array() );
$active_stream = key( $streams );
if ( empty( $active_stream ) ) {
	$active_stream = 'None';
}

?>
	<div id="popup-player-livestream">
		<aside id="live-player__sidebar" class="live-player">

			<nav class="live-player__stream">
				<ul class="live-player__stream--list">
					<li class="live-player__stream--current">
						<div class="live-player__stream--title">Stream</div>
						<div class="live-player__stream--current-name"><?php echo esc_html( $active_stream ); ?></div>
						<ul class="live-player__stream--available">
							<?php foreach ( $streams as $stream => $meta ) : ?>
								<li class="live-player__stream--item">
									<div class="live-player__stream--name"><?php echo esc_html( $stream ); ?></div>
									<div class="live-player__stream--desc"><?php echo esc_html( $meta['description'] ); ?></div>
								</li>
							<?php endforeach; ?>
						</ul>
					</li>
				</ul>
			</nav>

			<div id="live-player" class="live-player__container">
				<div id="up-next" class="up-next">
					<span class="up-next__title">Up Next:</span><span class="up-next__show">Pierre Robert</span>
				</div>
				<div class="live-stream">
					<?php do_action( 'gm_live_player' ); ?>
					<div class="live-stream__status">
						<a href="<?php echo esc_url( home_url( '/members/login' ) ); ?>"
						   id="live-stream__listen-now"
						   class="live-stream__listen-now--btn"><?php _e( 'Listen Live', 'greatermedia' ); ?></a>

						<div id="live-stream__now-playing" class="live-stream__now-playing--btn">Now Playing</div>
					</div>
					<div id="nowPlaying" class="now-playing">
						<div id="trackInfo" class="now-playing__info"></div>
						<div id="npeInfo"></div>
					</div>
				</div>
			</div>
		</aside>
	</div>
<?php
get_footer( 'player' );