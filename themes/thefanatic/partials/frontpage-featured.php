<?php
/**
 * Partial for the Front Page Featured Content
 *
 * @package Greater Media
 * @since   0.1.0
 */
?>
<section id="featured" class="home__featured">
		<?php
		$hp_featured_query = \GreaterMedia\HomepageCuration\get_featured_query(); ?>

		<div class="featured__articles">
			<div class="featured__article--primary">
				<div class="featured__article">
					<a href="#<?php /* the_permalink(); */?>" class="featured__article--link">
						<div class="featured__article--image" style='background-image: url(http://lorempixel.com/900/550/sports/1)'>
							<?php /*

								$image_attr = image_attribution();

								if ( ! empty( $image_attr ) ) {
									echo $image_attr;
								} */

							?>
						</div>
						<div class="featured__article--content">
							<div class="featured__article--heading">
								What Chris Christie might talk about in 2015 State of the State speech today.
								<?php /* the_title(); */ ?>
							</div>
						</div>
					</a>
				</div>
			</div>
			<div class="featured__article--secondary">
				<div class="featured__article">
					<a href="#<?php /* the_permalink(); */?>" class="featured__article--link">
						<div class="featured__article--image" style='background-image: url(http://lorempixel.com/450/310/people/1)'>
							<?php /*

										$image_attr = image_attribution();

										if ( ! empty( $image_attr ) ) {
											echo $image_attr;
										} */

							?>
						</div>
						<div class="featured__article--content">
							<div class="featured__article--heading">
								Vigil honors 4 shot dead in Hayes Valley
								<?php /* the_title(); */ ?>
							</div>
						</div>
					</a>
				</div>
				<div class="featured__article">
					<a href="#<?php /* the_permalink(); */?>" class="featured__article--link">
						<div class="featured__article--image" style='background-image: url(http://lorempixel.com/450/310/nature/1)'>
							<?php /*

										$image_attr = image_attribution();

										if ( ! empty( $image_attr ) ) {
											echo $image_attr;
										} */

							?>
						</div>
						<div class="featured__article--content">
							<div class="featured__article--heading">
								Record spell of Spare the Air days as dirty Bay Area skies linger
								<?php /* the_title(); */ ?>
							</div>
						</div>
					</a>
				</div>
			</div>
		</div>
		<?php // if we still have more posts (we almost always will), render the 3 below the main section ?>
			<div class="featured__content">
					<div class="featured__content--block">
						<a href="#<?php /* the_permalink(); */?>" class="featured__content--link">
							<div class="featured__content--image" style='background-image: url(http://lorempixel.com/450/310/animals/1)'></div>
							<div class="featured__content--meta">
								<h2 class="featured__content--title">Video Shows Tale of Horse's Rescue from Florida Sinkhole<?php /* the_title(); */ ?></h2>
							</div>
						</a>
					</div>
					<div class="featured__content--block">
						<a href="#<?php /* the_permalink(); */?>" class="featured__content--link">
							<div class="featured__content--image" style='background-image: url(http://lorempixel.com/450/310/transport/1)'></div>
							<div class="featured__content--meta">
								<h2 class="featured__content--title">United Airlines Passengers Endure Nightmare Layover<?php /* the_title(); */ ?></h2>
							</div>
						</a>
					</div>
					<div class="featured__content--block">
						<a href="#<?php /* the_permalink(); */?>" class="featured__content--link">
							<div class="featured__content--image" style='background-image: url(http://lorempixel.com/450/310/nightlife/1)'></div>
							<div class="featured__content--meta">
								<h2 class="featured__content--title">WBT Presents Another Holiday On Ice!<?php /* the_title(); */ ?></h2>
							</div>
						</a>
					</div>
			</div>
</section>