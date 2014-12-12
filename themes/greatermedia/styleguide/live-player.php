<?php
/**
 * Partial for the Live Player section on the Style Guide
 *
 * @package Greater Media
 * @since 0.1.0
 */
?>
<section id="live-player" class="sg-layout sg-sections">
	<div class="sg-content">
		<h2 class="sg-section-title"><?php _e( 'Live Player', 'greatermedia' ); ?></h2>

		<div class="live-player__col">

			<h3 class="sg-subsection--title"><?php _e( 'Logged in : Live Radio Not Playing', 'greatermedia' ); ?></h2>
			<div id="live-player__sidebar" class="live-player">

				<nav class="live-player__stream">
					<ul class="live-player__stream--list">
						<li class="live-player__stream--current">
							<div class="live-player__stream--title">Stream</div>
							<div class="live-player__stream--current-name">HD1</div>
							<ul class="live-player__stream--available">
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">HD1</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">HD2</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">HD3</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">FM</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">FM2</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
							</ul>
						</li>
					</ul>
				</nav>

				<div id="live-player" class="live-player__container">

					<div id="on-air" class="on-air">
						<span class="on-air__title">On Air:</span><span class="on-air__show">Preston and Steve Show</span>
					</div>

					<div class="live-stream">
						<div class="live-stream__player">
							<div class="live-stream__controls">
								<div id="playButton" class="live-stream__btn--play" data-station="WKLBFM"></div>
							</div>

							<!-- Player placeholder -->
							<div id="td_container"></div>
						</div>

						<div class="live-stream__status">
							<div id="live-stream__listen-now" class="live-stream__listen-now--btn">Listen Live</div>
						</div>
						<div id="now-playing" class="now-playing">
							<div class="now-playing__title">Track Title</div>
							<div class="now-playing__artist">Artist Name</div>
						</div>
					</div>

				</div>

				<div id="live-links" class="live-links">
					<h3 class="widget--live-player__title"><?php _e( 'Live Links', 'greatermedia' ); ?></h3>
					<div class="widget--live-player">
						<ul>
							<li class="live-link__type--audio">
								<div class="live-link__title"><a href="#">WMMR Promo - 10/16/14 - A surprise in the movie "Fury"</a></div>
							</li>
							<li class="live-link__type--video">
								<div class="live-link__title"><a href="#">"Breakdance Conversation" with Jimmy Fallon & Brad Pitt</a></div>
							</li>
							<li class="live-link__type--link">
								<div class="live-link__title"><a href="#">Flyers Charities Halloween 5K will be held on Saturday, October 25</a></div>
							</li>
						</ul>
					</div>
				</div>

			</div>
		</div>

		<div class="live-player__col">

			<h3 class="sg-subsection--title"><?php _e( 'Live Radio Playing', 'greatermedia' ); ?></h3>
			<div id="live-player--sidebar" class="live-player">

				<nav class="live-player__stream">
					<ul class="live-player__stream--list">
						<li class="live-player__stream--current">
							<div class="live-player__stream--title">Stream</div>
							<div class="live-player__stream--current-name">HD1</div>
							<ul class="live-player__stream--available">
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">HD1</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">HD2</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">HD3</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">FM</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">FM2</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
							</ul>
						</li>
					</ul>
				</nav>

				<div id="live-player" class="live-player__container">

					<div id="up-next" class="up-next">
						<span class="up-next__title">Up Next:</span><span class="up-next__show">Pierre Robert</span>
					</div>

					<div class="live-stream">
						<div class="live-stream__player">
							<div class="live-stream__controls">
								<div id="pauseButton" class="live-stream__btn--pause" data-station="WKLBFM"></div>
							</div>

							<!-- Player placeholder -->
							<div id="td_container"></div>
						</div>

						<div class="live-stream__status">
							<div id="live-stream__now-playing" class="live-stream__now-playing--btn">Now Playing</div>
						</div>
						<div id="now-playing" class="now-playing">
							<div class="now-playing__title">Track Title</div>
							<div class="now-playing__artist">Artist Name</div>
						</div>
					</div>

				</div>

				<div id="live-links" class="live-links">
					<h3 class="widget--live-player__title"><?php _e( 'Live Links', 'greatermedia' ); ?></h3>
					<div class="widget--live-player">
						<ul>
							<li class="live-link__type--audio">
								<div class="live-link__title"><a href="#">WMMR Promo - 10/16/14 - A surprise in the movie "Fury"</a></div>
							</li>
							<li class="live-link__type--video">
								<div class="live-link__title"><a href="#">"Breakdance Conversation" with Jimmy Fallon & Brad Pitt</a></div>
							</li>
							<li class="live-link__type--link">
								<div class="live-link__title"><a href="#">Flyers Charities Halloween 5K will be held on Saturday, October 25</a></div>
							</li>
						</ul>
					</div>
				</div>

			</div>

		</div>

		<div class="live-player__col">

			<h3 class="sg-subsection--title"><?php _e( 'Podcast Playing', 'greatermedia' ); ?></h3>
			<div id="live-player--sidebar" class="live-player">

				<nav class="live-player__stream">
					<ul class="live-player__stream--list">
						<li class="live-player__stream--current">
							<div class="live-player__stream--title">Stream</div>
							<div class="live-player__stream--current-name">HD1</div>
							<ul class="live-player__stream--available">
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">HD1</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">HD2</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">HD3</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">FM</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">FM2</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
							</ul>
						</li>
					</ul>
				</nav>

				<div id="live-player" class="live-player__container">

					<div class="live-stream__resume">
						<div id="live-stream__resume-live" class="live-stream__resume--btn">Listen Live</div>
					</div>

					<div class="live-stream">
						<div class="live-stream__player">
							<div class="live-stream__controls">
								<div id="resumeButton" class="live-stream__btn--resume" data-station="WKLBFM"></div>
							</div>

							<!-- Player placeholder -->
							<div id="td_container"></div>
						</div>

						<div class="live-stream__status">
							<div class="live-stream--podcast__progress">
								<div class="live-stream--podcast__progress-current"></div>
							</div>
							<div class="live-stream--podcast__time">
								<div class="live-stream--podcast__start">
									0:00
								</div>
								<div class="live-stream--podcast__end">
									3:09
								</div>
							</div>
						</div>
						<div id="now-playing" class="now-playing">
							<div class="now-playing__title">Jaxon’s Local Shots</div>
							<div class="now-playing__artist">PODCAST - 8.23.14</div>
						</div>
					</div>

				</div>

				<div id="live-links" class="live-links">
					<h3 class="widget--live-player__title"><?php _e( 'Live Links', 'greatermedia' ); ?></h3>
					<div class="widget--live-player">
						<ul>
							<li class="live-link__type--audio">
								<div class="live-link__title"><a href="#">WMMR Promo - 10/16/14 - A surprise in the movie "Fury"</a></div>
							</li>
							<li class="live-link__type--video">
								<div class="live-link__title"><a href="#">"Breakdance Conversation" with Jimmy Fallon & Brad Pitt</a></div>
							</li>
							<li class="live-link__type--link">
								<div class="live-link__title"><a href="#">Flyers Charities Halloween 5K will be held on Saturday, October 25</a></div>
							</li>
						</ul>
					</div>
				</div>

			</div>
		</div>

		<div class="live-player__col">

			<h3 class="sg-subsection--title"><?php _e( 'Inline Audio Playing', 'greatermedia' ); ?></h3>
			<div id="live-player--sidebar" class="live-player">

				<nav class="live-player__stream">
					<ul class="live-player__stream--list">
						<li class="live-player__stream--current">
							<div class="live-player__stream--title">Stream</div>
							<div class="live-player__stream--current-name">HD1</div>
							<ul class="live-player__stream--available">
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">HD1</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">HD2</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">HD3</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">FM</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">FM2</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
							</ul>
						</li>
					</ul>
				</nav>

				<div id="live-player" class="live-player__container">

					<div class="live-stream__resume">
						<div id="live-stream__resume-live" class="live-stream__resume--btn">Listen Live</div>
					</div>

					<div class="live-stream">
						<div class="live-stream__player">
							<div class="live-stream__controls">
								<div id="resumeButton" class="live-stream__btn--resume" data-station="WKLBFM"></div>
							</div>

							<!-- Player placeholder -->
							<div id="td_container"></div>
						</div>

						<div class="live-stream__status">
							<div class="live-stream--podcast__progress">
								<div class="live-stream--podcast__progress-current"></div>
							</div>
							<div class="live-stream--podcast__time">
								<div class="live-stream--podcast__start">
									0:00
								</div>
								<div class="live-stream--podcast__end">
									3:09
								</div>
							</div>
						</div>
						<div id="now-playing" class="now-playing">
							<div class="now-playing__title">Interview with Ozzy</div>
							<div class="now-playing__artist"></div>
						</div>
					</div>

				</div>

				<div id="live-links" class="live-links">
					<h3 class="widget--live-player__title"><?php _e( 'Live Links', 'greatermedia' ); ?></h3>
					<div class="widget--live-player">
						<ul>
							<li class="live-link__type--audio">
								<div class="live-link__title"><a href="#">WMMR Promo - 10/16/14 - A surprise in the movie "Fury"</a></div>
							</li>
							<li class="live-link__type--video">
								<div class="live-link__title"><a href="#">"Breakdance Conversation" with Jimmy Fallon & Brad Pitt</a></div>
							</li>
							<li class="live-link__type--link">
								<div class="live-link__title"><a href="#">Flyers Charities Halloween 5K will be held on Saturday, October 25</a></div>
							</li>
						</ul>
					</div>
				</div>

			</div>

		</div>

		<div class="live-player__col">

			<h3 class="sg-subsection--title"><?php _e( 'Channel Selector', 'greatermedia' ); ?></h3>
			<div id="live-player__sidebar" class="live-player">

				<nav class="live-player__stream">
					<ul class="live-player__stream--list">
						<li class="live-player__stream--current open">
							<div class="live-player__stream--title">Stream</div>
							<div class="live-player__stream--current-name">HD1</div>
							<ul class="live-player__stream--available">
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">HD1</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">HD2</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">HD3</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">FM</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
								<li class="live-player__stream--item">
									<div class="live-player__stream--name">FM2</div>
									<div class="live-player__stream--desc">A brief description can be used here</div>
								</li>
							</ul>
						</li>
					</ul>
				</nav>

				<div id="live-player" class="live-player__container">

					<div id="on-air" class="on-air">
						<span class="on-air__title">On Air:</span><span class="on-air__show">Preston and Steve Show</span>
					</div>

					<div class="live-stream">
						<div class="live-stream__player">
							<div class="live-stream__controls">
								<div id="playButton" class="live-stream__btn--play" data-station="WKLBFM"></div>
							</div>

							<!-- Player placeholder -->
							<div id="td_container"></div>
						</div>

						<div class="live-stream__status">
							<div id="live-stream__listen-now" class="live-stream__listen-now--btn">Listen Live</div>
						</div>
						<div id="now-playing" class="now-playing">
							<div class="now-playing__title">Track Title</div>
							<div class="now-playing__artist">Artist Name</div>
						</div>
					</div>

				</div>

				<div id="live-links" class="live-links">
					<h3 class="widget--live-player__title"><?php _e( 'Live Links', 'greatermedia' ); ?></h3>
					<div class="widget--live-player">
						<ul>
							<li class="live-link__type--audio">
								<div class="live-link__title"><a href="#">WMMR Promo - 10/16/14 - A surprise in the movie "Fury"</a></div>
							</li>
							<li class="live-link__type--video">
								<div class="live-link__title"><a href="#">"Breakdance Conversation" with Jimmy Fallon & Brad Pitt</a></div>
							</li>
							<li class="live-link__type--link">
								<div class="live-link__title"><a href="#">Flyers Charities Halloween 5K will be held on Saturday, October 25</a></div>
							</li>
						</ul>
					</div>
				</div>

			</div>
		</div>

	</div>
</section>