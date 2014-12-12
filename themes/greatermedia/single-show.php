<?php

 get_header();

?>

	<main class="main" role="main">

		<div class="container">

			<?php the_post(); ?>

				<?php get_template_part( 'show-header' ); ?>

				<section class="content">

					<section class="show__features">
						<div class="show__feature--primary">
							<a href=""><div class="show__feature">
								<img src="http://placehold.it/570x315&text=show-feature" alt="">
								<div class="show__feature--desc">
									<h3>The Title of the Primary Featured Post on the Show Homepage</h3>
									<time class="show__feature--date" datetime="">23 SEP</time>
								</div>
							</div></a>
						</div>
						<div class="show__feature--secondary">
							<a href=""><div class="show__feature">
								<img src="http://placehold.it/570x315&text=show-feature" alt="">
								<div class="show__feature--desc">
									<h3>The Title of a Secondary Featured Post on the Show Homepage</h3>
									<time class="show__feature--date" datetime="">23 SEP</time>
								</div>
							</div></a>
							<a href=""><div class="show__feature">
								<img src="http://placehold.it/570x315&text=show-feature" alt="">
								<div class="show__feature--desc">
									<h3>The Title of a Secondary Featured Post on the Show Homepage</h3>
									<time class="show__feature--date" datetime="">23 SEP</time>
								</div>
							</div></a>
						</div>
					</section>

					<div class="featured__content">
			            <div class="featured__content--block">
			                <div class="featured__content--image">
			                    <img src="http://placehold.it/400x400&text=featured+image">
			                </div>
			                <div class="featured__content--meta">
			                    <h3 class="featured__content--title">MMR Rocks the Flyers</h3>
			                </div>
			            </div>
			            <div class="featured__content--block">
			                <div class="featured__content--image">
			                    <img src="http://placehold.it/400x400&text=featured+image">
			                </div>
			                <div class="featured__content--meta">
			                    <h3 class="featured__content--title">Hitch a Ride with Pierre ...and Minerva</h3>
			                </div>
			            </div>
			            <div class="featured__content--block">
			                <div class="featured__content--image">
			                    <img src="http://placehold.it/400x400&text=featured+image">
			                </div>
			                <div class="featured__content--meta">
			                    <h3 class="featured__content--title">Preston and Steve</h3>
			                </div>
			            </div>
			        </div>

			        <div class="row">

				        <aside class="inner-right-col">
					        <section class="show__favorites">
					        	<h2>Our Favorites</h2>
								<div class="featured__content--block">
					                <div class="featured__content--image">
					                    <img src="http://placehold.it/400x400&text=featured+image">
					                </div>
					                <div class="featured__content--meta">
					                    <h3 class="featured__content--title">MMR Rocks the Flyers</h3>
					                </div>
					            </div>
					            <div class="featured__content--block">
					                <div class="featured__content--image">
					                    <img src="http://placehold.it/400x400&text=featured+image">
					                </div>
					                <div class="featured__content--meta">
					                    <h3 class="featured__content--title">Hitch a Ride with Pierre ...and Minerva</h3>
					                </div>
					            </div>
					            <div class="featured__content--block">
					                <div class="featured__content--image">
					                    <img src="http://placehold.it/400x400&text=featured+image">
					                </div>
					                <div class="featured__content--meta">
					                    <h3 class="featured__content--title">Preston and Steve</h3>
					                </div>
					            </div>
					        </section>

						<section class="show__latest-crap">
				        	<h2>Latest Crap</h2>
							<ul>
								<li class="live-link__type--standard">
									<div class="live-link__title">
										<a href=
										"http://wmmr.greatermedia.10uplabs.com/scroll-test/">GMR-278
										test</a>
									</div>
								</li>


								<li class="live-link__type--standard">
									<div class="live-link__title">
										<a href=
										"http://wmmr.greatermedia.10uplabs.com/contest/daves-contest/">Dave’s
										Contest</a>
									</div>
								</li>

								<li class="live-link__type--link">
									<div class="live-link__title">
										<a href=
										"http://wmmr.greatermedia.10uplabs.com/timed-content-test/">test
										test</a>
									</div>
								</li>


								<li class="live-link__type--standard">
									<div class="live-link__title">
										<a href=
										"http://wmmr.greatermedia.10uplabs.com/ontario-highway-401-wikipedia-the-free-encyclopedia/">
										Ontario Highway 401 – Wikipedia, the free encyclopedia</a>
									</div>
								</li>


								<li class="live-link__type--video">
									<div class="live-link__title">
										<a href=
										"http://wmmr.greatermedia.10uplabs.com/?post_type=gmr-live-link&amp;p=633">
										Gladys Knight &amp; The Pips “Midnight Train To Georgia”</a>
									</div>
								</li>


								<li class="live-link__type--video">
									<div class="live-link__title">
										<a href=
										"http://wmmr.greatermedia.10uplabs.com/gladys-knight-the-pips-midnight-train-to-georgia/">
										Gladys Knight &amp; The Pips</a>
									</div>
								</li>


								<li class="live-link__type--video">
									<div class="live-link__title">
										<a href=
										"http://wmmr.greatermedia.10uplabs.com/?post_type=gmr-live-link&amp;p=538">
										Hilarious Volleyball Triple Head Shot – YouTube</a>
									</div>
								</li>
							</ul>
				        </section>

				        </aside>

				        <section class="show__blogroll inner-left-col">
				        	<h2>Blog</h2>
							<article class=
							"post-1241 post type-post status-publish format-standard has-post-thumbnail sticky hentry category-uncategorized tag-sticky-2 tag-template cf"
							id="post-1241">
								<section class="entry__meta">
									<time class="entry__date" datetime="2012-01-07T07:07:21+00:00">7
									January</time>

									<h2 class="entry__title"><a href=
									"http://greatermedia.dev/template-sticky/">Template: Sticky</a>
									</h2>
								</section>


								<section class="entry__thumbnail entry__thumbnail--standard">
									<a href="http://greatermedia.dev/template-sticky/"><img alt=
									"Horizontal Featured Image" class=
									"attachment-gm-article-thumbnail wp-post-image" height="300" src=
									"http://greatermedia.dev/wp-content/uploads/2013/03/featured-image-horizontal.jpg"
									width="580"></a>
								</section>


								<footer class="entry__footer">
									<a class="entry__footer--category" href=
									"http://greatermedia.dev/category/uncategorized/">Uncategorized</a>
								</footer>
							</article>


							<article class=
							"post-1850 post type-post status-publish format-gallery hentry category-uncategorized cf"
							id="post-1850">
								<section class="entry__meta--fullwidth">
									<time class="entry__date" datetime="2014-12-09T05:26:47+00:00">9
									December</time>

									<h2 class="entry__title"><a href=
									"http://greatermedia.dev/gallery-test/">Gallery Test</a>
									</h2>
								</section>


								<footer class="entry__footer">
									<a class="entry__footer--category" href=
									"http://greatermedia.dev/category/uncategorized/">Uncategorized</a>
								</footer>
							</article>


							<article class="post-19 episode type-episode status-publish hentry cf" id=
							"post-19">
								<section class="entry__meta--fullwidth">
									<time class="entry__date" datetime="2014-09-29T12:53:39+00:00">29
									September</time>

									<h2 class="entry__title"><a href=
									"http://greatermedia.dev/episode/test-episode/">Test Episode</a>
									</h2>
								</section>


								<footer class="entry__footer">
								</footer>
							</article>


							<article class=
							"post-1 post type-post status-publish format-standard hentry category-uncategorized cf"
							id="post-1">
								<section class="entry__meta--fullwidth">
									<time class="entry__date" datetime="2014-09-18T13:24:09+00:00">18
									September</time>

									<h2 class="entry__title"><a href=
									"http://greatermedia.dev/hello-world/">Hello world!</a>
									</h2>
								</section>


								<footer class="entry__footer">
									<a class="entry__footer--category" href=
									"http://greatermedia.dev/category/uncategorized/">Uncategorized</a>
								</footer>
							</article>
				        </section>

			        </div>

			</section>

		</div>

	</main>

<?php get_footer();