<?php
/**
 * Partial to display live player states statically.
 */

$liveplayer_disabled = get_option( 'gmr_liveplayer_disabled' );
if ( $liveplayer_disabled ) {
	return;
}

$streams = apply_filters( 'gmr_live_player_streams', array() );

$active_stream = key( $streams );
$active_station_id = $active_callsign = $active_description = '';

if ( empty( $active_stream ) ) {
	$active_stream = 'None';
} else {
	$active_callsign = $active_stream;
	$active_station_id = $streams[ $active_stream ]['station_id'];
	$active_description = $streams[ $active_stream ]['description'];
}

$livelinks_template = home_url( '/stream/%s/' );

?><div id="live-player" class="audio-interface">

	<?php // @TODO Available classes to add to audio-ad: -show ?>
	<div id="js-audio-ad-aboveplayer" class="audio-ad audio-ad--aboveplayer">
	</div>

	<div class="audio-interface__container">
		<?php // @TODO Available classes to add to audio-stream: -multiple, -open ?>
		<nav class="audio-stream<?php echo count( $streams ) >= 2 ? ' -multiple' : ''; ?>">
			<ul class="audio-stream__list">
				<li class="audio-stream__current">
					<?php // @TODO On desktop, the .audio-stream__title button below would control the -open class for .audio-stream ?>
					<button class="audio-stream__title" data-callsign="<?php echo esc_attr( $active_callsign ); ?>" data-station-id="<?php echo esc_attr( $active_station_id ); ?>">
						<?php echo esc_attr( ! empty( $active_description ) ? $active_description : $active_stream ); ?>
					</button>
					<ul class="audio-stream__available">
						<?php foreach ( $streams as $stream => $meta ) : ?>
							<li class="audio-stream__item">
								<button class="audio-stream__link" data-callsign="<?php echo esc_attr( $stream ); ?>" data-station-id="<?php echo esc_attr( $meta['station_id'] ); ?>">
									<span class="audio-stream__name"><?php echo esc_html( $stream ); ?></span>
									<span class="audio-stream__desc"><?php echo esc_html( $meta['description'] ); ?></span>
								</button>
							</li>
						<?php endforeach; ?>
					</ul>
				</li>
			</ul><!-- .audio-stream__list -->

			<div class="audio-sponsor -open">
				<?php do_action( 'dfp_sponsorship_tag' ); ?>
			</div>
		</nav><!-- .audio-stream -->

		<div id="js-audio-player" class="audio-player">

			<?php // @TODO Available classes to add to audio-volume: -open ?>
			<div id="js-audio-volume" class="audio-volume">
				<button id="audio-volume-mute" class="audio-volume__mute"><span class="audio-volume__text">Mute Volume</span></button>
				<input type="range" min="0" max="1" step="0.01" value="1" title="Volume Slider">
				<button id="js-audio-volume-button" class="audio-volume__btn"><span class="audio-volume__text">Volume</span></button>
			</div><!-- .audio-volume -->

			<?php // @TODO Available classes to add to audio-controls: -playing, -loading ?>
			<div id="js-audio-controls" class="audio-controls">
				<button id="playButton" class="audio-controls__play" data-action="play-live">
					<span class="audio-controls__text">Play</span>
				</button>
				<div id="loadButton" class="audio-controls__loading"><i class="gmr-icon icon-spin icon-loading">
					<span class="audio-controls__text">Loading</span></i>
				</div>
				<button id="pauseButton" class="audio-controls__pause">
					<span class="audio-controls__text">Pause</span>
				</button>
				<button id="resumeButton" class="audio-controls__resume">
					<span class="audio-controls__text">Resume</span>
				</button>
			</div><!-- .audio-controls -->

			<div id="js-audio-readout" class="audio-readout">

				<?php // @TODO Available classes to add to audio-ad: -show ?>
				<div id="js-audio-ad-inplayer" class="audio-ad audio-ad--inplayer">
				</div>

				<?php // @TODO Available classes to add to audio-readout__notification: -show ?>
				<div id="live-stream__listen-now" class="audio-readout__notification audio-readout__notification--listen -show">Listen Live</div>
				<!--<div id="js-notification-preroll" class="audio-readout__notification audio-readout__notification--preroll">Live stream will be available after this brief ad from our sponsors</div>-->

				<?php // @TODO Available classes to add to audio-playing: -show ?>
				<div id="live-stream__now-playing" class="audio-playing">
					<div id="js-track-info" class="audio-playing__track"></div>
					<div id="js-artist-info" class="audio-playing__artist"></div>

					<?php // @TODO Available classes to add to audio-podcast: -show ?>
					<div id="js-audio-podcast" class="audio-podcast">
						<span class="audio-podcast__text">00:00</span>
						<input type="range" name="audio-podcast" id="audio-podcast-slider" min="0" max="1" step="0.001" value="0">
						<span class="audio-podcast__text">00:00</span>
					</div><!-- .audio-podcast -->
				</div><!-- .audio-playing -->

				<?php // @TODO Available classes to add to audio-status: -show ?>
				<div id="js-audio-status" class="audio-status">
					<button id="js-audio-status-listen" class="audio-status__btn">Listen Live</button>
				</div>

				<div id="js-audio-more" class="audio-more">
					<a href="<?php echo esc_url( sprintf( $livelinks_template, $active_stream ) ); ?>" data-tmpl="<?php echo esc_attr( $livelinks_template ); ?>" title="<?php esc_attr_e( 'Play History', 'greatermedia' ) ?>"><span class="icon-clock"></span></a>
				</div>

				<div id="js-audio-time" class="audio-time">
					<div id="js-audio-time__progressbar" class="audio-time__progressbar">
						<span id="js-audio-time__progress" class="audio-time__progress"></span>
					</div>
					<div id="js-audio-time__elapsed" class="audio-time__elapsed"></div>
					<div id="js-audio-time__remaining" class="audio-time__remaining"></div>
				</div><!-- .audio-time -->

			</div><!-- .audio-readout -->

			<?php // @TODO On desktop, the .audio-expand button below would control the -open class for .audio-stream. Available classes: -open ?>
			<button id="js-audio-expand" class="audio-expand">
				<span class="audio-expand__text"><?php esc_html_e( 'View Audio Streams', 'greatermedia' ); ?></span>
			</button>

		</div><!-- .audio-player -->

	</div>
</div><!-- .audio-interface -->
