<?php
/**
 * Partial for the Layout section on the Style Guide
 *
 * @package Greater Media
 * @since 0.1.0
 */
 ?>
<section id="layout" class="sg-layout sg-sections">
	<div class="sg-content">
		<h2 class="sg-section-title"><?php _e( 'Layout', 'greatermedia' ); ?></h2>

		<!-- Featured Content for the Front Page -->
		<h3 class="sg-section-subtitle"><?php _e( 'Featured Content - Front Page', 'greatermedia' ); ?></h3>
		<section id="featured" class="featured">
			<div class="container">
				<div class="featured__artist">
					<div class="featured__artist--image">
						<img src="http://placehold.it/2800x1000&text=featured+image">
					</div>
					<div class="featured__artist--content">
						<div class="featured__artist--heading">
							<h2 class="featured__artist--title"><?php _e( 'Artist of the Month', 'greatermedia' ); ?></h2>
							<h3 class="featured__artist--subtitle">Minshara</h3>
						</div>
						<div class="featured__artist--bio">
							Min•sha•ra (min SHä rə) 1. adj. the classification for a planet capable of supporting humanoid life; 2. n. electronic pop rock band from Harrisburg / Philadelphia, Pennsylvania From The Viper Room in LA to Webster Hall in NYC, Minshara has been spreading infectious pop melodies, dance grooves, and rock
						</div>
					</div>
				</div>
			</div>
			<div class="container">
				<div class="featured__content">
					<div class="featured__content--block">
						<div class="featured__content--image">
							<img src="http://placehold.it/400x400&text=featured+image">
						</div>
						<div class="featured__content--meta">
							<h2 class="featured__content--title">MMR Rocks the Flyers</h2>
							<ul class="featured__content--list">
								<li class="featured__content--item">MMR Rocks the Flyers</li>
								<li class="featured__content--item">Flyers All Access</li>
							</ul>
						</div>
					</div>
					<div class="featured__content--block">
						<div class="featured__content--image">
							<img src="http://placehold.it/400x400&text=featured+image">
						</div>
						<div class="featured__content--meta">
							<h2 class="featured__content--title">Hitch a Ride with Pierre ...and Minerva</h2>
							<div class="featured__content--link">
								<a href="#" class="featured__content--btn">Enter To Win</a>
							</div>
						</div>
					</div>
					<div class="featured__content--block">
						<div class="featured__content--image">
							<img src="http://placehold.it/400x400&text=featured+image">
						</div>
						<div class="featured__content--meta">
							<h2 class="featured__content--title">Preston and Steve</h2>
							<ul class="featured__content--list">
								<li class="featured__content--item">Daily Rush</li>
								<li class="featured__content--item">Flyers All Access</li>
								<li class="featured__content--item">Studio Guests</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- Highlighted Content for the Front Page -->
		<h3 class="sg-section-subtitle"><?php _e( 'Featured Content - Front Page', 'greatermedia' ); ?></h3>
		<section class="highlights">

			<div class="container">

				<div class="highlights__community">

					<h2 class="highlights__heading"><?php bloginfo( 'name' ); ?><?php _e( ' Community Highlights', 'greatermedia' ); ?><span class="highlights__heading--span"><?php _e( 'YouRock', 'greatermedia' ); ?></span></h2>

					<div class="highlights__community--item">

						<div class="highlights__community--thumb">
							<img src="http://placehold.it/356x356&text=image">
						</div>

						<h3 class="highlights__community--title"><?php _e( 'title', 'greatermedia' ); ?></h3>

					</div>

					<div class="highlights__community--item">

						<div class="highlights__community--thumb">
							<img src="http://placehold.it/356x356&text=image">
						</div>

						<h3 class="highlights__community--title"><?php _e( 'title', 'greatermedia' ); ?></h3>

					</div>

					<div class="highlights__community--item">

						<div class="highlights__community--thumb">
							<img src="http://placehold.it/356x356&text=image">
						</div>

						<h3 class="highlights__community--title"><?php _e( 'title', 'greatermedia' ); ?></h3>

					</div>

				</div>

				<div class="highlights__events">

					<h2 class="highlights__heading"><?php _e( 'Upcoming', 'greatermedia' ); ?><span class="highlights__heading--span"><?php _e( 'Events', 'greatermedia' ); ?></span></h2>

					<div class="highlights__event--item">

						<div class="highlights__event--thumb">
							<img src="http://placehold.it/156x156&text=image">
						</div>

						<div class="highlights__event--meta">
							<h3 class="highlights__event--title">Preston & Steve’s Camp Out For Hunger 2014</h3>
							<time datetime="<?php the_time( 'c' ); ?>" class="highlights__event--date">Dec 1 - Dec 5</time>
						</div>

					</div>

					<div class="highlights__event--item">

						<div class="highlights__event--thumb">
							<img src="http://placehold.it/156x156&text=image">
						</div>

						<div class="highlights__event--meta">
							<h3 class="highlights__event--title">A.B.A.T.E. Motorcycle Toy Run For The Kids!</h3>
							<time datetime="<?php the_time( 'c' ); ?>" class="highlights__event--date">Nov 2</time>
						</div>

					</div>

				</div>

			</div>

		</section>

		<!-- Video Post -->
		<article id="post-id" class="post type-post status-publish hentrycf" role="article" itemscope="" itemtype="http://schema.org/BlogPosting">

			<section class="entry-video--thumbnail">

				<img src="http://placehold.it/600x350&text=video">

			</section>

			<section class="entry-video--content">

				<header class="entry-header">

					<h2 class="entry-title" itemprop="headline"><a href="#">Royal Blood News Round-Up: ‘Late Night’ Performance, MTV EMAs and More!</a></h2>

				</header>

			</section>

			<footer class="entry-footer">

				<div class="entry-author">
					<div class="entry-author--img">
						<img src="http://placekitten.com/g/40/40">
					</div>
					<div class="entry-author--meta">
						<div class="entry-author--name">Pierre Robert</div>
						<time datetime="2014" class="entry-date">12 September</time>
					</div>
				</div>

				<div class="entry-type">

					<div class="entry-type--standard">Rock News</div>

				</div>

				<div class="entry-comments">

					<div class="entry-comments--count">

						<a href="#comments">8</a>

					</div>

				</div>

			</footer>

		</article>

		<!-- Concert Content Type -->
		<article id="post-id" class="post type-post status-publish hentrycf" role="article" itemscope="" itemtype="http://schema.org/BlogPosting">

			<section class="entry-concert--thumbnail">

				<img src="http://placehold.it/600x400&text=concert">

			</section>

			<section class="entry-concert--content">

				<header class="entry-header">

					<h2 class="entry-title" itemprop="headline"><a href="#">Weezer</a></h2>

				</header>

				<section class="entry-content" itemprop="articleBody">

					Performing their newest album in it's entirety! Tickets on sale Saturday, 9/27 at 1pm

				</section>

				<section class="entry-concert--meta">

					<ul class="entry-concert--meta_list">
						<li class="entry-concert--meta_item">8pm</li>
						<li class="entry-concert--meta_item">The Trocadero Theatre</li>
						<li class="entry-concert--meta_item">$65 (+Fees)</li>
						<li class="entry-concert--meta_item">All Ages</li>
					</ul>

				</section>

			</section>

			<footer class="entry-footer">

				<div class="entry-author">
					<div class="entry-author--img">
						<img src="http://placeskull.com/40/40">
					</div>
					<div class="entry-author--meta">
						<div class="entry-author--name">Pierre Robert</div>
						<time datetime="2014" class="entry-date">12 September</time>
					</div>
				</div>

				<div class="entry-type">

					<div class="entry-type--standard">Rock News</div>

				</div>

				<div class="entry-comments">

					<div class="entry-comments--count">

						<a href="#comments">8</a>

					</div>

				</div>

			</footer>

		</article>

		<!-- CD Release Content Type -->
		<article id="post-id" class="post type-post status-publish hentrycf" role="article" itemscope="" itemtype="http://schema.org/BlogPosting">

			<section class="entry-cdrelease--thumbnail">

				<img src="http://placehold.it/600x600&text=Cd+Release">

			</section>

			<section class="entry-cdrelease--content">

				<header class="entry-header">

					<h2 class="entry-title" itemprop="headline"><a href="#">Alt. Mix Of “Rock And Roll” From ‘Led Zeppelin IV’ Reissue</a></h2>

				</header>

				<section class="entry-content" itemprop="articleBody">

					The October 28th release date on the reissues of Led Zeppelin IV and House of the Holy is fast approaching, but to tide us over, a new alternative mix of “Rock And Roll” has been released for fans to enjoy.

				</section>

			</section>

			<footer class="entry-footer">

				<div class="entry-author">
					<div class="entry-author--img">
						<img src="http://placebear.com/40/40">
					</div>
					<div class="entry-author--meta">
						<div class="entry-author--name">Pierre Robert</div>
						<time datetime="2014" class="entry-date">12 September</time>
					</div>
				</div>

				<div class="entry-type">

					<div class="entry-type--standard">Rock News</div>

				</div>

				<div class="entry-comments">

					<div class="entry-comments--count">

						<a href="#comments">8</a>

					</div>

				</div>

			</footer>

		</article>

		<!-- Standard Post -->
		<article id="post-id" class="post type-post status-publish hentrycf" role="article" itemscope="" itemtype="http://schema.org/BlogPosting">

			<section class="entry-standard--content">

				<header class="entry-header">

					<h2 class="entry-title" itemprop="headline"><a href="#">Royal Blood News Round-Up: ‘Late Night’ Performance, MTV EMAs and More!</a></h2>

				</header>

				<section class="entry-content" itemprop="articleBody">

					British duo Royal Blood are just about to wrap up their current set U.S tour dates, but before they leave to go back home for a series of dates in the U.K. and Ireland, they stopped by Late Night with Seth Meyers for a performance of their latest single “Figure It Out.”

				</section>

			</section>

			<section class="entry-standard--thumbnail">

				<img src="http://placehold.it/600x400&text=Standard">

			</section>

			<footer class="entry-footer">

				<div class="entry-author">
					<div class="entry-author--img">
						<img src="http://hhhhold.com/png/40">
					</div>
					<div class="entry-author--meta">
						<div class="entry-author--name">Pierre Robert</div>
						<time datetime="2014" class="entry-date">12 September</time>
					</div>
				</div>

				<div class="entry-type">

					<div class="entry-type--standard">Rock News</div>

				</div>

				<div class="entry-comments">

					<div class="entry-comments--count">

						<a href="#comments">8</a>

					</div>

				</div>

			</footer>

		</article>

		<!-- Video Post Format Full Width -->
		<article id="post-id" class="post type-post status-publish hentrycf" role="article" itemscope="" itemtype="http://schema.org/BlogPosting">

			<section class="entry--full_width">

				<section class="entry-content" itemprop="articleBody">

					<img src="http://placehold.it/2000x700&text=Video">

				</section>

				<header class="entry-header">

					<h2 class="entry-title" itemprop="headline"><a href="#">Royal Blood News Round-Up: ‘Late Night’ Performance, MTV EMAs and More!</a></h2>

				</header>

			</section>

			<footer class="entry-footer">

				<div class="entry-author">
					<div class="entry-author--img">
						<img src="http://placecreature.com/40/40">
					</div>
					<div class="entry-author--meta">
						<div class="entry-author--name">Pierre Robert</div>
						<time datetime="2014" class="entry-date">12 September</time>
					</div>
				</div>

				<div class="entry-type">

					<div class="entry-type--standard">Rock News</div>

				</div>

				<div class="entry-comments">

					<div class="entry-comments--count">

						<a href="#comments">8</a>

					</div>

				</div>

			</footer>

		</article>

		<!-- Standard Post -->
		<article id="post-id" class="post type-post status-publish hentrycf" role="article" itemscope="" itemtype="http://schema.org/BlogPosting">

			<section class="entry-standard--content">

				<header class="entry-header">

					<h2 class="entry-title" itemprop="headline"><a href="#">Ozzy Osbourne Talks New Solo Compilation ‘Memoirs of a Madman’</a></h2>

				</header>

				<section class="entry-content" itemprop="articleBody">

					Ozzy Osbourne says he wanted to put out his new compilation "Memoirs of a Madman" to remind fans he's a successful solo artist as well as a member of Black Sabbath.

				</section>

			</section>

			<section class="entry-standard--thumbnail">

				<img src="http://placehold.it/600x400&text=Standard">

			</section>

			<footer class="entry-footer">

				<div class="entry-author">
					<div class="entry-author--img">
						<img src="http://baconmockup.com/40/40">
					</div>
					<div class="entry-author--meta">
						<div class="entry-author--name">Pierre Robert</div>
						<time datetime="2014" class="entry-date">12 September</time>
					</div>
				</div>

				<div class="entry-type">

					<div class="entry-type--standard">Rock News</div>

				</div>

				<div class="entry-comments">

					<div class="entry-comments--count">

						<a href="#comments">8</a>

					</div>

				</div>

			</footer>

		</article>

		<!-- Video Post Format Full Width -->
		<article id="post-id" class="post type-post status-publish hentrycf" role="article" itemscope="" itemtype="http://schema.org/BlogPosting">

			<section class="entry--full_width">

				<header class="entry-header">

					<h2 class="entry-title" itemprop="headline"><a href="#">Royal Blood News Round-Up: ‘Late Night’ Performance, MTV EMAs and More!</a></h2>

				</header>

				<section class="entry-content" itemprop="articleBody">

					British duo Royal Blood are just about to wrap up their current set U.S tour dates, but before they leave to go back home for a series of dates in the U.K. and Ireland, they stopped by Late Night with Seth Meyers for a performance of their latest single “Figure It Out.”

				</section>

			</section>

			<footer class="entry-footer">

				<div class="entry-author">
					<div class="entry-author--img">
						<img src="http://place-hoff.com/40/40">
					</div>
					<div class="entry-author--meta">
						<div class="entry-author--name">Pierre Robert</div>
						<time datetime="2014" class="entry-date">12 September</time>
					</div>
				</div>

				<div class="entry-type">

					<div class="entry-type--standard">Rock News</div>

				</div>

				<div class="entry-comments">

					<div class="entry-comments--count">

						<a href="#comments">8</a>

					</div>

				</div>

			</footer>

		</article>
	</div>
</section>